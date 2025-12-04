<?php

namespace Wpshop\AbcPagination\Support;

use MyThemeShop\Helpers\Str;
use MyThemeShop\Helpers\Url;
use RankMath\Paper\Taxonomy;
use WP_Query;
use Wpshop\AbcPagination\VirtualTax;
use function Wpshop\AbcPagination\is_plugin_active;

/**
 * Add support for Rank Math SEO
 *
 * Up to v1.0.110
 */
class RankMathSeoSupport {

    /**
     * @var VirtualTax
     */
    protected $virtual_tax;

    /**
     * @var Taxonomy|null
     */
    protected $_paper;

    /**
     * @param VirtualTax $virtual_tax
     */
    public function __construct( VirtualTax $virtual_tax ) {
        $this->virtual_tax = $virtual_tax;
    }

    /**
     * @return void
     */
    public function init() {
        if ( ! defined( 'RANK_MATH_FILE' ) || ! is_plugin_active( plugin_basename( RANK_MATH_FILE ) ) ) {
            return;
        }

        add_filter( 'abc_pagination/virtual_tax/replace_queried_object', [ $this, '_replace_queried_object' ], 10, 2 );

        $wrap_global_wp_query = function ( $cb ) {
            return function ( $result ) use ( $cb ) {
                if ( is_admin() ) {
                    return $result;
                }

                if ( ! $this->virtual_tax->is_applied() ) {
                    return $result;
                }

                global $wp_query;

                $orig_object    = $wp_query->queried_object;
                $orig_object_id = $wp_query->queried_object_id;

                $wp_query->queried_object    = $this->virtual_tax->get_wp_query_dump()->get_queried_object();
                $wp_query->queried_object_id = $this->virtual_tax->get_wp_query_dump()->get_queried_object_id();
                $wp_query->tax_query         = $this->virtual_tax->get_wp_query_dump()->tax_query;

                $result = call_user_func_array( $cb, func_get_args() );

                $wp_query->queried_object    = $orig_object;
                $wp_query->queried_object_id = $orig_object_id;

                return $result;
            };
        };

        add_filter( 'rank_math/frontend/title', $wrap_global_wp_query( function () {
            return $this->get_paper()->title();
        } ) );

        add_filter( 'rank_math/frontend/description', $wrap_global_wp_query( function ( $result ) {
            add_filter( 'abc_pagination/shortcode/do_output', $do_output_cb = function () {
                return false;
            }, 11 );
            $result = $this->get_paper()->description();
            remove_filter( 'abc_pagination/shortcode/do_output', $do_output_cb, 11 );

            return $result;
        } ) );

        add_filter( 'rank_math/frontend/keywords', $wrap_global_wp_query( function () {
            return $this->get_paper()->keywords();
        } ) );

        add_filter( 'rank_math/frontend/robots', $wrap_global_wp_query( function () {
            return $this->get_paper()->robots();
        } ) );

        add_filter( 'rank_math/frontend/canonical', $wrap_global_wp_query( function () {
            $vars = wp_parse_args( $this->get_paper()->canonical(), [
                'canonical'          => false,
                'canonical_unpaged'  => false,
                'canonical_override' => false,
            ] );

            extract( $vars );

            $canonical = Str::is_non_empty( $canonical ) && true === Url::is_relative( $canonical ) ? $this->base_url( $canonical ) : $canonical;
            $canonical = Str::is_non_empty( $canonical_override ) ? $canonical_override : $canonical;

            return $canonical;
        } ) );

//        add_filter( 'rank_math/paper/is_valid/taxonomy', function () {
//            return $this->virtual_category->is_applied();
//        } );
    }

    /**
     * @param bool     $result
     * @param WP_Query $wp_query
     *
     * @return bool
     */
    public function _replace_queried_object( $result, $wp_query ) {
        if ( $wp_query->is_tag() ) {
            return true;
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function base_url( $path = null ) {
        $parts    = wp_parse_url( get_option( 'home' ) );
        $base_url = trailingslashit( $parts['scheme'] . '://' . $parts['host'] );

        if ( ! is_null( $path ) ) {
            $base_url .= ltrim( $path, '/' );
        }

        return $base_url;
    }

    /**
     * @return Taxonomy
     */
    protected function get_paper() {
        if ( ! $this->_paper ) {
            $this->_paper = new Taxonomy();
        }

        return $this->_paper;
    }
}

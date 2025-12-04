<?php

namespace Wpshop\AbcPagination\Support;

use WP_Query;
use Wpshop\AbcPagination\VirtualTax;
use Yoast\WP\SEO\Memoizers\Meta_Tags_Context_Memoizer;
use function Wpshop\AbcPagination\is_plugin_active;

/**
 * Add support for Yoast SEO
 *
 * Up to v20.2.1
 */
class YoastSeoSupport {

    /**
     * @var VirtualTax
     */
    protected $virtual_tax;

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
        if ( ! defined( 'WPSEO_BASENAME' ) || ! is_plugin_active( WPSEO_BASENAME ) ) {
            return;
        }

        add_action( 'abc_pagination/virtual_tax/before_setup', function () {
            YoastSEO()->classes->get( Meta_Tags_Context_Memoizer::class )->for_current_page();
        } );

        add_filter( 'abc_pagination/shortcode/do_output', [ $this, '_do_output_in_content_only' ] );
        add_filter( 'abc_pagination/virtual_tax/replace_queried_object', [ $this, '_replace_queried_object' ], 10, 2 );
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
     * Prevent shortcode output out of `the_content` filter
     *
     * @return bool
     */
    public function _do_output_in_content_only( $do_output ) {
        if ( $this->virtual_tax->is_applied() && $do_output ) {
            return doing_filter( 'the_content' );
        }

        return $do_output;
    }
}

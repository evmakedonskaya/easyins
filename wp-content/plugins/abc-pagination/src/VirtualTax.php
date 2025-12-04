<?php

namespace Wpshop\AbcPagination;

use WP;
use WP_Admin_Bar;
use WP_Post;
use WP_Query;
use WP_Term;
use Wpshop\AbcPagination\Admin\Settings;

class VirtualTax {

    /**
     * @var \stdClass
     */
    protected $post;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var bool
     */
    protected $is_applied = false;

    /**
     * @var WP_Term|null
     */
    protected $stashed_term;

    /**
     * @var WP_Query|null
     */
    protected $wp_query_dump;

    /**
     * Constructor
     */
    public function __construct( Settings $settings ) {
        $this->settings = $settings;

        $this->post = new \stdClass();

        $this->post->ID          = - 1;
        $this->post->post_author = 1;

        $this->post->post_title   = null;
        $this->post->post_content = null;
        $this->post->post_name    = null;

        $this->post->post_date             = current_time( 'mysql' );
        $this->post->post_date_gmt         = current_time( 'mysql', 1 );
        $this->post->post_excerpt          = '';
        $this->post->post_status           = 'publish';
        $this->post->ping_status           = 'closed';
        $this->post->post_password         = '';
        $this->post->to_ping               = '';
        $this->post->pinged                = '';
        $this->post->modified              = $this->post->post_date;
        $this->post->modified_gmt          = $this->post->post_date_gmt;
        $this->post->post_content_filtered = '';
        $this->post->post_parent           = 0;
        $this->post->guid                  = get_home_url( '/' . $this->post->post_name ); // use url instead?
        $this->post->menu_order            = 0;
        $this->post->post_type             = 'page';
        $this->post->post_mime_type        = '';
        $this->post->comment_status        = 'closed';
        $this->post->comment_count         = 0;
        $this->post->filter                = 'raw';
        $this->post->ancestors             = [];

        add_action( 'parse_request', [ $this, '_parse_request' ] );
    }

    /**
     * @return void
     */
    public function init() {
        add_filter( 'abc_pagination/virtual_tax/enabled', function () {
            return $this->settings->get_value( 'virtual_category_enabled' );
        } );
    }

    /**
     * @return bool
     */
    public function is_applied() {
        return $this->is_applied;
    }

    /**
     * @return WP_Term|null
     */
    public function get_stashed_term() {
        return $this->stashed_term;
    }

    /**
     * @return WP_Query|null
     */
    public function get_wp_query_dump() {
        return $this->wp_query_dump;
    }

    /**
     * @param WP $wp
     *
     * @return void
     */
    public function _parse_request( $wp ) {
        // legacy hook
        if ( ! apply_filters( 'abc_pagination/virtual_category/enabled', true ) ) {
            return;
        }

        if ( ! apply_filters( 'abc_pagination/virtual_tax/enabled', true, $wp ) ) {
            return;
        }

        [ $term, $taxonomy ] = $this->find_term( $wp );
        if ( $term ) {
            $content = get_term_field( 'description', $term, $taxonomy, 'raw' );
            if ( has_shortcode( $content, 'abc_pagination' ) ) {
                $this->post->post_title   = $term->name;
                $this->post->post_content = $content;
                $this->post->post_name    = $term->slug;

                add_filter( 'the_posts', [ $this, '_create_dummy_post' ], 10, 2 );
                add_action( 'template_redirect', [ $this, '_template_redirect' ] );
                add_filter( 'pre_get_document_title', [ $this, '_set_default_title' ] );

                $this->is_applied = true;

                $this->admin_edit_menu( $term, $taxonomy );
            }
        }
    }

    /**
     * @param WP $wp
     *
     * @return array
     */
    protected function find_term( $wp ) {
        if ( array_key_exists( 'tag', $wp->query_vars ) ) {
            return [ get_term_by( 'slug', $wp->query_vars['tag'], 'post_tag' ), 'post_tag' ];
        }

        if ( array_key_exists( 'category_name', $wp->query_vars ) && empty( $wp->query_vars['name'] ) ) {
            $categories = $wp->query_vars['category_name'];
            $categories = explode( '/', $categories );
            if ( ! ( $cat = end( $categories ) ) ) {
                return [ null, null ];
            }

            return [ get_term_by( 'slug', $cat, 'category' ), 'category' ];
        }

        return [ null, null ];
    }

    /**
     * @return string
     */
    public function _set_default_title() {
        return $this->get_stashed_term()->name;
    }

    /**
     * @param WP_Term $term
     *
     * @return void
     * @see wp_admin_bar_edit_menu()
     */
    protected function admin_edit_menu( $term, $taxonomy ) {
        add_action( 'admin_bar_menu', function ( $wp_admin_bar ) use ( $term, $taxonomy ) {
            if ( is_admin() ) {
                return;
            }
            /** @var WP_Admin_Bar $wp_admin_bar */
            $tax            = get_taxonomy( $term->taxonomy );
            $edit_term_link = get_edit_term_link( $term->term_id, $taxonomy );
            if ( $edit_term_link && current_user_can( 'edit_term', $term->term_id ) ) {
                $wp_admin_bar->add_node(
                    [
                        'id'    => 'edit',
                        'title' => $tax->labels->edit_item,
                        'href'  => $edit_term_link,
                        'meta'  => [
                            'title' => __( 'Replaced by ABC Pagination', 'abc-pagination' ),
                        ],
                    ]
                );
            }
        }, 81 );
    }

    /**
     * @return void
     */
    public function _template_redirect() {
        global $post;
        $post = $this->post;

        if ( file_exists( STYLESHEETPATH . "/page.php" ) ) {
            include( STYLESHEETPATH . "/page.php" ); // child
            exit;
        }
        if ( file_exists( TEMPLATEPATH . "/page.php" ) ) {
            include( TEMPLATEPATH . "/page.php" );
            exit;
        }

        trigger_error( 'Unable to locate page.php', E_USER_ERROR );
    }

    /**
     * @param WP_Post[] $posts
     * @param WP_Query  $wp_query_orig
     *
     * @return mixed|\stdClass[]
     */
    public function _create_dummy_post( $posts, $wp_query_orig ) {
        global $wp_query;

        if ( spl_object_hash( $wp_query ) != spl_object_hash( $wp_query_orig ) ) {
            return $posts;
        }

        $this->wp_query_dump = clone $wp_query_orig;

        $this->stashed_term = $wp_query_orig->get_queried_object();

        do_action( 'abc_pagination/virtual_tax/before_setup', $wp_query_orig );

        // reset wp_query properties to simulate a found page
//        $wp_query->is_page     = true;
//        $wp_query->is_singular = true;
        $wp_query_orig->is_home     = false;
        $wp_query_orig->is_archive  = false;
        $wp_query_orig->is_category = false;
        unset( $wp_query_orig->query['error'] );
//        $wp->query                     = [];
        $wp_query_orig->query_vars['error'] = '';
        $wp_query_orig->is_404              = false;
//
//        $wp_query->current_post  = -1;
        $wp_query_orig->found_posts   = 1;
        $wp_query_orig->post_count    = 1;
        $wp_query_orig->comment_count = 0;
//        // -1 for current_comment displays comment if not logged in!
        $wp_query_orig->current_comment = null;
//
        $wp_query_orig->post              = $this->post;
        $wp_query_orig->posts             = [ $this->post ];
        $wp_query_orig->queried_object    = $this->post;
        $wp_query_orig->queried_object_id = $this->post->ID;

        add_action( 'wp', function () use ( $wp_query_orig ) {
            if ( ! apply_filters( 'abc_pagination/virtual_tax/replace_queried_object', false, $wp_query_orig ) ) {
                return;
            }

            $wp_query_orig->queried_object    = $this->get_wp_query_dump()->get_queried_object();
            $wp_query_orig->queried_object_id = $this->get_wp_query_dump()->get_queried_object_id();
            $wp_query_orig->tax_query         = $this->get_wp_query_dump()->tax_query;

        }, PHP_INT_MIN + 1 );

        $posts = [ $this->post ];

        return $posts;
    }
}

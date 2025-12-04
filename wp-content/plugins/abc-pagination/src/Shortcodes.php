<?php

namespace Wpshop\AbcPagination;

use Wpshop\AbcPagination\Admin\Settings;

class Shortcodes {

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var int
     */
    protected static $anchor_counter = - 1;

    protected $cache = [];

    /**
     * @param Settings $settings
     */
    public function __construct( Settings $settings ) {
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    public function init() {
        add_shortcode( 'abc_pagination', [ $this, '_shortcode' ] );

        add_filter( 'abc_pagination/shortcode/do_output', [ $this, '_hide_disabled_glossary' ], 10, 2 );
        add_filter( 'abc_pagination/shortcode/atts', [ $this, '_filter_disabled_glossary' ] );
    }

    /**
     * @param bool  $result
     * @param array $atts
     *
     * @return false
     */
    public function _hide_disabled_glossary( $result, $atts ) {
        if ( ! $this->settings->get_value( 'enable_glossary' ) &&
             $atts['post_type'] === 'glossary'
        ) {
            return false;
        }

        return $result;
    }

    /**
     * @param array $atts
     *
     * @return array
     */
    public function _filter_disabled_glossary( $atts ) {
        if ( ! $this->settings->get_value( 'enable_glossary' ) ) {
            $types = wp_parse_list( $atts['post_type'] );
            $types = array_filter( $types, function ( $type ) {
                return $type !== 'glossary';
            } );

            $atts['post_type'] = is_array( $atts['post_type'] ) ? $types : implode( ',', $types );
        }

        return $atts;
    }

    /**
     * Setup placeholder of counter for anchor
     *
     * @param string $letter
     * @param string $prefix
     *
     * @return string
     */
    public static function anchor( $letter, $prefix = 'letter_' ) {
        $anchor = $prefix . $letter;
        $anchor .= '{{%d}}';

        return $anchor;
    }

    /**
     * Replace anchor counter placeholders with actual counter value
     *
     * @param string $content
     *
     * @return string
     */
    protected static function prepare_anchor( $content ) {
        $anchor_suffix = '';
        if ( self::$anchor_counter ) {
            $anchor_suffix = '-' . self::$anchor_counter;
        }

        return str_replace( '{{%d}}', $anchor_suffix, $content );
    }

    /**
     * @param array  $atts
     * @param string $content
     * @param string $tag
     *
     * @return string
     * @throws \Exception
     */
    public function _shortcode( $atts, $content, $tag ) {
        if ( is_admin() && ! wp_doing_ajax() ) {
            return '[ abc_pagination ]';
        }

        /**
         * Атрибуты, которые можно передать в WP_Query
         */
        $wp_query_args = [
            'category__and',
            'category__in',
            'category__not_in',

            'tag_id',
            'tag__and',
            'tag__in',
            'tag__not_in',
            'tag_slug__and',
            'tag_slug__in',

            'author_name',
            'author__in',
            'author__not_in',

            'p',
            'name',
            'title',
            'page_id',
            'pagename',
            // 'post_parent', есть в нижнем
            'post_parent__in',
            'post_parent__not_in',
            'post__in',
            'post__not_in',
            'post_name__in',

            'ignore_sticky_posts',

            'meta_key',
            'meta_compare_key',
            'meta_type_key',
            'meta_value',
            'meta_type',
            'meta_value_num',
            'meta_compare',

            'post_status',

            'year',
            'monthnum',
            'w',
            'day',
            'hour',
            'minute',
            'second',
            'm',

            'comment_status',
            'comment_count',

            's',
        ];

        $context = Context::createFromWpQuery();

        $original_atts = $atts;

        $pairs = [
            // For posts query
            'count'                    => 1000,
            'cat'                      => '',
            'category_name'            => '',
            'tag'                      => '',
            'author'                   => '',
            'post_type'                => 'post',
            'exclude'                  => '',
            'include'                  => '',
            'tax_query'                => '', // example: taxonomy=people&field=slug&terms=bob
            'post_parent'              => '',

            // атрибуты таксономий
            'taxonomy'                 => '',
            'exclude_tree'             => '',
            'hide_empty'               => false,
            'child_of'                 => '',
            'parent'                   => '',


            // Settings > Letter list
            'show_letters'             => $this->settings->get_value( 'show_letters', true ),
            'show_counts'              => $this->settings->get_value( 'show_counts', true ),
            'show_search'              => $this->settings->get_value( 'show_search' ),
            'alphabet'                 => $this->settings->get_value( 'alphabet' ),

            // Settings > Posts list
            'card_type'                => 'default',
            'type'                     => $this->settings->get_value( 'type' ),
            'show_tab_letter'          => $this->settings->get_value( 'show_tab_letter' ),
            'show_post_link'           => $this->settings->get_value( 'show_post_link' ),
            'show_post_thumb'          => $this->settings->get_value( 'show_post_thumb' ),
            'show_post_excerpt'        => $this->settings->get_value( 'show_post_excerpt' ),
            'show_post_content'        => $this->settings->get_value( 'show_post_content' ),
            'posts_short_title'        => $this->settings->get_value( 'posts_short_title' ),
            'show_posts_limit'         => max( - 1, $this->settings->get_value( 'show_posts_limit' ) ),

            // Appearance > Letters
            'letters_text_before'      => '',
            'letters_text_after'       => '',
            'letters_gap'              => $this->settings->get_value( 'letters_gap', true ),
            'letters_padding'          => $this->settings->get_value( 'letters_padding', true ),
            'letters_background'       => $this->settings->get_value( 'letters_background', true ),
            'letters_justify_content'  => $this->settings->get_value( 'letters_justify_content', true ),
            'letters_border_radius'    => $this->settings->get_value( 'letters_border_radius', true ),

            // Appearance > Letter
            'letter_padding'           => $this->settings->get_value( 'letter_padding', true ),
            'letter_color'             => $this->settings->get_value( 'letter_color', true ),
            'letter_background'        => $this->settings->get_value( 'letter_background' ),
            'letter_hover_color'       => $this->settings->get_value( 'letter_hover_color', true ),
            'letter_hover_background'  => $this->settings->get_value( 'letter_hover_background', true ),
            'letter_hover_effect'      => $this->settings->get_value( 'letter_hover_effect' ),
            'letter_border_width'      => $this->settings->get_value( 'letter_border_width' ),
            'letter_border_style'      => $this->settings->get_value( 'letter_border_style' ),
            'letter_border_color'      => $this->settings->get_value( 'letter_border_color' ),
            'letter_font_weight'       => $this->settings->get_value( 'letter_font_weight', true ),
            'letter_font_size'         => $this->settings->get_value( 'letter_font_size', true ),
            'letter_border_radius'     => $this->settings->get_value( 'letter_border_radius', true ),

            // Appearance > Tab letter
            'tab_letter_width'         => $this->settings->get_value( 'tab_letter_width', true ),
            'tab_letter_margin'        => $this->settings->get_value( 'tab_letter_margin', true ),
            'tab_letter_padding'       => $this->settings->get_value( 'tab_letter_padding', true ),
            'tab_letter_font_weight'   => $this->settings->get_value( 'tab_letter_font_weight', true ),
            'tab_letter_font_size'     => $this->settings->get_value( 'tab_letter_font_size', true ),
            'tab_letter_text_align'    => $this->settings->get_value( 'tab_letter_text_align', true ),
            'tab_letter_color'         => $this->settings->get_value( 'tab_letter_color', true ),
            'tab_letter_background'    => $this->settings->get_value( 'tab_letter_background', true ),
            'tab_letter_border_radius' => $this->settings->get_value( 'tab_letter_border_radius', true ),

            // Appearance > Posts
            'posts_image_height'       => $this->settings->get_value( 'posts_image_height', true ),
            'posts_columns'            => $this->settings->get_value( 'posts_columns', true ),
            'posts_columns_mobile'     => $this->settings->get_value( 'posts_columns_mobile', true ),
            'posts_gap'                => $this->settings->get_value( 'posts_gap', true ),
            'posts_title_font_weight'  => $this->settings->get_value( 'posts_title_font_weight', true ),
            'posts_show_id'            => 0,

            // Name
            'name'                     => '',
            'ajax'                     => 0,

        ];

        $pairs = array_merge( $pairs, array_fill_keys( $wp_query_args, '' ) );
        $atts = shortcode_atts( $pairs, $atts, $tag );

        $atts['show_posts_limit'] ++;

        if ( ! $this->settings->verify() || ! apply_filters( 'abc_pagination/shortcode/do_output', true, $atts, $context ) ) {
            return '';
        }

        $atts = apply_filters( 'abc_pagination/shortcode/atts', $atts, $context );

        if ( ! $atts['post_type'] ) {
            return '';
        }

        if ( $atts['ajax'] && apply_filters( 'abc_pagination/shortcode/do_output_ajax', true ) ) {
            return '<span data-params="' . base64_encode( json_encode( $original_atts ) ) . '" class="js-abc-pagination-ajax"></span>';
        }

        $post_types = wp_parse_list( $atts['post_type'] );

        $exclude = wp_parse_list( $atts['exclude'] );
        if ( $context->is_object_type( Context::OBJ_TYPE_POST ) && in_array( $context->get_object_subtype(), $post_types ) ) {
            $exclude[] = $context->get_object_id();
        }
        $exclude = array_unique( $exclude );

        if ( ! in_array( $atts['type'], [ VIEW_TYPE_LIST, VIEW_TYPE_TABS, VIEW_TYPE_POPUP ] ) ) {
            $atts['type'] = VIEW_TYPE_LIST;
        }

        $args = [
            'numberposts'      => $atts['count'],
            'post_type'        => $post_types,
            'suppress_filters' => true,
            'cat'              => $atts['cat'],
            'category_name'    => $atts['category_name'],
            'tag'              => $atts['tag'],
            'author'           => $atts['author'],
            'exclude'          => $exclude ? implode( ',', $exclude ) : '',
            'include'          => $atts['include'],
            'post_parent'      => $atts['post_parent'],
        ];
        if ( $tax_query = wp_parse_args( html_entity_decode( $atts['tax_query'] ) ) ) {
            $args['tax_query'] = [ $tax_query ];
        }

        // добавляем атрибуты WP_Query
        foreach ( $wp_query_args as $key ) {
            if ( ! empty( $atts[ $key ] ) ) {

                // для некоторых аргументов парсим значения через запятую чтобы сделать массив 12,18
                $parse_keys = [
                    'category__and',
                    'category__in',
                    'category__not_in',

                    'tag__and',
                    'tag__in',
                    'tag__not_in',
                    'tag_slug__and',
                    'tag_slug__in',

                    'author__in',
                    'author__not_in',

                    'post_parent__in',
                    'post_parent__not_in',
                    'post__in',
                    'post__not_in',
                    'post_name__in',

                    'post_status',
                ];

                // если есть запятая и аргумент может содержать массив
                if ( in_array( $key, $parse_keys ) && strpos( $atts[ $key ], ',' ) !== false ) {
                    $args[ $key ] = wp_parse_list( $atts[ $key ] );
                } else {
                    $args[ $key ] = $atts[ $key ];
                }
            }
        }

        $args = apply_filters( 'abc_pagination/get_posts/args', $args, $atts, $context );

        $cache_key = $this->get_cache_key( $context, $atts, $args );

        self::$anchor_counter ++;

        if ( null !== $cache_key && array_key_exists( $cache_key, $this->cache ) ) {
            return static::prepare_anchor( $this->cache[ $cache_key ] );
        }

        $items = [];

        if ( ! empty( $atts['taxonomy'] ) && taxonomy_exists( $atts['taxonomy'] ) ) {
            $terms_args = [
                'taxonomy'     => $atts['taxonomy'],
                'number'       => $atts['count'],
                'include'      => $atts['include'],
                'exclude'      => $atts['exclude'],
                'exclude_tree' => $atts['exclude_tree'],
                'hide_empty'   => $atts['hide_empty'],
                'child_of'     => $atts['child_of'],
                'parent'       => $atts['parent'],
                'meta_key'     => $atts['meta_key'],
                'meta_value'   => $atts['meta_value'],
            ];

            $terms_args = apply_filters( 'abc_pagination/get_terms/args', $terms_args, $atts, $context );

            $terms = get_terms( $terms_args );

            foreach ( $terms as $term ) {
                $items[] = [
                    'title'   => $term->name,
                    'slug'    => $term->slug,
                    'id'      => $term->term_id,
                    'type'    => 'taxonomy',
                    'url'     => get_term_link( $term ),
                    'excerpt' => '',
                    'content' => $term->description,
                    'object'  => $term,
                ];
            }

        } else {
            $posts = get_posts( $args );

            foreach ( $posts as $post ) {
                $items[] = [
                    'title'   => $post->post_title,
                    'slug'    => $post->post_name,
                    'id'      => $post->ID,
                    'type'    => $post->post_type,
                    'url'     => get_permalink( $post ),
                    'excerpt' => get_the_excerpt( $post ),
                    'content' => get_the_content( null, false, $post ),
                    'object'  => $post,
                ];
            }
        }

        $items = apply_filters( 'abc_pagination/get_posts/items', $items, $atts, $context );

        usort( $items, 'Wpshop\AbcPagination\posts_sort_callback' );

        $letter_border = function () use ( $atts ) {
            if ( ! $atts['letter_border_width'] ) {
                return null;
            }
            $width = is_numeric( $atts['letter_border_width'] ) ? $atts['letter_border_width'] . 'px' : $atts['letter_border_width'];

            return "{$width} {$atts['letter_border_style']} {$atts['letter_border_color']}";
        };

        $styles = new SimpleCssBuilder( '' );
        $styles->add( ':root', [
            // Letters
            '--abc-pagination-letters-gap'              => $atts['letters_gap'] ?: null,
            '--abc-pagination-letters-padding'          => $atts['letters_padding'] ?: null,
            '--abc-pagination-letters-background'       => $atts['letters_background'] ?: null,
            '--abc-pagination-letters-justify-content'  => $atts['letters_justify_content'] ?: null,
            '--abc-pagination-letters-border-radius'    => $atts['letters_border_radius'] ?: null,
            // Letter
            '--abc-pagination-letter-padding'           => $atts['letter_padding'] ?: null,
            '--abc-pagination-letter-color'             => $atts['letter_color'] ?: null,
            '--abc-pagination-letter-background'        => $atts['letter_background'] ?: null,
            '--abc-pagination-letter-hover-color'       => $atts['letter_hover_color'] ?: null,
            '--abc-pagination-letter-hover-background'  => $atts['letter_hover_background'] ?: null,
            '--abc-pagination-letter-border'            => $letter_border(),
            '--abc-pagination-letter-font-weight'       => $atts['letter_font_weight'] ?: null,
            '--abc-pagination-letter-font-size'         => $atts['letter_font_size'] ?: null,
            '--abc-pagination-letter-border-radius'     => $atts['letter_border_radius'] ?: null,
            // Tab letter
            '--abc-pagination-tab-letter-width'         => $atts['tab_letter_width'] ?: null,
            '--abc-pagination-tab-letter-margin'        => $atts['tab_letter_margin'] ?: null,
            '--abc-pagination-tab-letter-padding'       => $atts['tab_letter_padding'] ?: null,
            '--abc-pagination-tab-letter-font-weight'   => $atts['tab_letter_font_weight'] ?: null,
            '--abc-pagination-tab-letter-font-size'     => $atts['tab_letter_font_size'] ?: null,
            '--abc-pagination-tab-letter-text-align'    => $atts['tab_letter_text_align'] ?: null,
            '--abc-pagination-tab-letter-color'         => $atts['tab_letter_color'] ?: null,
            '--abc-pagination-tab-letter-background'    => $atts['tab_letter_background'] ?: null,
            '--abc-pagination-tab-letter-border-radius' => $atts['tab_letter_border_radius'] ?: null,
            //Columns
            '--abc-pagination-posts-image-height'       => $atts['posts_image_height'] ?: null,
            '--abc-pagination-posts-columns'            => $atts['posts_columns'] ?: null,
            '--abc-pagination-posts-columns-mobile'     => $atts['posts_columns_mobile'] ?: null,
            '--abc-pagination-posts-gap'                => $atts['posts_gap'] ?: null,
            '--abc-pagination-posts-title-font-weight'  => $atts['posts_title_font_weight'] ?: null,
        ] );

        if ( $atts['show_posts_limit'] > 0 ) {
            $styles->add( ( $atts['class_name'] ?? '' ) . " .abc-pagination-post:nth-child(n + {$atts['show_posts_limit']}):not(.visible)", [ 'display' => 'none' ] );
        }

        $result = ob_get_content( function () use ( $atts, $items, $styles ) {
            get_template_part( 'post/list', null, [
                'alphabet'     => $atts['alphabet'],
                'posts'        => $items,
                'card_type'    => $atts['card_type'],
                'type'         => $atts['type'],
                'show_letters' => $atts['show_letters'],
                'show_counts'  => $atts['show_counts'],
                'show_search'  => $atts['show_search'],

                'show_posts_limit' => $atts['show_posts_limit'],

                'show_tab_letter' => $atts['show_tab_letter'],

                'letters_text_before' => $atts['letters_text_before'],
                'letters_text_after'  => $atts['letters_text_after'],

                'letter_hover_effect' => $atts['letter_hover_effect'],

                'show_post_link'    => $atts['show_post_link'],
                'show_post_thumb'   => $atts['show_post_thumb'],
                'show_post_excerpt' => $atts['show_post_excerpt'],
                'show_post_content' => $atts['show_post_content'],
                'posts_short_title' => $atts['posts_short_title'],
                'posts_show_id'     => $atts['posts_show_id'],
                'class_name'        => sanitize_html_class( $atts['name'] ),

                'styles' => (string) $styles,
            ] );
        } );

        if ( null !== $cache_key ) {
            $this->cache[ $cache_key ] = $result;
        }

        return static::prepare_anchor( $result );
    }

    /**
     * @param Context $context
     * @param array   $atts
     * @param array   $args
     *
     * @return string|null
     */
    protected function get_cache_key( $context, $atts, $args ) {
        ksort_recursive( $atts );
        ksort_recursive( $args );

        return md5( $context->get_hash() . serialize( $atts ) . serialize( $args ) );
    }
}

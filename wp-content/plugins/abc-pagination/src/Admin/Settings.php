<?php

namespace Wpshop\AbcPagination\Admin;

use Wpshop\AbcPagination\Maintenance;
use Wpshop\Settings\AbstractSettings;
use function Wpshop\AbcPagination\doc_link;
use const Wpshop\AbcPagination\VIEW_TYPE_LIST;

class Settings extends AbstractSettings {

    const TEXT_DOMAIN = 'abc-pagination';

    const REG_OPTION_GROUP = 'abc-pagination-registration';
    const REG_OPTION       = 'abc-pagination-r';

    const OPTION_GROUP = 'abc_pagination';
    const OPTION_NAME  = 'abc-pagination-settings';

    /**
     * @var Maintenance
     */
    protected $maintenance;

    /**
     * @var null|array
     */
    protected $_options;

    /**
     * @var null|array
     */
    protected $_options_with_defaults;

    /**
     * @var string[]
     */
    protected $_defaults = [
        // Settings
        'post_types'               => [ 'post', 'page' ],
        'alphabet'                 => '',
        'virtual_category_enabled' => 1,
        'clear_database'           => 1,

        // Settings > Letter list
        'show_letters'             => 1,
        'show_counts'              => 1,
        'show_search'              => 0,

        // Settings > Posts list
        'type'                     => VIEW_TYPE_LIST,
        'show_tab_letter'          => true,
        'show_post_link'           => true,
        'show_post_thumb'          => true,
        'show_post_excerpt'        => false,
        'show_post_content'        => false,
        'posts_short_title'        => true,
        'show_posts_limit'         => - 1,

        // Settings > Glossary
        'enable_glossary'          => 0,
        'is_public_glossary'       => 0,


        // Appearance > Letters
        'letters_gap'              => '.5em',
        'letters_padding'          => '.5em',
        'letters_background'       => '#eaeff6',
        'letters_justify_content'  => 'center',
        'letters_border_radius'    => '.5em',

        // Appearance > Letter
        'letter_padding'           => '.3em .6em',
        'letter_color'             => '#ffffff',
        'letter_background'        => '#74b423',
        'letter_hover_color'       => '#ffffff',
        'letter_hover_background'  => '#5e9617',
        'letter_hover_effect'      => '',
        'letter_border_width'      => '',
        'letter_border_style'      => 'solid',
        'letter_border_color'      => '',
        'letter_font_weight'       => 'bold',
        'letter_font_size'         => '1em',
        'letter_border_radius'     => '.3em',

        // Appearance > Tab letter
        'tab_letter_width'         => 'auto',
        'tab_letter_margin'        => '1.5em 0 .5em',
        'tab_letter_padding'       => '.3em 1em',
        'tab_letter_font_weight'   => 'bold',
        'tab_letter_font_size'     => '1.3em',
        'tab_letter_text_align'    => 'left',
        'tab_letter_color'         => '#111111',
        'tab_letter_background'    => '#eaeff6',
        'tab_letter_border_radius' => '.2em',

        // Appearance > Posts
        'posts_image_height'       => '200px',
        'posts_columns'            => '3',
        'posts_columns_mobile'     => '1',
        'posts_gap'                => '.8em',
        'posts_title_font_weight'  => '400',

        // Custom CSS tab
        'styles'                   => '',
    ];

    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * @var bool
     */
    protected $do_flush_rewrite_rules = false;

    /**
     * @return void
     */
    protected function setup_tabs() {
        $this->add_tab( 'settings', __( 'Settings', 'abc-pagination' ) );
        $this->add_tab( 'appearance', __( 'Appearance', 'abc-pagination' ) );
        $this->add_tab( 'css', __( 'Additional styles', 'abc-pagination' ) );
    }

    /**
     * @inheridoc
     */
    public function get_tab_icons() {
        return array_merge( parent::get_tab_icons(), [
            'settings'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256.01 209.36c12.96 0 25.24 4.27 33.69 11.7 9.07 7.98 13.87 19.73 14.29 34.93-.42 15.21-5.23 26.96-14.3 34.94-8.46 7.44-20.74 11.7-33.69 11.7s-25.24-4.27-33.7-11.7c-9.07-7.98-13.87-19.73-14.29-34.93.42-15.21 5.23-26.96 14.3-34.93 8.46-7.44 20.74-11.7 33.7-11.7M332.28 0H179.72v78.23a189.922 189.922 0 0 0-36.88 21.69l-66.6-39.13L0 195.17l66.58 39.12a198.125 198.125 0 0 0 0 43.41L0 316.83l76.24 134.39 66.6-39.13a189.419 189.419 0 0 0 36.88 21.69v78.23h152.56v-78.23c13.05-5.8 25.41-13.08 36.88-21.69l66.6 39.13L512 316.83l-66.58-39.12c.79-7.23 1.19-14.5 1.19-21.71s-.4-14.48-1.19-21.71L512 195.17 435.76 60.79l-66.6 39.13a189.419 189.419 0 0 0-36.88-21.69V0ZM146.65 155.93c16.34-13.16 35.37-29.23 55.09-36.62l23.83-9.82V46.55h60.88v62.95l23.83 9.82c19.67 7.36 38.8 23.5 55.09 36.62l53.61-31.5 30.48 53.72-53.59 31.49c1.62 12.88 5.23 33.6 4.93 46.36.31 12.67-3.32 33.6-4.93 46.36l53.59 31.49-30.48 53.72-53.61-31.5c-16.34 13.16-35.37 29.23-55.09 36.62l-23.83 9.82v62.95h-60.88v-62.95l-23.83-9.82c-19.67-7.36-38.8-23.5-55.09-36.62l-53.61 31.5-30.48-53.72 53.59-31.49c-1.62-12.88-5.23-33.6-4.93-46.36-.31-12.67 3.32-33.6 4.93-46.36l-53.59-31.49 30.48-53.72 53.61 31.5ZM256 161.36c-47.46 0-94.92 31.55-96 94.63 1.07 63.1 48.53 94.64 96 94.64s94.92-31.55 96-94.64c-1.07-63.09-48.53-94.64-96-94.64Z" fill="currentColor"></path></svg>',
            'appearance' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M176 263c48.52 0 88-39.48 88-88s-39.48-88-88-88-88 39.48-88 88 39.48 88 88 88Zm0-128c22.06 0 40 17.94 40 40s-17.94 40-40 40-40-17.94-40-40 17.94-40 40-40ZM432 0H80C35.89 0 0 35.89 0 80v352c0 44.11 35.89 80 80 80h352c44.11 0 80-35.89 80-80V80c0-44.11-35.89-80-80-80ZM80 48h352c17.67 0 32 14.33 32 32v209.01l-76.82-78.77a24 24 0 0 0-17.1-7.24H370c-6.41 0-12.56 2.57-17.07 7.13L196.44 368.44l-66.36-67.29a24.02 24.02 0 0 0-17.07-7.15h-.02a24.05 24.05 0 0 0-17.07 7.12l-47.93 48.47V80c0-17.67 14.33-32 32-32ZM48 432v-14.14l64.98-65.71 49.72 50.42-60.71 61.42H80c-17.67 0-32-14.33-32-32Zm384 32H169.48l200.4-202.74L464 357.77v74.24c0 17.67-14.33 32-32 32Z" fill="currentColor"></path></svg>',
            'css'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M10.06 12.06 6.12 16l3.94 3.94-2.12 2.12L1.88 16l6.06-6.06 2.12 2.12Zm14-2.12-2.12 2.12L25.88 16l-3.94 3.94 2.12 2.12L30.12 16l-6.06-6.06ZM12.03 27.18l2.93.64 5.01-23-2.93-.64-5 23Z" fill="currentColor"></path></svg>',
        ] );
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function doc_link( $type ) {
        return doc_link( $type );
    }

    /**
     * @inheridoc
     */
    protected static function get_template_parts_root() {
        return dirname( ABC_PAGINATION_FILE ) . '/template-parts/';
    }

    /**
     * @inheritвoc
     */
    public static function product_prefix() {
        return 'abc_pagination_';
    }

    /**
     * @param string $name input name
     * @param string $label
     * @param array  $args
     *
     * @return void
     */
    public function render_css_editor( $name, $label = null, array $args = [] ) {
        $input_name = $this->get_input_name( $name );

        $args = wp_parse_args( $args, [
            'rows' => 10,
        ] );
        ?>
        <textarea name="<?php echo $input_name ?>" class="js-abc-pagination-css-editor" rows="<?php echo $args['rows'] ?>"><?php echo esc_textarea( $this->get_value( $name ) ) ?></textarea>
        <?php
    }

    /**ve
     * @return void
     */
    public function handle_activation() {
        if ( php_sapi_name() !== 'cli' && strtoupper( $_SERVER['REQUEST_METHOD'] ) !== 'POST' ||
             ! current_user_can( 'manage_options' )
        ) {
            return;
        }

        if ( ( $_POST['option_page'] ?? '' ) === self::REG_OPTION_GROUP ) {
            if ( is_multisite() && ! current_user_can( 'manage_network_options' ) ) {
                wp_die( __( 'Sorry, you are not allowed to modify unregistered settings for this site.' ) );
            }

            check_admin_referer( self::REG_OPTION_GROUP . '-options' );

            $license = $_POST[ self::REG_OPTION ]['license'] ?? '';
            $result  = $this->maintenance->activate( $license, function ( $params ) {
                $opt = wp_parse_args( $params, [
                    'license'        => '',
                    'license_verify' => '',
                    'license_error'  => '',
                ] );

                update_option( self::REG_OPTION, $opt );
            } );

            wp_redirect( add_query_arg( 'plugin-activated', is_wp_error( $result ) ? 0 : 1, wp_get_referer() ) );
            die;
        }
    }
}

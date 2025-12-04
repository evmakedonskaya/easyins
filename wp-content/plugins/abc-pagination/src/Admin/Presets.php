<?php

namespace Wpshop\AbcPagination\Admin;

use JetBrains\PhpStorm\NoReturn;

class Presets {

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var string[]
     */
    protected $default_keys = [
        // Appearance > Letters
        'letters_gap',
        'letters_padding',
        'letters_background',
        'letters_justify_content',
        'letters_border_radius',

        // Appearance > Letter
        'letter_padding',
        'letter_color',
        'letter_background',
        'letter_hover_color',
        'letter_hover_background',
        'letter_hover_effect',
        'letter_border_width',
        'letter_border_style',
        'letter_border_color',
        'letter_font_weight',
        'letter_font_size',
        'letter_border_radius',

        // Appearance > Tab letter
        'tab_letter_width',
        'tab_letter_margin',
        'tab_letter_padding',
        'tab_letter_font_weight',
        'tab_letter_font_size',
        'tab_letter_text_align',
        'tab_letter_color',
        'tab_letter_background',
        'tab_letter_border_radius',

        // Appearance > Posts
        'posts_image_height',
        'posts_columns',
        'posts_columns_mobile',
        'posts_gap',
        'posts_title_font_weight',
    ];

    /**
     * @var \string[][]
     */
    protected $presets = [
        'default'     => [
            '_image' => 'assets/admin/images/presets/preset-default.png',
        ],
        'red'         => [
            '_image'                  => 'assets/admin/images/presets/preset-red.png',
            'letters_padding'         => '0',
            'letters_background'      => '#ffffff',
            'letters_justify_content' => 'start',
            'letter_padding'          => '.4em .6em',
            'letter_background'       => '#e94f44',
            'letter_hover_background' => '#2c89e8',
            'letter_hover_effect'     => 'pop',
            'letter_font_weight'      => '500',
            'letter_border_radius'    => '.2em',
            'tab_letter_width'        => '4em',
            'tab_letter_font_size'    => '1.2em',
            'tab_letter_text_align'   => 'center',
            'tab_letter_color'        => '#ffffff',
            'tab_letter_background'   => '#e94f44',
        ],
        'viola'       => [
            '_image'                   => 'assets/admin/images/presets/preset-viola.png',
            'letters_gap'              => '0.5em',
            'letters_padding'          => '0',
            'letters_background'       => '#ffffff',
            'letters_justify_content'  => 'start',
            'letter_padding'           => '.35em .8em',
            'letter_background'        => '#7f57e5',
            'letter_hover_background'  => '#a321e0',
            'letter_hover_effect'      => 'push',
            'letter_border_radius'     => '3em',
            'tab_letter_padding'       => '.3em 1.2em',
            'tab_letter_color'         => '#ffffff',
            'tab_letter_background'    => '#7f57e5',
            'tab_letter_border_radius' => '2em',
        ],
        'border-blue' => [
            '_image'                  => 'assets/admin/images/presets/preset-border-blue.png',
            'letters_background'      => '#ffffff',
            'letter_color'            => '#0066f4',
            'letter_background'       => '#ffffff',
            'letter_hover_background' => '#0066f4',
            'letter_border_width'     => '2px',
            'letter_border_color'     => '#0066f4',
            'letter_font_weight'      => '400',
            'letter_font_size'        => '1.2em',
            'tab_letter_padding'      => '0em',
            'tab_letter_font_size'    => '1.6em',
            'tab_letter_color'        => '#0066f4',
            'tab_letter_background'   => '#ffffff',
        ],
        'blue'        => [
            '_image'                  => 'assets/admin/images/presets/preset-blue.png',
            'letters_background'      => '#28a2ff',
            'letters_justify_content' => 'start',
            'letter_hover_color'      => '#0a0a0a',
            'letter_background'       => '#28a2ff',
            'letter_hover_background' => '#ffde26',
            'tab_letter_padding'      => '.5em 1em',
            'tab_letter_font_size'    => '1.2em',
            'tab_letter_text_align'   => 'center',
            'tab_letter_color'        => '#287eff',
            'tab_letter_background'   => '#ebf3ff',
        ],
        'orange'      => [
            '_image'                   => 'assets/admin/images/presets/preset-orange.png',
            'letters_padding'          => '.6em .8em',
            'letters_background'       => '#ffb728',
            'letters_justify_content'  => 'start',
            'letters_border_radius'    => '3em',
            'letter_padding'           => '.35em .8em',
            'letter_color'             => '#000000',
            'letter_hover_color'       => '#0a0a0a',
            'letter_background'        => '#ffffff',
            'letter_hover_background'  => '#ffde26',
            'letter_border_radius'     => '2em',
            'tab_letter_padding'       => '.5em 1.5em',
            'tab_letter_font_size'     => '1.2em',
            'tab_letter_color'         => '#000000',
            'tab_letter_background'    => '#fff3d4',
            'tab_letter_border_radius' => '3em',
        ],
        'salad'       => [
            '_image'                  => 'assets/admin/images/presets/preset-salad.png',
            'letters_gap'             => '0.1em',
            'letters_padding'         => '0.3em',
            'letters_background'      => '#a2f96c',
            'letters_justify_content' => 'start',
            'letters_border_radius'   => '.2em',
            'letter_padding'          => '.3em .7em',
            'letter_color'            => '#000000',
            'letter_hover_color'      => '#000000',
            'letter_background'       => '#a2f96c',
            'letter_hover_background' => '#ffffff',
            'letter_hover_effect'     => 'pop',
            'letter_font_weight'      => '400',
            'letter_font_size'        => '1.2em',
            'letter_border_radius'    => '0em',
            'tab_letter_font_weight'  => '400',
            'tab_letter_background'   => '#a2f96c',
        ],
        'sky'         => [
            '_image'                   => 'assets/admin/images/presets/preset-sky.png',
            'letters_background'       => '#d0dff5',
            'letter_color'             => '#000000',
            'letter_hover_color'       => '#000000',
            'letter_background'        => '#ffffff',
            'letter_hover_background'  => '#d0dff5',
            'letter_border_width'      => '4px',
            'letter_border_color'      => '#ffffff',
            'letter_font_weight'       => '600',
            'tab_letter_background'    => '#e9f2ff',
            'tab_letter_border_radius' => '.5em',
        ],
    ];

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
        if ( wp_doing_ajax() ) {
            $action = 'abc_pagination_save_preset';
            add_action( "wp_ajax_{$action}", [ $this, '_save_preset' ] );
        }
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function _save_preset() {
        if ( ! defined( 'ABC_PAGINATION_DEV' ) || ! ABC_PAGINATION_DEV ) {
            wp_send_json_error();
        }

        $data = $_POST['preset'] ?? [];
        if ( ! $data ) {
            wp_send_json_error( new \WP_Error( 'empty_data', __( 'Unable to save preset with empty data' ) ) );
        }
        $data = wp_parse_args( $data, [ 'name' => '', 'values' => [] ] );

        $preset = [];
        foreach ( $data['values'] as $key => $value ) {
            if ( ! in_array( $key, $this->default_keys ) ) {
                continue;
            }
            $default = $this->settings->get_default( $key );
            if ( null !== $default && $default == $value ) {
                continue;
            }
            $preset[ $key ] = $value;
        }

        $tmp_file = plugin_dir_path( ABC_PAGINATION_FILE ) . '_presets_tmp.php';
        if ( ! file_exists( $tmp_file ) ) {
            file_put_contents( $tmp_file, "<?php\n" );
        }
        $preset = var_export( [ $data['name'] => $preset ], true );
        file_put_contents( $tmp_file, '$a = ' . $preset . ";\n", FILE_APPEND );

        wp_send_json_success();
    }

    /**
     * @return false|string
     */
    public function get_presets_data_json() {
        $result = [
            'presets'  => $this->presets,
            'defaults' => [],
        ];
        foreach ( $this->default_keys as $key ) {
            $result['defaults'][ $key ] = $this->settings->get_default( $key );
        }

        return json_encode( $result );
    }

    /**
     * @return \string[][]
     */
    public function get_presets() {
        return $this->presets;
    }
}

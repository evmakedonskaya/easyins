<?php

namespace Wpshop\AbcPagination;

use Wpshop\AbcPagination\Admin\Settings;

class AssetsProvider {

    /**
     * @var Settings
     */
    protected $settings;

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
        add_action( 'wp_enqueue_scripts', [ $this, '_enqueue_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, '_admin_enqueue_scripts' ] );
    }

    /**
     * @return void
     */
    public function _enqueue_scripts() {
        if ( ! $this->settings->verify() ) {
            return;
        }
        $version = ABC_PAGINATION_VERSION;
        wp_enqueue_style( 'abc-pagination-style', plugin_dir_url( ABC_PAGINATION_FILE ) . 'assets/public/css/style.min.css', [], $version );
        wp_enqueue_script( 'abc-pagination-scripts', plugin_dir_url( ABC_PAGINATION_FILE ) . 'assets/public/js/scripts.min.js', [ 'jquery' ], $version, true );

        wp_localize_script( 'abc-pagination-scripts', 'abc_pagination_ajax', [
            'url' => admin_url( 'admin-ajax.php' ),
        ] );
    }

    /**
     * @return void
     */
    public function _admin_enqueue_scripts() {
        if ( ! get_current_screen() || ! in_array( get_current_screen()->id, [ 'settings_page_abc-pagination-settings' ] ) ) {
            return;
        }

        $version = ABC_PAGINATION_VERSION;

        wp_enqueue_style(
            'abc-pagination-settings',
            plugin_dir_url( ABC_PAGINATION_FILE ) . 'assets/admin/css/settings.min.css',
            [],
            $version
        );

        wp_enqueue_script(
            'abc-pagination-settings',
            plugin_dir_url( ABC_PAGINATION_FILE ) . 'assets/admin/js/settings.min.js',
            [ 'jquery', 'wp-color-picker', 'wp-theme-plugin-editor' ],
            $version, true
        );
        wp_localize_script( 'abc-pagination-settings', 'abc_pagination_settings_globals', [
            'storage_key' => 'abc-pagination-settings-tab',
            'actions'     => Settings::ajax_actions(),
        ] );

        $cm_settings['codeEditor'] = wp_enqueue_code_editor( [ 'type' => 'text/css' ] );

        wp_localize_script( 'abc-pagination-settings', 'abc_pagination_settings', $cm_settings );

        wp_localize_script( 'abc-pagination-settings', 'abc_pagination_messages', [
            'viewTypeWithLetterList' => __( 'With the output type "{{viewType}}", you need to enable the display of a list of letters. Turn it on?', 'abc-pagination' ),
        ] );

        // for presets preview
        wp_enqueue_style( 'abc-pagination-public-style', plugin_dir_url( ABC_PAGINATION_FILE ) . 'assets/public/css/style.min.css', [], $version );
    }
}

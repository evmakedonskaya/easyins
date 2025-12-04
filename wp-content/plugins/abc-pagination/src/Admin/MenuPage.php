<?php

namespace Wpshop\AbcPagination\Admin;

use function Wpshop\AbcPagination\get_template_part;

class MenuPage {

    const SETTINGS_SLUG = 'abc-pagination-settings';

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
        add_action( 'admin_menu', [ $this, '_setup_menu' ] );
    }

    /**
     * @return void
     */
    public function _setup_menu() {
        add_options_page(
            __( 'ABC Pagination', 'abc-pagination' ),
            __( 'ABC Pagination', 'abc-pagination' ),
            'manage_options',
            self::SETTINGS_SLUG,
            function () {
                get_template_part( 'admin/settings' );
            }
        );
    }
}

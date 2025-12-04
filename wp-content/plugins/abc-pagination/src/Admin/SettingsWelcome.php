<?php

namespace Wpshop\AbcPagination\Admin;

/**
 * @deprecated
 */
class SettingsWelcome {

    /**
     * @return void
     */
    public function init() {
        $action = 'abc_pagination_hide_welcome';
        add_action( "wp_ajax_{$action}", [ $this, '_handle_hide_welcome' ] );
    }

    /**
     * @return void
     */
    public function _handle_hide_welcome() {
        update_option( 'abc-pagination--hide-settings-welcome', 1 );
        wp_send_json_success();
    }

    /**
     * @return void
     */
    public function clear_database() {
        delete_option( 'abc-pagination--hide-settings-welcome' );
    }

    /**
     * @return bool
     */
    public function do_show() {
        return ! get_option( 'abc-pagination--hide-settings-welcome', 0 );
    }
}

<?php

namespace Wpshop\AbcPagination;

use JetBrains\PhpStorm\NoReturn;
use WP_Error;

class Ajax {

    /**
     * @return void
     */
    public function init() {
        $action = 'abc_pagination_fetch_content';
        add_action( "wp_ajax_{$action}", [ $this, '_fetch_pagination' ] );
        add_action( "wp_ajax_nopriv_{$action}", [ $this, '_fetch_pagination' ] );
    }

    /**
     * @return void
     */
    #[NoReturn]
    public function _fetch_pagination() {
        $params = $_REQUEST['params'] ?? null;
        if ( null === $params ) {
            wp_send_json_error( new WP_Error( 'empty_params', __( 'Unable to handle request without params', 'abc-pagination' ) ) );
        }
        $params = base64_decode( $params );
        if ( false === $params ) {
            wp_send_json_error( new WP_Error( 'base64_decode', __( 'Unable to parse params', 'abc-pagination' ) ) );
        }
        $params = json_decode( $params, true );
        if ( null === $params ) {
            wp_send_json_error( new WP_Error( 'json_decode', __( 'Unable to parse params', 'abc-pagination' ) ) );
        }

        unset( $params['ajax'] );

        array_walk( $params, function ( &$param, $key ) {
            $param = $key . '=' . ( is_numeric( $param ) ? $param : '"' . $param . '"' );
        } );

        wp_send_json_success( [
            'html' => do_shortcode( '[abc_pagination ' . implode( ' ', $params ) . ']' ),
        ] );
    }
}

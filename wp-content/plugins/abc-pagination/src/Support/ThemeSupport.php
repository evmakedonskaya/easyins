<?php

namespace Wpshop\AbcPagination\Support;

use Wpshop\AbcPagination\VirtualTax;
use function Wpshop\AbcPagination\is_theme;

class ThemeSupport {

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
        if ( is_theme( 'reboot' ) ) {
            add_filter( 'is_active_sidebar', [ $this, '_check_sidebar_state' ] );
            add_filter( 'body_class', [ $this, '_set_sidebar_body_class' ] );
        }
    }

    /**
     * @param bool $result
     *
     * @return bool
     */
    public function _check_sidebar_state( $result ) {
        if ( $this->virtual_tax->is_applied() ) {
            global $wpshop_core;

            return in_array( $wpshop_core->get_option( 'structure_archive_sidebar' ), [ 'left', 'right' ] );
        }

        return $result;
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    public function _set_sidebar_body_class( $classes ) {
        if ( $this->virtual_tax->is_applied() ) {
            global $wpshop_core;

            if ( ! in_array( $wpshop_core->get_option( 'structure_archive_sidebar' ), [ 'left', 'right' ] ) ) {
                $classes = array_filter( $classes, function ( $item ) {
                    return ! in_array( $item, [ 'sidebar-left', 'sidebar-right' ] );
                } );

                $classes[] = 'sidebar-none';
            } else {
                $classes[] = 'sidebar-' . $wpshop_core->get_option( 'structure_archive_sidebar' );
            }
            $classes = array_unique( $classes );
        }

        return $classes;
    }
}

<?php

namespace Wpshop\AbcPagination\Support;

use Wpshop\AbcPagination\VirtualTax;
use function Wpshop\AbcPagination\is_plugin_active;

class AllInOneSeoSupport {

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
        if ( ! defined( 'AIOSEO_FILE' ) || ! is_plugin_active( plugin_basename( AIOSEO_FILE ) ) ) {
            return;
        }

//        $orig_object    = null;
//        $orig_object_id = null;
//
//        add_action( 'wp_head', function () use ( &$orig_object, &$orig_object_id ) {
//            global $wp_query;
//
//            $orig_object    = $wp_query->queried_object;
//            $orig_object_id = $wp_query->queried_object_id;
//
//            $wp_query->queried_object    = $this->virtual_tax->get_stashed_term();
//            $wp_query->queried_object_id = $this->virtual_tax->get_stashed_term()->term_id;
//        }, 0.9 );
//
//        add_action( 'wp_head', function () use ( &$orig_object, &$orig_object_id ) {
//            global $wp_query;
//
//            $wp_query->queried_object    = $orig_object;
//            $wp_query->queried_object_id = $orig_object_id;
//        }, 1.1 );
    }
}

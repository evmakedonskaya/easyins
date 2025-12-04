<?php

namespace Wpshop\AbcPagination\Admin;

use function Wpshop\AbcPagination\get_short_title_post_types;
use const Wpshop\AbcPagination\META_TITLE_FOR_SORT;

class PostGrid {

    /**
     * @return void
     */
    public function init() {
        foreach ( get_short_title_post_types() as $post_type ) {
            add_filter( "manage_{$post_type}_posts_columns", [ $this, '_add_columns' ] );
            add_action( "manage_{$post_type}_posts_custom_column", [ $this, '_manage_custom_column' ], 10, 2 );
        }
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    public function _add_columns( array $columns ) {
        if ( array_key_exists( 'date', $columns ) ) {
            $result = [];
            foreach ( $columns as $key => $value ) {
                if ( 'date' === $key ) {
                    $result['abc_pagination_short_title'] = __( 'Short Title', 'abc-pagination' );
                }
                $result[ $key ] = $value;
            }
            $columns = $result;
        } else {
            $columns['abc_pagination_short_title'] = __( 'Short Title', 'abc-pagination' );
        }

        return $columns;
    }

    /**
     * @param string $column_key
     * @param int    $post_id
     *
     * @return void
     */
    public function _manage_custom_column( $column_key, $post_id ) {
        if ( 'abc_pagination_short_title' === $column_key ) {
            echo esc_html( get_post_meta( $post_id, META_TITLE_FOR_SORT, true ) );
        }
    }
}

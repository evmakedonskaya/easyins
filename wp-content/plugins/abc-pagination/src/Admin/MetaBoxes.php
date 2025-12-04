<?php

namespace Wpshop\AbcPagination\Admin;

use WP_Post;
use function Wpshop\AbcPagination\get_short_title_post_types;
use const Wpshop\AbcPagination\META_TITLE_FOR_SORT;

class MetaBoxes {

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
        add_action( 'add_meta_boxes', [ $this, '_add_meta_boxes' ] );
        add_action( 'save_post', [ $this, '_save_post' ], 10, 2 );
    }

    /**
     * @param $post_type
     *
     * @return void
     */
    public function _add_meta_boxes( $post_type ) {
        if ( ! $this->settings->verify() ||
             ! in_array( $post_type, get_short_title_post_types() )
        ) {
            return;
        }

        add_meta_box(
            'abc-pagination-data',
            __( 'ABC Pagination Options', 'abc-pagination' ),
            function ( $post ) {
                ?>
                <div class="abc-pagination-metabox">
                    <label for="<?php echo $_id = uniqid( 'post_title_for_sort.' ) ?>">
                        <?php echo __( 'Short Title', 'abc-pagination' ) ?>
                    </label>
                    <input type="text" id="<?php echo $_id ?>"
                           name="<?php echo META_TITLE_FOR_SORT ?>"
                           value="<?php echo esc_attr( get_post_meta( $post->ID, META_TITLE_FOR_SORT, true ) ) ?>"<?php disabled( ! current_user_can( 'manage_options' ) ) ?>>
                </div>
                <?php
            },
            get_short_title_post_types(),
            'side'
        );
    }

    /**
     * @param int     $post_id
     * @param WP_Post $post
     *
     * @return void
     */
    public function _save_post( $post_id, $post ) {
        if ( ! $this->settings->verify() ||
             ! current_user_can( 'manage_options' ) ||
             ! in_array( $post->post_type, get_short_title_post_types() ) ) {
            return;
        }

        if ( empty( $_POST[ META_TITLE_FOR_SORT ] ) ) {
            delete_post_meta( $post_id, META_TITLE_FOR_SORT );
        } else {
            update_post_meta( $post_id, META_TITLE_FOR_SORT, $_POST[ META_TITLE_FOR_SORT ] );
        }
    }
}

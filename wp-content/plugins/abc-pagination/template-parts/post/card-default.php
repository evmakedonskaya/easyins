<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use function Wpshop\AbcPagination\get_post_anchor;
use function Wpshop\AbcPagination\get_title_from_item;
use const Wpshop\AbcPagination\META_TITLE_FOR_SORT;

/**
 * @version 1.2.0
 */

/**
 * @var $args
 */

$title = get_title_from_item( $args['post'], $args['posts_short_title'] );
$title = apply_filters( 'abc_pagination/post-card/title', $title );

$id = '';
if ( $args['posts_show_id'] ) {
    $id = apply_filters( 'abc_pagination/post-card/id', get_post_anchor( $title, $args['post'] ), $args['post'] );
}

?>

<div class="abc-pagination-post js-abc-pagination-post"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : '' ?>>

    <?php if ( $args['show_post_link'] ) : ?><a href="<?php echo $args['post']['url']; ?>"><?php endif; ?>

        <?php if ( $args['show_post_thumb'] && $args['post']['object'] instanceof WP_Post): ?>
            <div class="abc-pagination-post__thumb">
                <?php echo get_the_post_thumbnail( $args['post']['id'], apply_filters( 'abc_pagination/post-card/thumbnail_size', 'post-thumbnail' ) ) ?>
            </div>
        <?php endif; ?>

        <div class="abc-pagination-post__title js-abc-pagination-post-title">
            <?php echo do_shortcode( esc_html( $title ) ) ?>
        </div>

    <?php if ( $args['show_post_link'] ) : ?></a><?php endif; ?>

    <?php
    if ( $args['show_post_excerpt'] ) : ?>
        <div class="abc-pagination-post__excerpt"><?php echo do_shortcode( $args['post']['excerpt'] ) ?></div>
    <?php endif ?>

    <?php if ( $args['show_post_content'] ): ?>
        <div class="abc-pagination-post__content"><?php echo do_shortcode( $args['post']['content'] ) ?></div>
    <?php endif ?>
</div>

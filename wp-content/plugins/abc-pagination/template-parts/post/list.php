<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Wpshop\AbcPagination\Admin\Settings;
use Wpshop\AbcPagination\PluginContainer;
use Wpshop\AbcPagination\Shortcodes;
use function Wpshop\AbcPagination\container;
use function Wpshop\AbcPagination\transform_posts_structure;
use function Wpshop\AbcPagination\get_template_part;
use const Wpshop\AbcPagination\VIEW_TYPE_TABS;

/**
 * @version 1.2.0
 */

/**
 * @var array{'posts': WP_Post[], 'card_type': string} $args
 */

$settings = container()->get( Settings::class );

$sorted_posts = transform_posts_structure( $args['posts'], $args['alphabet'] );

$show_search = apply_filters( 'abc_pagination/post-list/show_search', $args['show_search'] );
?>

<?php if ( $args['styles'] ) : ?>
    <style id="<?php echo uniqid( 'abc_pagination_styles.' ) ?>">
        <?php echo $args['styles'] ?>
    </style>
<?php endif ?>

<?php if ( $custom_styles = $settings->get_value( 'styles' ) ): ?>
    <style id="<?php echo uniqid( 'abc_pagination_custom_styles.' ) ?>">
        <?php echo $custom_styles ?>
    </style>
<?php endif ?>

<div class="abc-pagination<?php echo $args['class_name'] ? " {$args['class_name']}" : '' ?> js-abc-pagination-container">
    <?php if ( $args['show_letters'] || $show_search ): ?>
        <div class="abc-pagination-letters<?php echo $args['letter_hover_effect'] ? " abc-pagination-letters--hover-{$args['letter_hover_effect']}" : '' ?> js-abc-pagination-letter-list" data-type="<?php echo $args['type'] ?>">
            <?php

            if ( ! empty( $args['letters_text_before'] ) ) {
                echo '<span class="abc-pagination-letters-text-before">' . $args['letters_text_before'] . '</span>';
            }

            if ( $show_search ) {
                get_template_part( 'post/search', null, [ 'type' => $args['type'] ] );
            }

            $n = - 1;
            foreach ( $sorted_posts as $letter => $_posts ):
                $n ++;
                $letter_link = apply_filters( 'abc_pagination/letters/letter_link', '#' . Shortcodes::anchor( $letter ), $letter );
                ?>
                <a href="<?php echo esc_attr( $letter_link ) ?>" class="abc-pagination-letter js-abc-pagination-letter<?php echo $args['type'] == VIEW_TYPE_TABS && ! $n ? ' active' : '' ?>">
                    <?php echo esc_html( $letter ) ?>
                    <?php if ( $args['show_counts'] ): ?>
                        <small><?php echo count( $_posts ) ?></small>
                    <?php endif ?>
                </a>
            <?php
            endforeach;

            if ( ! empty( $args['letters_text_after'] ) ) {
                echo '<span class="abc-pagination-letters-text-after">' . $args['letters_text_after'] . '</span>';
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="abc-pagination-posts abc-pagination-posts--type-<?php echo $args['type'] ?> js-abc-pagination-tab-list" data-type="<?php echo $args['type'] ?>">
        <?php
        foreach ( $sorted_posts as $first_letter => $_posts ) :
            ?>
            <div class="abc-pagination-tab js-abc-pagination-tab" id="<?php echo Shortcodes::anchor( $first_letter ) ?>">
                <?php if ( $args['show_tab_letter'] ): ?>
                    <div class="abc-pagination-tab__letter"><?php echo $first_letter ?></div>
                <?php endif ?>
                <div class="abc-pagination-tab__posts">
                    <?php
                    $_post_count = 0;
                    foreach ( $_posts as $post ) {
                        $_post_count ++;
                        setup_postdata( $post );
                        get_template_part( 'post/card', $args['card_type'], [
                            'post' => $post,

                            'show_post_link'    => $args['show_post_link'],
                            'show_post_thumb'   => $args['show_post_thumb'],
                            'show_post_excerpt' => $args['show_post_excerpt'],
                            'show_post_content' => $args['show_post_content'],
                            'posts_short_title' => $args['posts_short_title'],
                            'posts_show_id'     => $args['posts_show_id'],
                        ] );
                    }
                    ?>
                </div>
                <?php if ( $args['type'] === 'popup' ): ?>
                    <div class="abc-pagination-tab__close js-abc-pagination-close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M440.97 407.03c9.37 9.37 9.37 24.57 0 33.94-4.69 4.69-10.83 7.03-16.97 7.03s-12.28-2.34-16.97-7.03L256 289.94 104.97 440.97C100.28 445.66 94.14 448 88 448s-12.28-2.34-16.97-7.03c-9.37-9.37-9.37-24.57 0-33.94L222.06 256 71.03 104.97c-9.37-9.37-9.37-24.57 0-33.94 9.37-9.37 24.57-9.37 33.94 0L256 222.06 407.03 71.03c9.37-9.37 24.57-9.37 33.94 0 9.37 9.37 9.37 24.57 0 33.94L289.94 256l151.03 151.03Z" fill="currentColor"></path>
                        </svg>
                    </div>
                <?php endif ?>
                <?php if ( $args['show_posts_limit'] > 0 && $_post_count >= $args['show_posts_limit'] ): ?>
                    <span class="abc-pagination-tab__more js-abc-pagination-show-more">
                        <?php echo apply_filters( 'abc_pagination/post_list/show_more_text', __( 'show more', 'abc-pagination' ) ) ?>
                    </span>
                <?php endif ?>
            </div>
        <?php endforeach;

        wp_reset_postdata();
        ?>
    </div>
    <?php do_action( 'abc_pagination/post_list/after', $args ); ?>
</div>

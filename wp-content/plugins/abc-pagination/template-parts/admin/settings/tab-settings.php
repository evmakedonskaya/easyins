<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Wpshop\AbcPagination\Admin\Settings;
use function Wpshop\AbcPagination\container;
use function Wpshop\AbcPagination\doc_link;
use function Wpshop\AbcPagination\displayed;
use const Wpshop\AbcPagination\VIEW_TYPE_LIST;
use const Wpshop\AbcPagination\VIEW_TYPE_POPUP;
use const Wpshop\AbcPagination\VIEW_TYPE_TABS;

/**
 * @var array{'label':string} $args
 */

$settings = container()->get( Settings::class );

$post_types = get_post_types( [ 'publicly_queryable' => 1 ], 'objects' );
array_unshift( $post_types, (object) [ 'name' => 'page', 'label' => __( 'Pages' ) ] );
unset( $post_types['attachment'] );
?>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Settings', 'abc-pagination' ),
        '',
        doc_link( 'doc' ) . '/settings/#settings'
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <div class="wpshop-settings-form-row__label">
        <label class="wpshop-settings-form-label">
            <?php echo __( 'Post Types', 'abc-pagination' ) ?>
        </label>
    </div>
    <div class="wpshop-settings-form-row__body">
        <div>
            <?php $value = (array) $settings->get_value( 'post_types' ) ?>
            <?php foreach ( $post_types as $post_type ): ?>
                <div class="wpshop-settings-check">
                    <input type="checkbox" class="wpshop-settings-check-input"
                           id="post_types_<?php echo $post_type->name ?>"
                           name="<?php echo $settings->get_input_name( 'post_types' ) ?>[]"
                           value="<?php echo $post_type->name ?>"<?php checked( in_array( $post_type->name, $value ) ) ?>>
                    <label class="wpshop-settings-check-label" for="post_types_<?php echo $post_type->name ?>"><?php echo $post_type->label ?>
                        (<?php echo $post_type->name ?>)</label>
                </div>
            <?php endforeach ?>
        </div>

        <div class="wpshop-settings-form-description">
            <?php echo __( 'For the selected post types, the plugin settings will be displayed on the post editing page.', 'abc-pagination' ) ?>
        </div>
    </div>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox(
        'virtual_category_enabled',
        __( 'Replace category and tag content', 'abc-pagination' ),
        [],
        __( 'Replaces the usual output of posts in a rubric and tags along with pagination with an alphabetical index. It works if there is the [abc_pagination] shortcode in the rubric or tag description.', 'abc-pagination' )
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox(
        'clear_database',
        __( 'Clear database on the plugin deleting', 'abc-pagination' ),
        [],
        __( 'All data related to the plugin will be deleted from the database.', 'abc-pagination' )
    ); ?>
</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Letter list', 'abc-pagination' ),
        __( 'Settings for the block with the alphabetical list of found letters.', 'abc-pagination' ),
        doc_link( 'doc' ) . '/settings/#settings-list-letters'
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox(
        'show_letters',
        __( 'Show letter list', 'abc-pagination' ), [
            'classes'     => 'js-abc-pagination--show_letters',
            'data-expand' => '.js-abc-pagination--show_counts',
        ]
    ); ?>
</div>
<div class="wpshop-settings-form-row js-abc-pagination--show_counts"<?php displayed( $settings->get_value( 'show_letters' ) ); ?>>
    <?php $settings->render_checkbox( 'show_counts', __( 'Show the number of entries for each letter', 'abc-pagination' ) ); ?>
</div>
<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_search', __( 'Show Search', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Posts list', 'abc-pagination' ),
        '',
        doc_link( 'doc' ) . '/settings/#post-list'
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_select( 'type', __( 'View type', 'abc-pagination' ), [
        VIEW_TYPE_LIST  => __( 'list', 'abc-pagination' ),
        VIEW_TYPE_TABS  => __( 'tabs', 'abc-pagination' ),
        VIEW_TYPE_POPUP => __( 'popup', 'abc-pagination' ),
    ], [ 'classes' => 'js-abc-pagination--type' ] ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input(
        'show_posts_limit',
        __( 'Show posts limit', 'abc-pagination' ),
        [
            'type' => 'number',
            'min'  => - 1,
            'step' => 1,
        ],
        __( 'Show the specified number of posts and the "show more" link. <code>-1</code> sets the output to no limit.', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_tab_letter', __( 'Show letter', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_post_link', __( 'Show post link', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_post_thumb', __( 'Show post thumbnail', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_post_excerpt', __( 'Show post excerpt', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'show_post_content', __( 'Show post content', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox(
        'posts_short_title',
        __( 'Use a short name', 'abc-pagination' ),
        [],
        __( 'In posts, you can set a short name that is used for sorting. With this option it will also be used in the post cards instead of the full title of the post.', 'abc-pagination' )
    ); ?>
</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Glossary', 'abc-pagination' ),
        __( 'A glossary is a glossary of terms with descriptions. It is used to explain little-known or highly specialized words. Usually both terms and their descriptions are displayed on the same page.', 'abc-pagination' ),
        doc_link( 'doc' ) . '/settings/#glossary'
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_checkbox( 'enable_glossary', __( 'Enable glossary', 'abc-pagination' ), [
        'data-expand' => '.glossary-shortcode,.js-abc-pagination--is_public_glossary',
    ] ); ?>
    <div class="wpshop-settings-form-description wpshop-settings-form-description--switch-box glossary-shortcode"<?php displayed( $settings->get_value( 'enable_glossary' ) ); ?>>
        <p><?php printf( __( 'Copy and paste the shortcode onto the page (more <a href="%s" target="_blank" rel="noopener">about the shortcode</a>)', 'abc-pagination' ), doc_link( 'doc' ) . '/shortcodes/' ) ?>
            :</p>
        <pre>[abc_pagination post_type="glossary" show_post_link="0" show_post_content="1" columns="1"]</pre>
    </div>
</div>

<div class="wpshop-settings-form-row js-abc-pagination--is_public_glossary"<?php displayed( $settings->get_value( 'enable_glossary' ) ); ?>>
    <div class="wpshop-settings-form-label">
        <?php $settings->render_checkbox( 'is_public_glossary', __( 'Enable pages for glossary terms', 'abc-pagination' ) ); ?>
    </div>
    <div class="wpshop-settings-form-description wpshop-settings-form-description--switch-box">
        <?php echo __( 'By default, no separate pages are created for each glossary term.', 'abc-pagination' ) ?>
        <?php echo __( 'Usually this is not necessary, because the name and description of the term are displayed on the same page.', 'abc-pagination' ) ?>
    </div>
</div>

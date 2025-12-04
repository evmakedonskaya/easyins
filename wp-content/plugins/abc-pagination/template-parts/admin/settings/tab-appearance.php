<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Wpshop\AbcPagination\Admin\Settings;
use Wpshop\AbcPagination\Admin\Presets;
use function Wpshop\AbcPagination\container;
use function Wpshop\AbcPagination\doc_link;

/**
 * @var array{'label':string} $args
 */

$settings = container()->get( Settings::class );
$presets  = container()->get( Presets::class );

?>

<?php
$header_description = '<p>' . sprintf(
        esc_html__( 'In this section you can customize the appearance for all alphabetical lists. Almost all settings correspond to similar %s, so you can customize through them.', 'abc-pagination' ),
        '<a href="' . doc_link( 'doc' ) . '/css-variables/" target="_blank" rel="noopener">' . _x( 'CSS variables', 'admin-header-description', 'abc-pagination' ) . '</a>'
    ) . '</p>';

$header_description .= '<p>' . sprintf(
        esc_html__( 'If you need to style an individual alphabetical lists, you can do so through the %s. The shortcode settings will replace the settings on this page.', 'abc-pagination' ),
        '<a href="' . doc_link( 'doc' ) . '/shortcodes/" target="_blank" rel="noopener">' . _x( 'shortcode attributes', 'admin-header-description', 'abc-pagination' ) . '</a>'
    ) . '</p>';

?>
<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Appearance', 'abc-pagination' ),
        $header_description,
        doc_link( 'doc' ) . '/settings/#appearance'
    ); ?>
</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Presets', 'abc-pagination' ),
        __( 'Presets are ready-to-use design sets that allow you to set new appearance settings in 1 click. Attention, old settings will be overwritten.', 'abc-pagination' ),
        doc_link( 'doc' ) . '/settings/#appearance-presets'
    ); ?>
</div>

<div class="abc-pagination-presets">
    <?php foreach ( $presets->get_presets() as $preset_name => $preset_item ): ?>
        <div class="abc-pagination-preset js-abc-pagination-preset" data-name="<?php echo esc_attr( $preset_name ); ?>">
            <img src="<?php echo esc_attr( plugin_dir_url( ABC_PAGINATION_FILE ) . $preset_item['_image'] ) ?>" alt="">
        </div>
    <?php endforeach ?>
</div>
<script>
    var abcPaginationPresetData = '<?php echo $presets->get_presets_data_json() ?>';
</script>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Letter list', 'abc-pagination' ),
        __( 'Separately, you can configure the block in which the letters are located and separately the appearance of the letters themselves.', 'abc-pagination' ),
        doc_link( 'doc' ) . '/settings/#appearance-list-letters'
    ); ?>
</div>

<div class="abc-pagination-admin-preview js-abc-pagination-admin-preview" id="abc-pagination-letters" data-selector=".abc-pagination-letters">

    <style>
        .abc-pagination-letters {
            /*variables*/
        }
    </style>

    <div class="abc-pagination-letters">
        <a href="#" class="abc-pagination-letter">A</a>
        <a href="#" class="abc-pagination-letter">B</a>
        <a href="#" class="abc-pagination-letter">C</a>
        <a href="#" class="abc-pagination-letter">D</a>
        <a href="#" class="abc-pagination-letter">E</a>
        <a href="#" class="abc-pagination-letter">F</a>
        <a href="#" class="abc-pagination-letter">G</a>
    </div>

</div>


<?php $settings->render_subheader( __( 'Container with letters', 'abc-pagination' ) ); ?>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letters_gap', __( 'Letter spacing', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letters-gap -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letters_padding', __( 'Indents', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letters-padding -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_color_picker( 'letters_background', __( 'Background color', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letters-background -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_select( 'letters_justify_content', __( 'Letter alignment', 'abc-pagination' ), [
        'start'         => __( 'start', 'abc-pagination' ),
        'center'        => __( 'center', 'abc-pagination' ),
        'end'           => __( 'end', 'abc-pagination' ),
        'space-evenly'  => __( 'space-evenly', 'abc-pagination' ),
        'space-around'  => __( 'space-around', 'abc-pagination' ),
        'space-between' => __( 'space-between', 'abc-pagination' ),
    ] ); ?>
    <!-- --abc-pagination-letters-justify-content -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letters_border_radius', __( 'Rounding', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letters-border-radius -->
</div>


<?php $settings->render_subheader( __( 'Letters', 'abc-pagination' ) ); ?>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letter_padding', __( 'Indents', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letter-padding -->
</div>

<div class="wpshop-settings-form-row">
    <div class="wpshop-settings-form-row__label">
        <label for=""><?php _e( 'Color', 'abc-pagination' ) ?></label>
    </div>
    <div class="wpshop-settings-form-row__body">
        <input type="text"
               name="<?php echo $settings->get_input_name( 'letter_color' ) ?>"
               value="<?php echo $settings->get_value( 'letter_color' ) ?>" class="js-wpshop-settings-color-picker">
        <!-- --abc-pagination-letter-color -->

        <span class="wpshop-settings-form-row__inline"><?php _e( 'Hover', 'abc-pagination' ) ?>:</span>

        <input type="text"
               name="<?php echo $settings->get_input_name( 'letter_hover_color' ) ?>"
               value="<?php echo $settings->get_value( 'letter_hover_color' ) ?>" class="js-wpshop-settings-color-picker">
        <!-- --abc-pagination-letter-hover-color -->
    </div>
</div>
<div class="wpshop-settings-form-row">
    <div class="wpshop-settings-form-row__label">
        <label for=""><?php _e( 'Background color', 'abc-pagination' ) ?></label>
    </div>
    <div class="wpshop-settings-form-row__body">
        <input type="text"
               name="<?php echo $settings->get_input_name( 'letter_background' ) ?>"
               value="<?php echo $settings->get_value( 'letter_background' ) ?>" class="js-wpshop-settings-color-picker">
        <!-- --abc-pagination-letter-background -->

        <span class="wpshop-settings-form-row__inline"><?php _e( 'Hover', 'abc-pagination' ) ?>:</span>

        <input type="text"
               name="<?php echo $settings->get_input_name( 'letter_hover_background' ) ?>"
               value="<?php echo $settings->get_value( 'letter_hover_background' ) ?>" class="js-wpshop-settings-color-picker">
        <!-- --abc-pagination-letter-hover-background -->
    </div>
</div>

<div class="wpshop-settings-form-row">
    <div class="wpshop-settings-form-row__label">
        <label><?php echo __( 'Hover', 'abc-pagination' ) ?></label>
    </div>
    <div class="wpshop-settings-form-row__body">
        <?php
        $options = [
            ''       => __( 'Select effect', 'abc-pagination' ),
            'grow'   => _x( 'Grow', 'hover effect', 'abc-pagination' ),
            'shrink' => _x( 'Shrink', 'hover effect', 'abc-pagination' ),
            'push'   => _x( 'Push', 'hover effect', 'abc-pagination' ),
            'pop'    => _x( 'Pop', 'hover effect', 'abc-pagination' ),
            'float'  => _x( 'Float', 'hover effect', 'abc-pagination' ),
        ];
        ?>
        <select name="<?php echo esc_attr( $settings->get_input_name( 'letter_hover_effect' ) ) ?>" class="wpshop-settings-select">
            <?php foreach ( $options as $value => $label ): ?>
                <option value="<?php echo $value ?>"<?php selected( $settings->get_value( 'letter_hover_effect' ), $value ) ?>><?php echo $label ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>

<div class="wpshop-settings-form-row">
    <?php
    $letter_border_width = 'letter_border_width';
    $letter_border_style = 'letter_border_style';
    $letter_border_color = 'letter_border_color';

    ?>
    <div class="wpshop-settings-form-row__label">
        <label><?php echo __( 'Border', 'abc-pagination' ) ?></label>
    </div>
    <div class="wpshop-settings-form-row__body">
        <input type="text"
               size="7"
               name="<?php echo $settings->get_input_name( $letter_border_width ) ?>"
               value="<?php echo $settings->get_value( $letter_border_width ) ?>">
        <?php $options = [
            'solid'  => __( 'solid', 'abc-pagination' ),
            'dotted' => __( 'dotted', 'abc-pagination' ),
            'dashed' => __( 'dashed', 'abc-pagination' ),
            'double' => __( 'double', 'abc-pagination' ),
        ]; ?>
        <select name="<?php echo $settings->get_input_name( $letter_border_style ) ?>">
            <?php foreach ( $options as $value => $label ): ?>
                <option value="<?php echo $value ?>"<?php selected( $settings->get_value( $letter_border_style ), $value ) ?>><?php echo $label ?></option>
            <?php endforeach ?>
        </select>
        <input type="text"
               name="<?php echo $settings->get_input_name( $letter_border_color ) ?>"
               value="<?php echo $settings->get_value( $letter_border_color ) ?>" class="js-wpshop-settings-color-picker">
    </div>
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letter_font_weight', __( 'Font thickness', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letter-font-weight -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letter_font_size', __( 'Font size', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letter-font-size -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'letter_border_radius', __( 'Rounding', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-letter-radius -->
</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header( __( 'Post list', 'abc-pagination' ), '', doc_link( 'doc' ) . '/settings/#list-posts' ); ?>
</div>

<div class="abc-pagination-admin-preview js-abc-pagination-admin-preview" id="abc-pagination-post-list" data-selector=".abc-pagination-tab">

    <style>
        .abc-pagination-tab {
            /*variables*/
        }
    </style>

    <div class="abc-pagination-tab">
        <div class="abc-pagination-tab__letter">A</div>
        <div class="abc-pagination-tab__posts">

            <div class="abc-pagination-post">
                <a href="#">
                    <div class="abc-pagination-post__thumb"></div>
                    <div class="abc-pagination-post__title"><?php echo __( 'Post title', 'abc-pagination' ) ?></div>
                </a>
            </div>

            <div class="abc-pagination-post">
                <a href="#">
                    <div class="abc-pagination-post__thumb"></div>
                    <div class="abc-pagination-post__title"><?php echo __( 'Post title', 'abc-pagination' ) ?></div>
                </a>
            </div>

            <div class="abc-pagination-post">
                <a href="#">
                    <div class="abc-pagination-post__thumb"></div>
                    <div class="abc-pagination-post__title"><?php echo __( 'Post title', 'abc-pagination' ) ?></div>
                </a>
            </div>

        </div>
    </div>

</div>

<?php $settings->render_subheader( __( 'Letters', 'abc-pagination' ) ); ?>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_width', __( 'Width block', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-width -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_margin', __( 'Indentation', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-margin -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_padding', __( 'Indents', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-padding -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_font_weight', __( 'Font thickness', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-font-weight -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_font_size', __( 'Font size', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-font-size -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_select( 'tab_letter_text_align', __( 'Text alignment', 'abc-pagination' ), [
        'left'   => __( 'left', 'abc-pagination' ),
        'right'  => __( 'right', 'abc-pagination' ),
        'center' => __( 'center', 'abc-pagination' ),
    ] ); ?>
    <!-- --abc-pagination-tab-letter-text-align -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_color_picker( 'tab_letter_color', __( 'Color', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-color -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_color_picker( 'tab_letter_background', __( 'Background color', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-background -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'tab_letter_border_radius', __( 'Rounding', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-tab-letter-border-radius -->
</div>

<?php $settings->render_subheader( __( 'Posts', 'abc-pagination' ) ); ?>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'posts_image_height', __( 'Thumbnail height', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-posts-image-height -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'posts_columns', __( 'Columns in the desktop version', 'abc-pagination' ), [
        'type' => 'number',
        'min'  => 1,
        'max'  => 5,
        'step' => 1,
    ] ); ?>
    <!-- --abc-pagination-posts-columns -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'posts_columns_mobile', __( 'Columns in the mobile version', 'abc-pagination' ), [
        'type' => 'number',
        'min'  => 1,
        'max'  => 5,
        'step' => 1,
    ] ); ?>
    <!-- --abc-pagination-posts-columns-mobile -->
</div>

<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'posts_gap', __( 'Column spacing', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-posts-gap -->
</div>
<div class="wpshop-settings-form-row">
    <?php $settings->render_input( 'posts_title_font_weight', __( 'Title font weight', 'abc-pagination' ) ); ?>
    <!-- --abc-pagination-posts-title-font-weight -->
</div>

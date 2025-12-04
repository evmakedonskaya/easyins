<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Wpshop\AbcPagination\Admin\Settings;
use function Wpshop\AbcPagination\container;
use function Wpshop\AbcPagination\doc_link;

/**
 * @var array{'label':string} $args
 */

$settings = container()->get( Settings::class );

?>

<div class="wpshop-settings-header">
    <?php $settings->render_header(
        __( 'Additional styles', 'abc-pagination' ),
        __( 'In this section you can specify any styles you want for the alphabetical index. Use as a normal CSS editor, starting each rule with a selector. For example, .abc-pagination { /* your code */ }', 'abc-pagination' ),
        doc_link( 'doc' ) . '/settings/#additional-styles'
    ); ?>
</div>

<div class="wpshop-settings-form-row">
    <div class="abc-pagination-css-editor">
        <?php $settings->render_css_editor( 'styles' ); ?>
    </div>
</div>

<p><?php printf( __( '<strong>We recommend:</strong> see examples of <a href="%s" target="_blank" rel="noopener">CSS variables</a>.', 'abc-pagination' ), doc_link( 'doc' ) . '/css-variables/' ) ?></p>


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

<?php if ( $settings->do_show_welcome() ): ?>
    <div class="wpshop-settings-welcome">
        <div class="wpshop-settings-welcome__close js-wpshop-settings-welcome-close"></div>
        <div class="wpshop-settings-welcome__icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M256 48c114.88 0 208 93.12 208 208s-93.12 208-208 208S48 370.88 48 256 141.12 48 256 48m0-48c-34.54 0-68.07 6.78-99.66 20.14-30.49 12.9-57.86 31.35-81.36 54.84-23.5 23.5-41.95 50.87-54.84 81.36C6.78 187.93 0 221.46 0 256s6.78 68.07 20.14 99.66c12.9 30.49 31.35 57.86 54.84 81.36 23.49 23.5 50.87 41.95 81.36 54.84C187.93 505.22 221.46 512 256 512s68.07-6.78 99.66-20.14c30.49-12.9 57.86-31.35 81.36-54.84 23.5-23.5 41.95-50.87 54.84-81.36C505.22 324.07 512 290.54 512 256s-6.78-68.07-20.14-99.66c-12.9-30.49-31.35-57.86-54.84-81.36-23.49-23.5-50.87-41.95-81.36-54.84C324.07 6.78 290.54 0 256 0Zm-23.27 361.24 149-149c9.37-9.37 9.37-24.57 0-33.94-9.37-9.37-24.57-9.37-33.94 0L215.76 310.33l-55.56-55.56c-9.37-9.37-24.57-9.37-33.94 0-9.37 9.37-9.37 24.57 0 33.94l72.53 72.53c4.69 4.69 10.83 7.03 16.97 7.03s12.28-2.34 16.97-7.03Z" fill="currentColor"></path>
            </svg>
        </div>
        <div class="wpshop-settings-welcome__header"><?php echo __( 'The plugin is successfully installed and ready to work!', 'abc-pagination' ) ?></div>
        <div class="wpshop-settings-welcome__content">
            <p><?php echo __( 'You have succeeded, now you can go to the settings of the plugin.', 'abc-pagination' ) ?></p>
            <p><?php echo __( 'The plugin displays the alphabetical index using a shortcode. The shortcode has various attributes that can change the output of blocks and posts for output.', 'abc-pagination' ) ?></p>
            <p>
                <?php echo __( 'An example of outputting an alphabetical index for a rubric with ID 3: [abc_pagination cat="3"]', 'abc-pagination' ) ?>
                <br>
                <?php printf( __( 'You can read more about the shortcode and all its attributes <a href="%s" target="_blank" rel="noopener">here</a>.', 'abc-pagination' ), doc_link( 'doc' ) . '/shortcodes/' ) ?>
            </p>
            <p>
                <?php printf( __( 'You can change the appearance here in the settings or with <a href="%s" target="_blank" rel="noopener">CSS variables</a>.', 'abc-pagination' ), doc_link( 'doc' ) . '/css-variables/' ) ?>
            </p>
            <p><?php echo __( 'If you\'re ready, you can close this window and move on to the settings.', 'abc-pagination' ) ?></p>
        </div>
    </div>
<?php endif ?>


<div class="wpshop-settings-top">
    <a class="wpshop-settings-top__item wpshop-settings-top__item--blue" href="<?php echo doc_link( 'doc' ) ?>" target="_blank" rel="noopener">
        <img src="<?php echo plugins_url( 'assets/admin/images/widget-docs.svg', ABC_PAGINATION_FILE ) ?>" alt="" height="50">
        <div class="wpshop-settings-top__body">
            <p><?php esc_html_e( 'Documentation', 'abc-pagination' ) ?></p>
            <?php echo __( 'If you have a question about our product, perhaps the answer is already in our documentation.', 'abc-pagination' ) ?>
        </div>
    </a>
    <a class="wpshop-settings-top__item wpshop-settings-top__item--purple" href="<?php echo doc_link( 'faq' ) ?>" target="_blank" rel="noopener">
        <img src="<?php echo plugins_url( 'assets/admin/images/widget-qa.svg', ABC_PAGINATION_FILE ) ?>" alt="" height="50">
        <div class="wpshop-settings-top__body">
            <p><?php echo __( 'Questions and answers', 'abc-pagination' ) ?></p>
            <?php echo __( 'Section of frequently asked questions and answers to them. You can quickly find the answer to a question.', 'abc-pagination' ) ?>
        </div>
    </a>

</div>

<div class="wpshop-settings-header">
    <?php $settings->render_header( __( 'License', 'abc-pagination' ) ); ?>
</div>

<div class="wpshop-settings-license">
    <div class="wpshop-settings-license-info">
        <div class="wpshop-settings-license-info__key">
            <div class="wpshop-settings-license__success">
                <?php echo __( 'The license is successfully activated.', 'abc-pagination' ) ?>
            </div>
            <?php echo __( 'The license key is hidden for security purposes.', 'abc-pagination' ) ?>
        </div>
        <div class="wpshop-settings-license-info__dashboard">
            <a href="https://wpshop.ru/dashboard" target="_blank" rel="noopener" class="wpshop-settings-button"><?php echo __( 'Personal account', 'abc-pagination' ) ?></a>
            <?php if ( current_user_can( 'administrator' ) ): ?>
                <a href="#" class="js-wpshop-settings-remove-license" data-message="<?php echo esc_attr( __( 'Are you sure you want to remove the license key?', 'abc-pagination' ) ) ?>"><?php echo __( 'Remove License', 'abc-pagination' ) ?></a>
            <?php endif ?>
        </div>
    </div>
</div>

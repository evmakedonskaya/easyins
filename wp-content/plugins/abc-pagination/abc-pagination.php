<?php

/**
 * ABC Pagination
 *
 * @wordpress-plugin
 * Plugin Name:       ABC Pagination
 * Plugin URI:        https://wpshop.ru/plugins/abc-pagination
 * Description:       Alphabetical index for WordPress.
 * Author:            WPShop.ru
 * Author URI:        https://wpshop.ru/
 * License:           WPShop License
 * License URI:       https://wpshop.ru/license
 * Text Domain:       abc-pagination
 * Domain Path:       /languages
 * Version:           1.3.2
 * Requires at least: 5.6
 * Tested up to:      6.6
 * Requires PHP:      7.2
 */

use Wpshop\AbcPagination\Admin\MenuPage;
use Wpshop\AbcPagination\Admin\MetaBoxes;
use Wpshop\AbcPagination\Admin\PostGrid;
use Wpshop\AbcPagination\Admin\Presets;
use Wpshop\AbcPagination\Admin\Settings;
use Wpshop\AbcPagination\Ajax;
use Wpshop\AbcPagination\AssetsProvider;
use Wpshop\AbcPagination\Glossary;
use Wpshop\AbcPagination\Shortcodes;
use Wpshop\AbcPagination\Support\RankMathSeoSupport;
use Wpshop\AbcPagination\Support\ThemeSupport;
use Wpshop\AbcPagination\Support\YoastSeoSupport;
use Wpshop\AbcPagination\VirtualTax;
use function Wpshop\AbcPagination\container;

if ( ! defined( 'WPINC' ) ) {
    die;
}

require __DIR__ . '/vendor/autoload.php';

const ABC_PAGINATION_VERSION    = '1.3.2';
const ABC_PAGINATION_FILE       = __FILE__;
const ABC_PAGINATION_SLUG       = 'abc-pagination';
const ABC_PAGINATION_TEXTDOMAIN = 'abc-pagination';
define( 'ABC_PAGINATION_BASENAME', plugin_basename( ABC_PAGINATION_FILE ) );

add_action( 'init', 'Wpshop\AbcPagination\init_i18n' );
add_action( 'activated_plugin', 'Wpshop\AbcPagination\redirect_on_activated' );
add_filter( 'plugin_action_links_' . ABC_PAGINATION_BASENAME, 'Wpshop\AbcPagination\add_settings_plugin_action' );
add_action( 'plugins_loaded', function () {
    container()->get( AssetsProvider::class )->init();
    container()->get( Glossary::class )->init();
    container()->get( MenuPage::class )->init();
    container()->get( MetaBoxes::class )->init();
    container()->get( PostGrid::class )->init();
    container()->get( Presets::class )->init();
    container()->get( Settings::class )->init();
    container()->get( Shortcodes::class )->init();
    container()->get( VirtualTax::class )->init();

    container()->get( RankMathSeoSupport::class )->init();
    container()->get( ThemeSupport::class )->init();
    container()->get( YoastSeoSupport::class )->init();

    if ( wp_doing_ajax() ) {
        container()->get( Ajax::class )->init();
    }
} );

register_activation_hook( ABC_PAGINATION_FILE, 'Wpshop\AbcPagination\activate' );

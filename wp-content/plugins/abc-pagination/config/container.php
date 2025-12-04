<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Wpshop\AbcPagination\Admin\MenuPage;
use Wpshop\AbcPagination\Admin\MetaBoxes;
use Wpshop\AbcPagination\Admin\PostGrid;
use Wpshop\AbcPagination\Admin\Presets;
use Wpshop\AbcPagination\Admin\Settings;
use Wpshop\AbcPagination\Admin\SettingsWelcome;
use Wpshop\AbcPagination\Ajax;
use Wpshop\AbcPagination\AssetsProvider;
use Wpshop\AbcPagination\Glossary;
use Wpshop\AbcPagination\Shortcodes;
use Wpshop\AbcPagination\Support\AllInOneSeoSupport;
use Wpshop\AbcPagination\Support\RankMathSeoSupport;
use Wpshop\AbcPagination\Support\ThemeSupport;
use Wpshop\AbcPagination\Support\YoastSeoSupport;
use Wpshop\AbcPagination\VirtualTax;
use WPShop\Container\ServiceRegistry;
use Wpshop\Settings\Maintenance;
use Wpshop\Settings\MaintenanceInterface;

return function ( $config ) {
    $container = new ServiceRegistry( [
        'config'                    => $config,
        Ajax::class                 => function () {
            return new Ajax();
        },
        AssetsProvider::class       => function ( $c ) {
            return new AssetsProvider( $c[ Settings::class ] );
        },
        Glossary::class             => function ( $c ) {
            return new Glossary( $c[ Settings::class ] );
        },
        MaintenanceInterface::class => function ( $c ) {
            return new Maintenance(
                $c['config']['plugin_config'],
                'plugin',
                ABC_PAGINATION_SLUG,
                ABC_PAGINATION_FILE,
                'abc-pagination'
            );
        },
        MenuPage::class             => function ( $c ) {
            return new MenuPage( $c[ Settings::class ] );
        },
        MetaBoxes::class            => function ( $c ) {
            return new MetaBoxes( $c[ Settings::class ] );
        },
        PostGrid::class             => function () {
            return new PostGrid();
        },
        Presets::class              => function ( $c ) {
            return new Presets( $c[ Settings::class ] );
        },
        Settings::class             => function ( $c ) {
            return new Settings(
                $c[ MaintenanceInterface::class ],
                'abc-pagination-r',
                'abc-pagination-settings'
            );
        },
        SettingsWelcome::class      => function () {
            return new SettingsWelcome();
        },
        Shortcodes::class           => function ( $c ) {
            return new Shortcodes( $c[ Settings::class ] );
        },
        VirtualTax::class           => function ( $c ) {
            return new VirtualTax( $c[ Settings::class ] );
        },

        AllInOneSeoSupport::class => function ( $c ) {
            return new AllInOneSeoSupport( $c[ VirtualTax::class ] );
        },
        RankMathSeoSupport::class => function ( $c ) {
            return new RankMathSeoSupport( $c[ VirtualTax::class ] );
        },
        ThemeSupport::class       => function ( $c ) {
            return new ThemeSupport( $c[ VirtualTax::class ] );
        },
        YoastSeoSupport::class    => function ( $c ) {
            return new YoastSeoSupport( $c[ VirtualTax::class ] );
        },
    ] );

    return $container;
};

<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

return [
    'plugin_config' => [
        'verify_url' => 'https://wpshop.ru/api.php',
        'update'     => [
            'url'          => 'https://api.wpgenerator.ru/wp-update-server/?action=get_metadata&slug=' . ABC_PAGINATION_SLUG,
            'slug'         => ABC_PAGINATION_SLUG,
            'check_period' => 12,
            'opt_name'     => ABC_PAGINATION_SLUG . '-check-update',
        ],
    ],
];

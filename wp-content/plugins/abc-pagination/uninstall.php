<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

use function Wpshop\AbcPagination\uninstall;

const ABC_PAGINATION_SLUG = 'abc-pagination';

require __DIR__ . '/vendor/autoload.php';

uninstall();

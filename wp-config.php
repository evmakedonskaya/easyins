<?php
/**
 * WordPress Configuration File (Production-ready)
 */
// Database settings
define('DB_NAME', 'unkiller');
define('DB_USER', 'unkiller');
define('DB_PASSWORD', 'HWGBBF*BZ1P3M4y9');
define('DB_HOST', 'localhost:/var/run/mysql8-container/mysqld.sock'); // Проверить работоспособность
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
// Authentication Keys and Salts
// ВАЖНО: замените на новые ключи с https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'MS!MQ#09hx=NFA$pJ+x}&r7X[YOeYh>SG#0;-q[)&/ED*DZ?R4RWPUR$;zkq_]63');
define('SECURE_AUTH_KEY',  ')7,4lgu!GY]sfOksy;5y4*d4D;mC|^|tk0}e*lLRJMrL__[ ?:F)#0}9(zExc-XC');
define('LOGGED_IN_KEY',    'aDIxo=60m<h){OGuUYPop>+<L#u|toKAC,:mt<.1wRJ6S^gI]7d0LQx0 }[L96J?');
define('NONCE_KEY',        'mL/YX70;QX<1)dnsGPHpM_-gs-Jv-9Z^YNJ}(JH(1(?vdeZ+I6TS6nb%+HK<t[>y');
define('AUTH_SALT',        'FSFv?xQwgkv2D^n:{Qt=im8yM2r5@.e&/8n:Ot-$/6-+Gwx!.bCeB9H}gUAjYnfN');
define('SECURE_AUTH_SALT', 'AdFgH=sU_<a1;=q$k07OB@Z/-MimfPxF4d|pX)%##K#+5SQM}+Dkc$`#5>~j~bqm');
define('LOGGED_IN_SALT',   'WY/hIH3[?)4NHpZZ~?oFaiU}qRnrm&:V|(HQ+X7K1mdM9Am|xt}qDc7MT3m-TVHV');
define('NONCE_SALT',       'pn24KmzVH<iW9|>@[|zVrgK703/W}s^H?a?8CF.$)nkGC3w>(Yj)-o]I2 G p#E3');
$table_prefix = 'wp_';
// Debugging settings (ОТКЛЮЧЕНО для продакшена)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 0);
// Performance optimizations
define('WP_MEMORY_LIMIT', '128M');
define('WP_MAX_MEMORY_LIMIT', '256M');
// Auto-update settings
define('WP_AUTO_UPDATE_CORE', 'minor');
// Security settings
define('DISALLOW_FILE_EDIT', true); // Запрет редактирования файлов через админ-панель
if (!defined('EMPTY_TRASH_DAYS')) {
    define('EMPTY_TRASH_DAYS', 7); // Очистка корзины каждые 7 дней
}
// Execution time settings (УВЕЛИЧЕНО для стабильности)
set_time_limit(180); // 3 минуты вместо 30 секунд
@ini_set('max_execution_time', '180');
@ini_set('max_input_time', '180');
// Force HTTPS if behind proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
// Load WordPress settings
require_once ABSPATH . 'wp-settings.php';
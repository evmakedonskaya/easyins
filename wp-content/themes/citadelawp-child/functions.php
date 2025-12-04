<?php
/**
 * functions.php — Citadela Child (ver 22/11/2025)
 */

// ==============================
// 1. БЛОКИРОВКА GOOGLE FONTS
// ==============================

add_action('wp_enqueue_scripts', function() {
    // 1. Блокируем все зарегистрированные стили с Google Fonts
    global $wp_styles;
    foreach ($wp_styles->queue as $handle) {
        $src = $wp_styles->registered[$handle]->src ?? '';
        if (strpos($src, 'fonts.googleapis.com') !== false) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
    }

    // 2. Фильтруем src всех стилей
    add_filter('style_loader_src', function($src, $handle) {
        if (strpos($src, 'fonts.googleapis.com') !== false) {
            return ''; // Лучше вернуть пустую строку, чем false
        }
        return $src;
    }, 9999, 2);

    // 3. Блокируем инлайн-стили с @import Google Fonts
    add_filter('style_loader_tag', function($html, $handle, $href) {
        if (strpos($href, 'fonts.googleapis.com') !== false) {
            return '';
        }
        return $html;
    }, 9999, 3);
}, 999);

// ==============================
// 2. ПОДКЛЮЧЕНИЕ СТИЛЕЙ
// ==============================
add_action('wp_enqueue_scripts', 'my_child_theme_enqueue_styles', 20);
function my_child_theme_enqueue_styles() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['parent-style'],
        wp_get_theme()->get('Version')
    );
}

// ==============================
// 3. СИСТЕМНЫЙ ШРИФТ БЕЗ ЗАСЕЧЕК
// ==============================
add_action('wp_head', function() {
    ?>
    <style>
        body,
        body *:not(script):not(style):not([class*="fa-"]):not([class*="icon"]):not(.dashicons) {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    </style>
    <?php
});


// ==============================
// 4. ОТКЛЮЧЕНИЕ НЕНУЖНЫХ СТИЛЕЙ НА ФРОНТЕНДЕ
// ==============================
add_action('wp_enqueue_scripts', 'disable_unnecessary_frontend_styles', 20);
function disable_unnecessary_frontend_styles() {
    if (is_admin()) {
        return;
    }

    $styles_to_disable = [
        'citadela-author-detail-block-style',
        'citadela-authors-list-block-style',
        'wp-optimize-global',
        'citadela-pro-google-fonts',
        'wordfenceAJAXcss',
        'media-views',
        'citadela-photoswipe-css',
        'imgareaselect'
    ];

    if (!is_user_logged_in()) {
        $styles_to_disable[] = 'admin-bar';
//        $styles_to_disable[] = 'seopress-admin-bar';
    }

    foreach ($styles_to_disable as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}

// ==============================
// 5. ОПТИМИЗАЦИЯ И БЕЗОПАСНОСТЬ
// ==============================

// Удаляем лишние мета-теги
add_action('wp_head', function() {
    if (is_admin()) return;

    $actions_to_remove = [
        // RSS-ленты
        ['feed_links_extra', 3],
        ['feed_links', 2],

        // Сервисные протоколы
        ['rsd_link'],
        ['wlwmanifest_link'],

        // Навигация между постами
        ['start_post_rel_link', 10, 0],
        ['index_rel_link'],
        ['parent_post_rel_link', 10, 0],
        ['adjacent_posts_rel_link_wp_head', 10, 0],
        ['adjacent_posts_rel_link', 10, 0],

        // Прочее
        ['wp_shortlink_wp_head', 10, 0],
        ['wp_generator'],
    ];

    foreach ($actions_to_remove as $action) {
        remove_action('wp_head', ...$action);
    }
}, 1);

// Отключение CSS‑стилей WordPress для оформления Emoji
add_action('init', function() {
    if (is_admin()) {
        return;
    }
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
});

// Скрываем версию WP везде: в HTML, RSS и ресурсах
add_filter('the_generator', '__return_empty_string');
add_filter('rss_generator_tag', '__return_empty_string');

function rem_wp_ver_css_js($src) {
    if (strpos($src, 'admin.php') !== false) {
        return $src; // Не трогаем админ-панель
    }
    return remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'rem_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'rem_wp_ver_css_js', 9999);

// Полное отключение системы комментариев
add_action('init', function() {
    foreach (get_post_types() as $post_type) {
        add_post_type_support($post_type, 'comments', false);
        add_post_type_support($post_type, 'trackbacks', false);
    }
});

// Убираем виджет последних комментариев
add_action('widgets_init', function() {
    unregister_widget('WP_Widget_RecentComments');
});

// Скрываем метабоксы комментариев для всех типов записей
add_action('admin_menu', function() {
    foreach (get_post_types() as $post_type) {
        remove_meta_box('commentstatusdiv', $post_type, 'normal');
        remove_meta_box('commentsdiv', $post_type, 'normal');
    }
});

// Отключаем REST API для комментариев
add_filter('rest_enable_comments', '__return_false');


// Убираем ссылки в админ-баре
add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}, 999);

// Закрываем RSS-ленту комментариев (возвращаем 404)
add_action('template_redirect', function() {
    if (is_comment_feed()) {
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
});

// Отключаем скрипты и стили Raty (рейтинг)
add_action('wp_enqueue_scripts', 'remove_raty_scripts', 999); // 999 вместо 9999 — достаточно
function remove_raty_scripts() {
    $script_handles = [
        'citadela-raty',
        'raty',
        // есть в теме
    ];
    
    $style_handles = [
        'citadela-raty',
        'raty',
        // Только актуальные
    ];

    foreach ($script_handles as $handle) {
        if (wp_script_is($handle)) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
    }

    foreach ($style_handles as $handle) {
        if (wp_style_is($handle)) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
    }
}

// Отключение XML-RPC и связанных мета-ссылок
add_filter('xmlrpc_enabled', '__return_false', 10, 0);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');


// ==============================
// 6. HTML ОПТИМИЗАЦИЯ
// ==============================
// 1. Фильтр WordPress для удаления preload (основной метод)
add_filter('wp_resource_hints', function($urls, $relation_type) {
    if ('preload' !== $relation_type) {
        return $urls;
    }

    foreach ($urls as $key => $url) {
        if (
            stripos($url, 'fontawesome') !== false ||
            stripos($url, 'awesome') !== false
        ) {
            unset($urls[$key]);
        }
    }

    return $urls;
}, 10, 2);

// 2. Буферизация как запасной вариант (если фильтр не сработал)
add_action('template_redirect', function() {
    ob_start(function($html) {
        // Удаляем preload для Font Awesome
        $html = preg_replace(
            '#<link[^>]+rel="preload"[^>]+href="[^"]*(fontawesome|awesome)[^"]*\.(woff|woff2)[^"]*"[^>]*>#i',
            '',
            $html
        );

        // Исправление HTML-ошибок
        $html = preg_replace('/"(?=[a-zA-Z_-]+=)/', '" ', $html); // пробелы между атрибутами
        $html = preg_replace('/sizes="auto,\s*\(/', 'sizes="(', $html); // исправляем sizes
        $html = preg_replace('#<script[^>]+type="speculationrules"[^>]*>.*?</script>#si', '', $html); // удаляем speculationrules


        // Удаляем стили с contain-intrinsic-size
        $html = preg_replace(
            '#<style[^>]*>[^<]*contain-intrinsic-size\s*:\s*3000px\s+1500px[^<]*</style>#is',
            '',
            $html
        );

        return $html;
    });
});

// 3. Безопасное завершение буферизации
add_action('shutdown', function() {
    while (ob_get_level()) {
        ob_end_flush();
    }
});


// ==============================
// 7. ИСПРАВЛЕНИЯ И НАСТРОЙКИ
// ==============================

// Пагинация
add_action('pre_get_posts', function($query) {
    if (!$query->is_main_query() || is_admin()) {
        return;
    }
    $paged = get_query_var('paged') ?: get_query_var('page') ?: ($_GET['paged'] ?? 1);
    if ($paged > 1) {
        $query->set('paged', (int)$paged);
    }
});

add_filter('redirect_canonical', function($redirect, $requested) {
    return !is_admin() && preg_match('/page\/[0-9]+/i', $requested) ? false : $redirect;
}, 10, 2);


// Перевод кнопок пагинации
add_action('wp_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nextButtons = document.querySelectorAll('.page-numbers.next');
            const prevButtons = document.querySelectorAll('.page-numbers.prev');

            if (nextButtons.length) {
                nextButtons.forEach(el => el.textContent = 'Вперёд »');
            }
            if (prevButtons.length) {
                prevButtons.forEach(el => el.textContent = '« Назад');
            }
        });
    </script>
    <?php
});

/**
 * Отключает скрипты SEOPress (metabox и pre‑publish‑checklist) на фронтенде
 */
add_action('wp_enqueue_scripts', function() {
    if (is_admin()) {
        return; // Не выполняем в админ‑панели
    }

    $scripts_to_remove = [
        'seopress-metabox',
        'seopress-pre-publish-checklist'
    ];

    foreach ($scripts_to_remove as $handle) {
        if (wp_script_is($handle, 'enqueued')) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
    }
}, 100);

/**
 * Удаляет избыточные атрибуты type из HTML-тегов <script> и <style>/<link>.
 *
 * - Для <script>: удаляет type="text/javascript", type="application/javascript"
 *   (но сохраняет type="module").
 * - Для <style>/<link>: удаляет type="text/css".
 * Соответствует стандарту HTML5, где type не обязателен.
 */
function sanitize_html_tag_type($html, $tag_name) {
    // Для <script> пропускаем модули и JSON-LD
    if ($tag_name === 'script') {
        if (preg_match('/type=["\']\s*(?:module|application\/(?:ld\+)?json)\s*["\']/i', $html)) {
            return $html;
        }
    }

    // Удаляем type="..." (универсально для любых MIME-типов)
    $html = preg_replace(
        '/\s+type=["\'][^"\']*["\']/i',
        '',
        $html
    );

    // Убираем лишние пробелы вокруг атрибутов
    $html = preg_replace('/\s{2,}/', ' ', $html);
    $html = str_replace(' />', '>', $html);

    return trim($html);
}

// Фильтр для <script>
add_filter('script_loader_tag', function($tag, $handle) {
    return sanitize_html_tag_type($tag, 'script');
}, 10, 2);

// Фильтр для <style> и <link>
add_filter('style_loader_tag', function($html, $handle, $href, $media) {
    return sanitize_html_tag_type($html, 'style');
}, 10, 4);
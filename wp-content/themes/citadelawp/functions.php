<?php

define('CITADELAWP_THEME', true);

// Константы для улучшенной функциональности
if (!defined('CITADELA_LOG_ENABLED')) define('CITADELA_LOG_ENABLED', false);
if (!defined('CITADELA_ALLOW_SVG_UPLOAD')) define('CITADELA_ALLOW_SVG_UPLOAD', true);
if (!defined('CITADELA_LAZY_LOAD_FILES')) define('CITADELA_LAZY_LOAD_FILES', true);

/**
 * ===========================================================================
 * ОПТИМИЗИРОВАННАЯ СИСТЕМА ЗАГРУЗКИ ФАЙЛОВ
 * ===========================================================================
 */

if (!function_exists('citadela_safe_require')) {
    /**
     * Безопасное подключение файлов с проверкой существования
     */
    function citadela_safe_require($file_path) {
        static $loaded_files = [];
        
        // Проверяем, не загружен ли уже файл
        if (isset($loaded_files[$file_path])) {
            return true;
        }
        
        // Проверяем существование файла
        if (!file_exists($file_path)) {
            if (CITADELA_LOG_ENABLED) {
                error_log("Citadela: File not found - {$file_path}");
            }
            return false;
        }
        
        $result = require_once $file_path;
        $loaded_files[$file_path] = true;
        
        return $result !== false;
    }
}

if (!function_exists('citadela_lazy_load_file')) {
    /**
     * Ленивая загрузка файлов по требованию
     */
    function citadela_lazy_load_file($file_path, $condition_callback = null) {
        static $lazy_loaded = [];
        
        $file_key = md5($file_path);
        
        // Если файл уже загружен или условие не выполнено
        if (isset($lazy_loaded[$file_key]) || 
            ($condition_callback && !call_user_func($condition_callback))) {
            return false;
        }
        
        if (citadela_safe_require($file_path)) {
            $lazy_loaded[$file_key] = true;
            return true;
        }
        
        return false;
    }
}

/**
 * ===========================================================================
 * ОПТИМИЗИРОВАННАЯ ИНИЦИАЛИЗАЦИЯ ТЕМЫ
 * ===========================================================================
 */

if (!function_exists('citadela_theme_setup')) :
    function citadela_theme_setup() {
        // Ленивая загрузка текстового домена
        if (CITADELA_LAZY_LOAD_FILES) {
            add_action('after_setup_theme', function() {
                load_theme_textdomain('citadela', get_template_directory() . '/languages');
            }, 1);
        } else {
            load_theme_textdomain('citadela', get_template_directory() . '/languages');
        }

        // Добавляем поддержку функций темы
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');

        // Безопасная загрузка основного класса темы
        $citadela_theme_file = get_template_directory() . '/citadela-theme/CitadelaTheme.php';
        if (file_exists($citadela_theme_file)) {
            require_once $citadela_theme_file;
            if (class_exists('Citadela_Theme')) {
                Citadela_Theme::get_instance()->run(__FILE__);
            }
        }

        // Регистрация меню
        register_nav_menus(array(
            'main-menu' => esc_html__('Main menu', 'citadela'),
            'footer-menu' => esc_html__('Footer menu', 'citadela'),
        ));
        
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
        
        add_theme_support('custom-logo', array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height'  => true,
        ));
        
        add_theme_support('align-wide');

        if (!isset($content_width)) {
            $content_width = 768;
        }
    }
endif;

add_action('after_setup_theme', 'citadela_theme_setup');

/**
 * ===========================================================================
 * ОПТИМИЗИРОВАННАЯ ЗАГРУЗКА СИСТЕМНЫХ ФАЙЛОВ
 * ===========================================================================
 */

// Функция для безопасной загрузки системных файлов
if (!function_exists('citadela_load_system_files')) {
    function citadela_load_system_files() {
        $template_dir = get_template_directory();
        $files_to_load = [];
        
        // Основные системные файлы (загружаются всегда)
        $required_files = [
            '/citadela-theme/compatibility.php',
            '/citadela-theme/template-tags.php',
            '/citadela-theme/widgets.php',
            '/citadela-theme/customizer.php',
            '/citadela-theme/config/paths.php',
            '/citadela-theme/Citadela.php'
        ];
        
        foreach ($required_files as $file) {
            $full_path = $template_dir . $file;
            if (file_exists($full_path)) {
                citadela_safe_require($full_path);
            }
        }
        
        // Проверка совместимости PHP и WordPress
        if (function_exists('citadela_support_php') && function_exists('citadela_support_wp')) {
            if (!citadela_support_php() || !citadela_support_wp()) {
                return;
            }
        }
        
        // Загрузка поддержки плагинов AIT (если функция существует)
        if (function_exists('citadela_handle_ait_plugins_support')) {
            citadela_handle_ait_plugins_support();
        }
        
        // Загрузка конфигурации (если функция paths существует)
        if (function_exists('citadela_paths')) {
            $config_file = citadela_paths()->dir->config . '/config.php';
            if (file_exists($config_file)) {
                citadela_safe_require($config_file);
            }
        }
    }
}

// Загружаем системные файлы
citadela_load_system_files();

/**
 * ===========================================================================
 * ОПТИМИЗИРОВАННОЕ ПОДКЛЮЧЕНИЕ СКРИПТОВ И СТИЛЕЙ
 * ===========================================================================
 */

if (!function_exists('citadela_enqueue_admin_assets')) {
    function citadela_enqueue_admin_assets() {
        $script_path = get_template_directory() . '/design/js/citadela.js';
        $script_url = get_template_directory_uri() . '/design/js/citadela.js';
        
        // Проверяем существование файла перед подключением
        if (file_exists($script_path)) {
            $version = filemtime($script_path);
            wp_enqueue_script('citadela-script', $script_url, [], $version, true);
        }
    }
}
add_action('admin_enqueue_scripts', 'citadela_enqueue_admin_assets');

/**
 * ===========================================================================
 * УЛУЧШЕННАЯ ОБРАБОТКА SVG ФАЙЛОВ С ПРОВЕРКАМИ
 * ===========================================================================
 */

if (!function_exists('citadela_handle_svg_upload')) {
    function citadela_handle_svg_upload($file) {
        // Проверяем, что это SVG и пользователь имеет права
        if (!isset($file['type']) || 
            $file['type'] !== 'image/svg+xml' || 
            !current_user_can('upload_files') ||
            !CITADELA_ALLOW_SVG_UPLOAD) {
            return $file;
        }
        
        // Проверка существования временного файла
        if (!isset($file['tmp_name']) || 
            !file_exists($file['tmp_name']) || 
            !is_uploaded_file($file['tmp_name'])) {
            $file['error'] = 'Ошибка безопасности файла.';
            return $file;
        }
        
        try {
            // Безопасное чтение файла
            $svg_content = file_get_contents($file['tmp_name']);
            if ($svg_content === false) {
                throw new Exception('Не удалось прочитать файл');
            }
            
            // Проверка размера файла (максимум 2MB)
            if (strlen($svg_content) > 2 * 1024 * 1024) {
                throw new Exception('SVG файл слишком большой. Максимальный размер: 2MB');
            }
            
            // Валидация SVG через DOMDocument если доступно
            if (class_exists('DOMDocument') && function_exists('libxml_use_internal_errors')) {
                libxml_use_internal_errors(true);
                $dom = new DOMDocument();
                
                $loaded = $dom->loadXML($svg_content);
                
                if (!$loaded) {
                    $xml_errors = libxml_get_errors();
                    $error_messages = [];
                    foreach (array_slice($xml_errors, 0, 5) as $error) {
                        $error_messages[] = "Line {$error->line}: {$error->message}";
                    }
                    libxml_clear_errors();
                    throw new Exception('Невалидный SVG: ' . implode('; ', $error_messages));
                }
                libxml_clear_errors();
            }
            
        } catch (Exception $e) {
            $file['error'] = 'Ошибка SVG: ' . $e->getMessage();
        }
        
        return $file;
    }
    
    // Подключаем обработчик ТОЛЬКО если нужно обрабатывать SVG
    if (CITADELA_ALLOW_SVG_UPLOAD) {
        add_filter('wp_handle_upload_prefilter', 'citadela_handle_svg_upload');
    }
}

/**
 * ===========================================================================
 * ОПТИМИЗИРОВАННОЕ ИСПРАВЛЕНИЕ ПАГИНАЦИИ
 * ===========================================================================
 */

if (!function_exists('citadela_fix_pagination_query')) {
    function citadela_fix_pagination_query($query) {
        if (!$query->is_main_query() || is_admin()) {
            return;
        }
        
        $paged = false;
        
        // Исправляем paged для главной страницы и архивов
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        }
        // Исправляем page для статических страниц
        elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        }
        // Принудительно устанавливаем paged из GET параметров
        elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
            $paged = intval($_GET['paged']);
        }
        
        if ($paged) {
            $query->set('paged', $paged);
        }
    }
}
add_action('pre_get_posts', 'citadela_fix_pagination_query');

if (!function_exists('citadela_disable_pagination_redirect')) {
    function citadela_disable_pagination_redirect($redirect_url, $requested_url) {
        // Отключаем редиректы для всех случаев пагинации
        if (preg_match('/[?&](paged|page)=/', $requested_url)) {
            return false;
        }
        
        // Отключаем редиректы для поиска с пагинацией
        if (preg_match('/[?&]s=.*[?&]paged=/', $requested_url)) {
            return false;
        }
        
        return $redirect_url;
    }
}
add_filter('redirect_canonical', 'citadela_disable_pagination_redirect', 10, 2);

/**
 * ===========================================================================
 * ДОПОЛНИТЕЛЬНЫЕ ОПТИМИЗАЦИИ
 * ===========================================================================
 */

// Кэширование путей для уменьшения запросов к файловой системе
if (!function_exists('citadela_get_cached_template_path')) {
    function citadela_get_cached_template_path($relative_path) {
        static $path_cache = [];
        
        if (isset($path_cache[$relative_path])) {
            return $path_cache[$relative_path];
        }
        
        $full_path = locate_template($relative_path);
        $path_cache[$relative_path] = $full_path;
        
        return $full_path;
    }
}

// Оптимизированная проверка существования функций
if (!function_exists('citadela_function_exists')) {
    function citadela_function_exists($function_name) {
        static $function_cache = [];
        
        if (isset($function_cache[$function_name])) {
            return $function_cache[$function_name];
        }
        
        $exists = function_exists($function_name);
        $function_cache[$function_name] = $exists;
        
        return $exists;
    }
}
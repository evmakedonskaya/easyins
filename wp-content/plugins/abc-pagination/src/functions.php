<?php

namespace Wpshop\AbcPagination;

use Generator;
use WP_Post;
use WP_Term;
use Wpshop\AbcPagination\Admin\MenuPage;
use Wpshop\AbcPagination\Admin\Settings;
use Wpshop\AbcPagination\Admin\SettingsWelcome;
use WPShop\Container\Container;

const META_TITLE_FOR_SORT = '_post_title_for_sort';

const VIEW_TYPE_LIST  = 'list';
const VIEW_TYPE_TABS  = 'tabs';
const VIEW_TYPE_POPUP = 'popup';

/**
 * @return Container
 */
function container() {
    static $container;
    if ( ! $container ) {
        $init      = require_once dirname( __DIR__ ) . '/config/container.php';
        $config    = require_once dirname( __DIR__ ) . '/config/config.php';
        $container = new Container( $init( $config ) );
    }

    return $container;
}

/**
 * @return void
 */
function init_i18n() {
    $text_domain = 'abc-pagination';
    $locale      = ( is_admin() && function_exists( 'get_user_locale' ) ) ? get_user_locale() : get_locale();
    $mo_file     = dirname( ABC_PAGINATION_FILE ) . "/languages/{$text_domain}-{$locale}.mo";
    if ( file_exists( $mo_file ) ) {
        load_textdomain( $text_domain, $mo_file );
    }
}

/**
 * @param string $plugin
 *
 * @return void
 */
function redirect_on_activated( $plugin ) {
    if ( $plugin === ABC_PAGINATION_BASENAME && ! container()->get( Settings::class )->verify() ) {
        $wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
        $action        = $wp_list_table->current_action();
        if ( $action === 'activate' ) {
            wp_redirect( get_settings_page_url() );
            die;
        }
    }
}

/**
 * @param array $actions
 *
 * @return array
 */
function add_settings_plugin_action( $actions ) {
    array_unshift( $actions, sprintf( '<a href="%s">%s</a>', get_settings_page_url(), __( 'Settings', 'abc-pagination' ) ) );

    return $actions;
}

/**
 * @return void
 * @see \register_activation_hook()
 */
function activate() {
}

/**
 * @return void
 */
function uninstall() {
    $settings = container()->get( Settings::class );
    if ( $settings->get_value( 'clear_database' ) ) {
        $settings->clear_database();
        container()->get( SettingsWelcome::class )->clear_database();
    }
}

/**
 * @param string    $json
 * @param bool|null $associative
 * @param int       $depth
 * @param int       $flags
 *
 * @return mixed|null
 * @see   \json_decode()
 * @since 1.2.1
 */
function json_decode( $json, $associative = null, int $depth = 512, int $flags = 0 ) {
    $result = \json_decode( $json, $associative, $depth, $flags );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        trigger_error( 'Json parse error: ' . json_last_error_msg(), E_USER_WARNING );

        return null;
    }

    return $result;
}


/**
 * @return string
 */
function get_settings_page_url() {
    return add_query_arg( 'page', MenuPage::SETTINGS_SLUG, admin_url( 'options-general.php' ) );
}

/**
 * @return string
 */
function get_ip() {
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

/**
 * @param string $plugin
 *
 * @return bool
 */
function is_plugin_active( $plugin ) {
    $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;

    if ( in_array( $plugin_path, \wp_get_active_and_valid_plugins() ) ||
         in_array( $plugin_path, \wp_get_active_network_plugins() )
    ) {
        return true;
    }

    return false;
}

/**
 * @param string $template
 *
 * @return bool
 */
function is_theme( $template ) {
    $theme = wp_get_theme();
    if ( $theme->parent() ) {
        $theme = $theme->parent();
    }

    return $template === $theme->get_template();
}

/**
 * @param callable $fn
 *
 * @return false|string
 * @throws \Exception
 */
function ob_get_content( $fn ) {
    try {
        $ob_level = ob_get_level();
        ob_start();
        ob_implicit_flush( false );

        $args = func_get_args();
        call_user_func_array( $fn, array_slice( $args, 1 ) );

        return ob_get_clean();

    } catch ( \Exception $e ) {
        while ( ob_get_level() > $ob_level ) {
            if ( ! @ob_end_clean() ) {
                ob_clean();
            }
        }
        throw $e;
    }
}

/**
 * @param string $slug
 * @param string $name
 * @param array  $args
 *
 * @return false|void
 * @see \get_template_part()
 */
function get_template_part( $slug, $name = null, $args = [] ) {
    do_action( "get_template_part_{$slug}", $slug, $name, $args );

    $templates = [];
    $name      = (string) $name;
    if ( '' !== $name ) {
        $templates[] = "{$slug}-{$name}.php";
    }

    $templates[] = "{$slug}.php";

    do_action( 'get_template_part', $slug, $name, $templates, $args );

    if ( ! locate_template( $templates, true, false, $args ) ) {
        return false;
    }
}

/**
 * @param string|array $template_names
 * @param bool         $load
 * @param bool         $require_once
 * @param array        $args
 *
 * @return string|null
 * @see \locate_template()
 */
function locate_template( $template_names, $load = false, $require_once = true, $args = [] ) {
    $located = null;
    foreach ( (array) $template_names as $template_name ) {
        if ( ! $template_name ) {
            continue;
        }

        // prevent to locate admin templates from other places
        if ( 'admin/' === substr( $template_name, 0, 6 ) ) {
            if ( file_exists( dirname( ABC_PAGINATION_FILE ) . '/template-parts/' . $template_name ) ) {
                $located = dirname( ABC_PAGINATION_FILE ) . '/template-parts/' . $template_name;
                break;
            }
            continue;
        }

        if ( file_exists( STYLESHEETPATH . '/' . ABC_PAGINATION_SLUG . '/' . $template_name ) ) {
            $located = STYLESHEETPATH . '/' . ABC_PAGINATION_SLUG . '/' . $template_name;
            break;
        } elseif ( file_exists( TEMPLATEPATH . '/' . ABC_PAGINATION_SLUG . '/' . $template_name ) ) {
            $located = TEMPLATEPATH . '/' . ABC_PAGINATION_SLUG . '/' . $template_name;
            break;
        } elseif ( file_exists( dirname( ABC_PAGINATION_FILE ) . '/template-parts/' . $template_name ) ) {
            $located = dirname( ABC_PAGINATION_FILE ) . '/template-parts/' . $template_name;
            break;
        }
    }

    $located = apply_filters( 'abc_pagination/locate_template/located', $located, $template_name, $args );
    if ( ! file_exists( $located ) ) {
        trigger_error( 'Unable to locate template file ' . $located );

        return null;
    }


    if ( $load && '' !== $located ) {
        load_template( $located, $require_once, $args );
    }

    return $located;
}

/**
 * @param string $type
 *
 * @return string|null
 */
function doc_link( $type ) {
    switch ( $type ) {
        case 'doc':
            return 'https://support.wpshop.ru/docs/plugins/' . ABC_PAGINATION_SLUG;
        case 'faq':
            return 'https://support.wpshop.ru/fag_tag/' . ABC_PAGINATION_SLUG . '/';
        case 'video':
            // todo add youtube video
            return 'https://www.youtube.com/watch?v=22ahf1M1TGM';
        default:
            return null;
    }
}



/**
 * @return array
 */
function get_short_title_post_types() {
    return (array) apply_filters(
        'abc_pagination/meta_boxes/post_types',
        (array) container()->get( Settings::class )->get_value( 'post_types' )
    );
}

/**
 * @param $items1
 * @param $items2
 *
 * @return int
 */
function posts_sort_callback( $items1, $items2 ) {

    $title1 = get_title_from_item( $items1 );
    $title2 = get_title_from_item( $items2 );

    $title1 = str_replace( [ 'ё', 'Ё' ], [ 'е', 'Е' ], $title1 );
    $title2 = str_replace( [ 'ё', 'Ё' ], [ 'е', 'Е' ], $title2 );

    // remove nbsp; (\xC2\xA0)
    $title1 = trim( $title1, " \n\r\t\v\x00\xC2\xA0" );
    $title1 = trim_wrong_chars( $title1 );
    $title2 = trim( $title2, " \n\r\t\v\x00\xC2\xA0" );
    $title2 = trim_wrong_chars( $title2 );

    $title1 = apply_filters( 'abc_pagination/functions/sort_title', $title1 );
    $title2 = apply_filters( 'abc_pagination/functions/sort_title', $title2 );

    return strcmp( mb_strtoupper( $title1, 'UTF-8' ), mb_strtoupper( $title2, 'UTF-8' ) );
}

/**
 * @param $items
 *
 * @return array
 */
function transform_posts_structure( array $items, $alphabet ) {
    $alphabet = mb_strtoupper( $alphabet, 'UTF-8' );

    $result = [];
    foreach ( $items as $item ) {

        $title = get_title_from_item( $item );

        $title = apply_filters( 'abc_pagination/functions/sort_title', $title );

        if ( $title ) {

            // remove nbsp; (\xC2\xA0)
            $title = trim( $title, " \n\r\t\v\x00\xC2\xA0" );
            $title = trim_wrong_chars( $title );

            $first_letter = mb_substr( $title, 0, 1, 'UTF-8' );
            $first_letter = mb_strtoupper( $first_letter, 'UTF-8' );

            if ( apply_filters( 'abc_pagination/functions/combine_letter_e', true ) ) {
                $first_letter = str_replace( 'Ё', 'Е', $first_letter );
            }

        } else {
            continue;
        }

        if ( $alphabet &&
             apply_filters( 'abc_pagination/functions/strict_match_alphabet', true ) &&
             false === mb_strpos( $alphabet, $first_letter, 0, 'UTF-8' )
        ) {
            continue; // skip adding first letter to list
        }

        $result[ $first_letter ][] = $item;
    }

    if ( $alphabet ) {
        uksort( $result, function ( $a, $b ) use ( $alphabet ) {
            $items_a = mb_strpos( $alphabet, mb_strtoupper( $a, 'UTF-8' ), 0, 'UTF-8' );
            $items_b = mb_strpos( $alphabet, mb_strtoupper( $b, 'UTF-8' ), 0, 'UTF-8' );

            if ( false === $items_a && false === $items_b ) {
                return strcmp( $a, $b );
            }

            if ( false === $items_a ) {
                $items_a = mb_strlen( $alphabet, 'UTF-8' ) + 1;
            }
            if ( false === $items_b ) {
                $items_b = mb_strlen( $alphabet, 'UTF-8' ) + 1;
            }

            return $items_a - $items_b;
        } );
    }

    return $result;
}

/**
 * @param mixed $display
 * @param mixed $current
 * @param bool  $echo
 *
 * @return string
 */
function displayed( $display, $current = true, $echo = true ) {
    if ( (string) $display === (string) $current ) {
        $result = '';
    } else {
        $result = ' style="display:none"';
    }

    if ( $echo ) {
        echo $result;
    }

    return $result;
}

/**
 * @param string $name icon type
 *
 * @return string|null
 * @deprecated
 */
function get_tab_icon( $name ) {
    return [
               'dashboard'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M273.27 7.08A24.14 24.14 0 0 0 256.09 0c-6.17-.02-12.35 2.32-17.06 7.03l-232 232c-9.37 9.37-9.37 24.57 0 33.94C11.72 277.66 17.86 280 24 280s12.28-2.34 16.97-7.03L64 249.94V464c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16V250.19l22.73 22.73c4.72 4.72 10.91 7.08 17.09 7.08s12.37-2.36 17.09-7.08c9.44-9.44 9.44-24.75 0-34.19M399.99 133.81l-32-32M224 432V304h64v128h-64Zm176 0h-64V272c0-8.84-7.16-16-16-16H192c-8.84 0-16 7.16-16 16v160h-64V201.94L255.88 58.06 400 202.18v229.81Z" fill="currentColor"></path></svg>',
               'dashboard-activate' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M273.27 7.08A24.14 24.14 0 0 0 256.09 0c-6.17-.02-12.35 2.32-17.06 7.03l-232 232c-9.37 9.37-9.37 24.57 0 33.94C11.72 277.66 17.86 280 24 280s12.28-2.34 16.97-7.03L64 249.94V464c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16V250.19l22.73 22.73c4.72 4.72 10.91 7.08 17.09 7.08s12.37-2.36 17.09-7.08c9.44-9.44 9.44-24.75 0-34.19M399.99 133.81l-32-32M224 432V304h64v128h-64Zm176 0h-64V272c0-8.84-7.16-16-16-16H192c-8.84 0-16 7.16-16 16v160h-64V201.94L255.88 58.06 400 202.18v229.81Z" fill="currentColor"></path></svg>',
               'appearance'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M176 263c48.52 0 88-39.48 88-88s-39.48-88-88-88-88 39.48-88 88 39.48 88 88 88Zm0-128c22.06 0 40 17.94 40 40s-17.94 40-40 40-40-17.94-40-40 17.94-40 40-40ZM432 0H80C35.89 0 0 35.89 0 80v352c0 44.11 35.89 80 80 80h352c44.11 0 80-35.89 80-80V80c0-44.11-35.89-80-80-80ZM80 48h352c17.67 0 32 14.33 32 32v209.01l-76.82-78.77a24 24 0 0 0-17.1-7.24H370c-6.41 0-12.56 2.57-17.07 7.13L196.44 368.44l-66.36-67.29a24.02 24.02 0 0 0-17.07-7.15h-.02a24.05 24.05 0 0 0-17.07 7.12l-47.93 48.47V80c0-17.67 14.33-32 32-32ZM48 432v-14.14l64.98-65.71 49.72 50.42-60.71 61.42H80c-17.67 0-32-14.33-32-32Zm384 32H169.48l200.4-202.74L464 357.77v74.24c0 17.67-14.33 32-32 32Z" fill="currentColor"></path></svg>',
               'settings'           => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256.01 209.36c12.96 0 25.24 4.27 33.69 11.7 9.07 7.98 13.87 19.73 14.29 34.93-.42 15.21-5.23 26.96-14.3 34.94-8.46 7.44-20.74 11.7-33.69 11.7s-25.24-4.27-33.7-11.7c-9.07-7.98-13.87-19.73-14.29-34.93.42-15.21 5.23-26.96 14.3-34.93 8.46-7.44 20.74-11.7 33.7-11.7M332.28 0H179.72v78.23a189.922 189.922 0 0 0-36.88 21.69l-66.6-39.13L0 195.17l66.58 39.12a198.125 198.125 0 0 0 0 43.41L0 316.83l76.24 134.39 66.6-39.13a189.419 189.419 0 0 0 36.88 21.69v78.23h152.56v-78.23c13.05-5.8 25.41-13.08 36.88-21.69l66.6 39.13L512 316.83l-66.58-39.12c.79-7.23 1.19-14.5 1.19-21.71s-.4-14.48-1.19-21.71L512 195.17 435.76 60.79l-66.6 39.13a189.419 189.419 0 0 0-36.88-21.69V0ZM146.65 155.93c16.34-13.16 35.37-29.23 55.09-36.62l23.83-9.82V46.55h60.88v62.95l23.83 9.82c19.67 7.36 38.8 23.5 55.09 36.62l53.61-31.5 30.48 53.72-53.59 31.49c1.62 12.88 5.23 33.6 4.93 46.36.31 12.67-3.32 33.6-4.93 46.36l53.59 31.49-30.48 53.72-53.61-31.5c-16.34 13.16-35.37 29.23-55.09 36.62l-23.83 9.82v62.95h-60.88v-62.95l-23.83-9.82c-19.67-7.36-38.8-23.5-55.09-36.62l-53.61 31.5-30.48-53.72 53.59-31.49c-1.62-12.88-5.23-33.6-4.93-46.36-.31-12.67 3.32-33.6 4.93-46.36l-53.59-31.49 30.48-53.72 53.61 31.5ZM256 161.36c-47.46 0-94.92 31.55-96 94.63 1.07 63.1 48.53 94.64 96 94.64s94.92-31.55 96-94.64c-1.07-63.09-48.53-94.64-96-94.64Z" fill="currentColor"></path></svg>',
               'css'                => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M10.06 12.06 6.12 16l3.94 3.94-2.12 2.12L1.88 16l6.06-6.06 2.12 2.12Zm14-2.12-2.12 2.12L25.88 16l-3.94 3.94 2.12 2.12L30.12 16l-6.06-6.06ZM12.03 27.18l2.93.64 5.01-23-2.93-.64-5 23Z" fill="currentColor"></path></svg>',
           ][ $name ] ?? null;
}

/**
 * @param array $arr
 *
 * @return void
 */
function ksort_recursive( &$arr ) {
    foreach ( $arr as $value ) {
        if ( is_array( $value ) ) {
            ksort_recursive( $value );
        }
        ksort( $arr );
    }
}

/**
 * @param $items
 *
 * @return string
 * @since 1.2.0
 */
function get_post_anchor( $title, $items ) {
    return $items['type'] . '-' . transliterate( $title );
}

/**
 * @param string $string
 * @param bool   $file is file name
 *
 * @return string
 */
function transliterate( $string, $file = false ) {
    $list   = [
        'Ä' => 'Ae',
        'ä' => 'ae',
        'Æ' => 'Ae',
        'æ' => 'ae',
        'À' => 'A',
        'à' => 'a',
        'Á' => 'A',
        'á' => 'a',
        'Â' => 'A',
        'â' => 'a',
        'Ã' => 'A',
        'ã' => 'a',
        'Å' => 'A',
        'å' => 'a',
        'ª' => 'a',
        'ₐ' => 'a',
        'ā' => 'a',
        'Ć' => 'C',
        'ć' => 'c',
        'Ç' => 'C',
        'ç' => 'c',
        'Ð' => 'D',
        'đ' => 'd',
        'È' => 'E',
        'è' => 'e',
        'É' => 'E',
        'é' => 'e',
        'Ê' => 'E',
        'ê' => 'e',
        'Ë' => 'E',
        'ë' => 'e',
        'ₑ' => 'e',
        'ƒ' => 'f',
        'ğ' => 'g',
        'Ğ' => 'G',
        'Ì' => 'I',
        'ì' => 'i',
        'Í' => 'I',
        'í' => 'i',
        'Î' => 'I',
        'î' => 'i',
        'Ï' => 'Ii',
        'ï' => 'ii',
        'ī' => 'i',
        'ı' => 'i',
        'I' => 'I',
        'Ñ' => 'N',
        'ñ' => 'n',
        'ⁿ' => 'n',
        'Ò' => 'O',
        'ò' => 'o',
        'Ó' => 'O',
        'ó' => 'o',
        'Ô' => 'O',
        'ô' => 'o',
        'Õ' => 'O',
        'õ' => 'o',
        'Ø' => 'O',
        'ø' => 'o',
        'ₒ' => 'o',
        'Ö' => 'Oe',
        'ö' => 'oe',
        'Œ' => 'Oe',
        'œ' => 'oe',
        'ß' => 'ss',
        'Š' => 'S',
        'š' => 's',
        'ş' => 's',
        'Ş' => 'S',
        'Ù' => 'U',
        'ù' => 'u',
        'Ú' => 'U',
        'ú' => 'u',
        'Û' => 'U',
        'û' => 'u',
        'Ü' => 'Ue',
        'ü' => 'ue',
        'Ý' => 'Y',
        'ý' => 'y',
        'ÿ' => 'y',
        'Ž' => 'Z',
        'ž' => 'z',
        '⁰' => '0',
        '¹' => '1',
        '²' => '2',
        '³' => '3',
        '⁴' => '4',
        '⁵' => '5',
        '⁶' => '6',
        '⁷' => '7',
        '⁸' => '8',
        '⁹' => '9',
        '₀' => '0',
        '₁' => '1',
        '₂' => '2',
        '₃' => '3',
        '₄' => '4',
        '₅' => '5',
        '₆' => '6',
        '₇' => '7',
        '₈' => '8',
        '₉' => '9',
        '±' => '-',
        '×' => 'x',
        '₊' => '-',
        '₌' => '=',
        '⁼' => '=',
        '⁻' => '-',
        '₋' => '-',
        '–' => '-',
        '—' => '-',
        '‑' => '-',
        '․' => '.',
        '‥' => '..',
        '…' => '...',
        '‧' => '.',
        ' ' => '-',
        ' ' => '-',
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'YO',
        'Ж' => 'ZH',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'Y',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'TS',
        'Ч' => 'CH',
        'Ш' => 'SH',
        'Щ' => 'SCH',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'YU',
        'Я' => 'YA',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
    ];
    $string = html_entity_decode( $string, ENT_QUOTES, 'utf-8' );

    $string = strtr( $string, $list );
    $string = strtolower( $string );
    $string = preg_replace( "/[^A-Za-z0-9-_.]/", '-', $string );
    $string = preg_replace( '~([=+.-])\\1+~', '\\1', $string );

    if ( ! $file ) {
        $string = str_replace( '.', '-', $string );
        $string = preg_replace( '/-{2,}/', '-', $string );
    }

    $string = trim( $string, '-' );

    return $string;
}

/**
 * @param $string
 *
 * @return string
 * @since 1.3.0
 */
function trim_wrong_chars( $string ) {
    return trim( $string, apply_filters( 'abc_pagination/functions/wrong_chars', "«‹»›„“‟”’\"❝❞❮❯〝〞〟＂‚‘‛❛❜" ) );
}


/**
 * Возвращает заголовок из переданного массива элемента.
 * Учитывает короткий заголовок или кастомный, который может быть сохранен в мета-поле.
 *
 * @param array $item An associative array possibly containing 'title', 'id', and 'object' keys.
 *                    The 'title' key should be a string representing the default title.
 *                    The 'id' key should be the item's ID (integer).
 *                    The 'object' key can be an instance of WP_Post or WP_Term, based on the item's type.
 * @param bool  $use_short_title Optional. Whether to prioritize short titles if available. Default is true.
 *
 * @return string The resolved title from the provided item array.
 */
function get_title_from_item( array $item, bool $use_short_title = true ): string {
    $default_title = isset( $item['title'] ) ? (string) $item['title'] : '';
    $id            = isset( $item['id'] ) ? (int) $item['id'] : 0;

    if ( $id <= 0 ) {
        return $default_title;
    }

    $title = $default_title;


    // Используем короткий заголовок, если он есть и разрешено его использовать
    // Если вдруг для вывода в шаблоне нужен всегда полный заголовок, то можно передать false вторым параметром
    if ( $use_short_title ) {
        if ( isset( $item['object'] ) && $item['object'] instanceof WP_Post ) {
            $meta = get_post_meta( $id, META_TITLE_FOR_SORT, true );
        } elseif ( isset( $item['object'] ) && $item['object'] instanceof WP_Term ) {
            $meta = get_term_meta( $id, META_TITLE_FOR_SORT, true );
        }

        if ( isset( $meta ) && $meta !== '' ) {
            $title = (string) $meta;
        }
    }

    return $title;
}

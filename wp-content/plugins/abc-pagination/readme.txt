=== ABC Pagination ===

Stable tag:        1.3.2
Requires at least: 5.6
Tested up to:      6.8.3
Requires PHP:      7.2
License:           WPShop License
License URI:       https://wpshop.ru/license
Contributors:      wpshopbiz
Tags:              alphabetic, alphabetic index, alphabetic list, alphabetic pagination, abc, abc pagination

Alphabetical index for WordPress.

== Description ==

Allows you to quickly create a directory of alphabetically sorted records. Many ready-made styles and output formats.

== Installation ==

1. Upload `abc-pagination` to the `/wp-content/abc-pagination/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 1.3.2 - 2025-10-14 =
* Добавлено: новые атрибуты для шорткода, соответствующие аргументам  WP_Term_Query для более точной выборки:
  exclude_tree, hide_empty, child_of, parent


= 1.3.1 - 2025-09-30 =
* Исправлено: проблема с совместимостью внутренней библиотеки

= 1.3.0 - 2025-09-29 =
* Добавлено: новые атрибуты для шорткода, соответствующие аргументам WP_Query для более точной выборки:
  category__and, category__in, category__not_in, tag__and, tag__in, tag__not_in, tag_slug__and, tag_slug__in, author__in, author__not_in, post_parent__in, post_parent__not_in, post__in, post__not_in, post_name__in, post_status
* Добавлено: атрибут шорткода "taxonomy" для вывода таксономий
* Добавлено: работа шорткодов в описании постов алфавитного указателя


= 1.2.2 - 2024-10-03 =
* Обновлено: страница настроек
* Исправлено: совместимость с php8

= 1.2.1 - 2023-07-07 =
* Исправлено: аяксовая подгрузка алфавитного указателя

= 1.2.0 - 2023-07-04 =
* Добавлено: атрибут шорткода "ajax" для аяксовой подрузки
* Добавлено: атрибут шорткода "show_search" для включения поиска
* Добавлено: атрибут шорткода "show_posts_limit" для ограничения максимального количества постов и показа ссылки "показать еще"
* Добавлено: атрибут шорткода "posts_show_id", для вывода атрибута id у терминов глоссария и добавления якоря в url
* Исправлено: удаление плагина

= 1.1.0 - 2023-04-05 =
* Добавлено: пресеты
* Добавлено: 2 новых атрибута в шорткод (letters_text_before и letters_text_after) для вывода текста до и после списка букв
* Добавлено: атрибут alphabet в шорткод для настройки порядка сортировки https://support.wpshop.ru/faq/abc-pagination-alphabet/
* Добавлено: атрибуты letter_hover_color, letter_hover_background и letter_hover_effect в шорткод для эффекта при наведении на букву
* Добавлено: замена страницы метки на алфавитный указатель по аналогии со страницей категории
* Добавлено: вывод <title> и поддержка плагинов Yoast SEO и Rank Math SEO на страницах категорий и меток, если они заменяются на алфавитный указатель
* Добавлено: опция для "Типы записей"
* Добавлено: опции для эффекта при наведении на букву в списке
* Добавлено: опции для настройки рамки у букв
* Исправлено: атрибут шорткода "tax_query"
* Исправлено: работа с неразрывными пробелами (nbsp), теперь корректно сортируются и выводятся
* Исправлено: замена страницы в дочерней категории
* Исправлено: мелкие фиксы по верстке

= 1.0.0 - 2023-02-27 =
* Релиз

= 0.9.0 - 2023-02-16 =
* Первая бета

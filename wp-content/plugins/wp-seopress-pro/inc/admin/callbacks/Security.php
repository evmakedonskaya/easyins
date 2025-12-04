<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_advanced_security_metaboxe_sdt_role_callback() {
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    $options = get_option('seopress_advanced_option_name');

    global $wp_roles;

    if ( ! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    foreach ($wp_roles->get_names() as $key => $value) {
        $check = isset($options['seopress_advanced_security_metaboxe_sdt_role'][$key]); ?>
    <p>
        <label
            for="seopress_advanced_security_metaboxe_sdt_role_<?php echo esc_attr($key); ?>">
            <input
                id="seopress_advanced_security_metaboxe_sdt_role_<?php echo esc_attr($key); ?>"
                name="seopress_advanced_option_name[seopress_advanced_security_metaboxe_sdt_role][<?php echo esc_attr($key); ?>]"
                type="checkbox" <?php if ('1' == $check) { ?>
            checked="yes"
            <?php } ?>
            value="1"/>

            <strong><?php echo esc_html($value); ?></strong> (<em><?php echo esc_html(translate_user_role($value,  'default')); ?>)</em>
        </label>

    </p>

    <?php if (isset($options['seopress_advanced_security_metaboxe_sdt_role'][$key])) {
            esc_attr($options['seopress_advanced_security_metaboxe_sdt_role'][$key]);
        }
    }
echo seopress_tooltip_link($docs['security']['metaboxe_data_types'], esc_html__('Hook to filter Structured data types metabox call by post type - new window', 'wp-seopress-pro'));
}


function seopress_advanced_security_ga_widget_role_callback() {
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    $options = get_option('seopress_advanced_option_name');

    global $wp_roles;

    if ( ! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    foreach ($wp_roles->get_names() as $key => $value) {
        $check = isset($options['seopress_advanced_security_ga_widget_role'][$key]); ?>
    <p>
        <label
            for="seopress_advanced_security_ga_widget_role_<?php echo esc_attr($key); ?>">
            <input
                id="seopress_advanced_security_ga_widget_role_<?php echo esc_attr($key); ?>"
                name="seopress_advanced_option_name[seopress_advanced_security_ga_widget_role][<?php echo esc_attr($key); ?>]"
                type="checkbox" <?php if ('1' == $check) { ?>
            checked="yes"
            <?php } ?>
            value="1"/>

            <strong><?php echo esc_html($value); ?></strong> (<em><?php echo esc_html(translate_user_role($value,  'default')); ?>)</em>
        </label>
    </p>

    <?php if (isset($options['seopress_advanced_security_ga_widget_role'][$key])) {
            esc_attr($options['seopress_advanced_security_ga_widget_role'][$key]);
        }
    }
    echo seopress_tooltip_link($docs['security']['ga_widget'], esc_html__('Hook to filter user capability for GA stats in dashboard widget - new window', 'wp-seopress-pro'));
}

function seopress_advanced_security_matomo_widget_role_callback() {
    $docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';

    $options = get_option('seopress_advanced_option_name');

    global $wp_roles;

    if ( ! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    foreach ($wp_roles->get_names() as $key => $value) {
        $check = isset($options['seopress_advanced_security_matomo_widget_role'][$key]); ?>
    <p>
        <label
            for="seopress_advanced_security_matomo_widget_role_<?php echo esc_attr($key); ?>">
            <input
                id="seopress_advanced_security_matomo_widget_role_<?php echo esc_attr($key); ?>"
                name="seopress_advanced_option_name[seopress_advanced_security_matomo_widget_role][<?php echo esc_attr($key); ?>]"
                type="checkbox" <?php if ('1' == $check) { ?>
            checked="yes"
            <?php } ?>
            value="1"/>

            <strong><?php echo esc_html($value); ?></strong> (<em><?php echo esc_html(translate_user_role($value,  'default')); ?>)</em>
        </label>
    </p>

    <?php if (isset($options['seopress_advanced_security_matomo_widget_role'][$key])) {
            esc_attr($options['seopress_advanced_security_matomo_widget_role'][$key]);
        }
    }
    echo seopress_tooltip_link($docs['security']['matomo_widget'], esc_html__('Hook to filter user capability for Matomo stats in dashboard widget - new window', 'wp-seopress-pro'));
}

<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//AI SECTION=======================================================================================
add_settings_section(
    'seopress_setting_section_ai', // ID
    '',
    //__("AI","wp-seopress-pro"), // Title
    'seopress_print_section_info_ai', // Callback
    'seopress-settings-admin-ai' // Page
);

add_settings_field(
    'seopress_ai_provider', // ID
    __('AI provider', 'wp-seopress-pro'), // Title
    'seopress_ai_provider_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai' // Section
);

add_settings_section(
    'seopress_setting_section_ai_openai', // ID
    '',
    //__("OpenAI","wp-seopress-pro"), // Title
    'seopress_print_section_info_ai_openai', // Callback
    'seopress-settings-admin-ai' // Page
);

add_settings_field(
    'seopress_ai_openai_api_key', // ID
    __('OpenAI API key', 'wp-seopress-pro'), // Title
    'seopress_ai_openai_api_key_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai_openai' // Section
);

add_settings_field(
    'seopress_ai_openai_model', // ID
    __('OpenAI model', 'wp-seopress-pro'), // Title
    'seopress_ai_openai_model_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai_openai' // Section
);

add_settings_section(
    'seopress_setting_section_ai_deepseek', // ID
    '',
    //__("AI","wp-seopress-pro"), // Title
    'seopress_print_section_info_ai_deepseek', // Callback
    'seopress-settings-admin-ai' // Page
);

add_settings_field(
    'seopress_ai_deepseek_api_key', // ID
    __('DeepSeek API key', 'wp-seopress-pro'), // Title
    'seopress_ai_deepseek_api_key_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai_deepseek' // Section
);

add_settings_field(
    'seopress_ai_deepseek_model', // ID
    __('DeepSeek model', 'wp-seopress-pro'), // Title
    'seopress_ai_deepseek_model_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai_deepseek' // Section
);

add_settings_section(
    'seopress_setting_section_ai_misc', // ID
    '',
    //__("AI Misc","wp-seopress"), // Title
    'seopress_print_section_info_ai_misc', // Callback
    'seopress-settings-admin-ai' // Page
);

add_settings_field(
    'seopress_ai_openai_alt_text', // ID
    __('Use AI to set the image Alt text', 'wp-seopress-pro'), // Title
    'seopress_ai_openai_alt_text_callback', // Callback
    'seopress-settings-admin-ai', // Page
    'seopress_setting_section_ai_misc' // Section
);

add_settings_section(
    'seopress_setting_section_ai_logs', // ID
    '',
    //__("AI Logs","wp-seopress"), // Title
    'seopress_print_section_info_ai_logs', // Callback
    'seopress-settings-admin-ai' // Page
);

<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//AI provider
function seopress_ai_provider_callback()
{
    $providers = [
        'openai' => esc_attr__('OpenAI', 'wp-seopress-pro'),
        'deepseek' => esc_attr__('DeepSeek', 'wp-seopress-pro'),
    ];

    $selected = seopress_pro_get_service('OptionPro')->getAIProvider() ?: 'openai';

    foreach ($providers as $key => $value) { ?>
<div class="seopress_wrap_single_cpt">

    <?php $check = $selected; ?>

    <label
        for="seopress_ai_provider_<?php echo esc_attr($key); ?>">
        <input
            id="seopress_ai_provider_<?php echo esc_attr($key); ?>"
            name="seopress_pro_option_name[seopress_ai_provider]" type="radio" <?php if ($key == $check) { ?>
        checked="yes"
        <?php } ?>
        value="<?php echo esc_attr($key); ?>"/>
        <img src="<?php echo esc_url(SEOPRESS_PRO_ASSETS_DIR . '/img/logo-' . $key . '.svg'); ?>" alt="<?php echo esc_attr($value); ?>" height="30" style="vertical-align: middle;" />
    </label>

    <?php if (isset($options['seopress_ai_provider'])) {
        esc_attr($options['seopress_ai_provider']);
    } ?>
</div>
<?php }
}

//OpenAI API key
function seopress_ai_openai_api_key_callback()
{
    $docs = seopress_get_docs_links();

    $api_key = seopress_pro_get_service('OptionPro')->getAIOpenaiApiKey();
    
    // Show placeholder if key exists, otherwise show empty field
    $display_value = !empty($api_key) ? 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' : '';
    
    printf('<input id="seopress_ai_openai_api_key" type="text" name="seopress_pro_option_name[seopress_ai_openai_api_key]" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" value="%s" placeholder="%s" aria-label="' . esc_html__('OpenAI API key', 'wp-seopress-pro') . '"/>', esc_attr($display_value), esc_attr__('Enter your OpenAI API key', 'wp-seopress-pro'));
?>
    <p class="description">
        <?php
        /* translators: %s documentation URL */
        echo wp_kses_post(sprintf(__('Sign up to <a href="%s" target="_blank">OpenAI</a> to generate your API key.', 'wp-seopress-pro'), esc_url('https://platform.openai.com/account/api-keys')));
        ?>
    </p>

    <?php
    $api_key = seopress_pro_get_service('Usage')->getLicenseKey('openai');

    if (defined('SEOPRESS_OPENAI_KEY') && !empty(SEOPRESS_OPENAI_KEY) && is_string(SEOPRESS_OPENAI_KEY)) { ?>
        <p class="seopress-notice"><?php esc_html_e('Your OpenAI key is defined in wp-config.php.', 'wp-seopress-pro'); ?></p>
    <?php } ?>

    <p>
        <button type="button" id="seopress-open-ai-check-license" class="btn btnTertiary">
            <?php esc_html_e('Test API Key', 'wp-seopress-pro'); ?>
        </button>
        <span class="spinner" style="float: none; margin-left: 10px;"></span>
    </p>
    <div id="seopress-open-ai-check-license-log" style="display: none; margin-top: 10px;"></div>
<?php }

//Open AI model
function seopress_ai_openai_model_callback()
{
    $selected = seopress_pro_get_service('OptionPro')->getAIOpenaiModel() ?: 'gpt-4o'; ?>

    <select id="seopress_ai_openai_model" name="seopress_pro_option_name[seopress_ai_openai_model]">
        <?php
        $models = [
            'gpt-5-chat-latest' => __('GPT-5', 'wp-seopress-pro'),
            'gpt-4o' => __('GPT-4o', 'wp-seopress-pro'),
            'gpt-4o-mini' => __('GPT-4o Mini', 'wp-seopress-pro'),
            'gpt-4' => __('GPT-4', 'wp-seopress-pro'),
            'gpt-3.5-turbo' => __('GPT-3.5 Turbo (deprecated, will no longer be supported after October 2025)', 'wp-seopress-pro'),
        ];

        if (!empty($models)) {
            foreach ($models as $key => $model) { ?>
                <option <?php if (esc_attr($key) == $selected) { ?> selected="selected" <?php } ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($model); ?>
                </option>
        <?php }
        }
        ?>
    </select>

    <p class="description">
        <?php esc_html_e('Select your OpenAI model. This requires at least 1 successful payment of $5 via the OpenAI platform. GPT-3.5 Turbo is deprecated, will no longer be supported after October 2025.', 'wp-seopress-pro'); ?>
    </p>

    <?php if (isset($options['seopress_ai_openai_model'])) {
        esc_attr($options['seopress_ai_openai_model']);
    }
}

//DeepSeek API key
function seopress_ai_deepseek_api_key_callback()
{
    $docs = seopress_get_docs_links();

    $api_key = seopress_pro_get_service('OptionPro')->getAIDeepSeekApiKey();
    
    // Show placeholder if key exists, otherwise show empty field
    $display_value = !empty($api_key) ? 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' : '';
    
    printf('<input id="seopress_ai_deepseek_api_key" type="text" name="seopress_pro_option_name[seopress_ai_deepseek_api_key]" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" value="%s" placeholder="%s" aria-label="' . esc_html__('DeepSeek API key', 'wp-seopress-pro') . '"/>', esc_attr($display_value), esc_attr__('Enter your DeepSeek API key', 'wp-seopress-pro'));
?>
    <p class="description">
        <?php
        /* translators: %s documentation URL */
        echo wp_kses_post(sprintf(__('Sign up to <a href="%s" target="_blank">DeepSeek</a> to generate your API key.', 'wp-seopress-pro'), esc_url('https://platform.deepseek.com/api_keys')));
        ?>
    </p>

    <?php
    $api_key = seopress_pro_get_service('Usage')->getLicenseKey('deepseek');

    if (defined('SEOPRESS_DEEPSEEK_KEY') && !empty(SEOPRESS_DEEPSEEK_KEY) && is_string(SEOPRESS_DEEPSEEK_KEY)) { ?>
        <p class="seopress-notice"><?php esc_html_e('Your DeepSeek key is defined in wp-config.php.', 'wp-seopress-pro'); ?></p>
    <?php } ?>

    <p>
        <button type="button" id="seopress-deepseek-check-license" class="btn btnTertiary">
            <?php esc_html_e('Test API Key', 'wp-seopress-pro'); ?>
        </button>
        <span class="spinner" style="float: none; margin-left: 10px;"></span>
    </p>
    <div id="seopress-deepseek-check-license-log" style="display: none; margin-top: 10px;"></div>
<?php }

//DeepSeek model
function seopress_ai_deepseek_model_callback()
{
    $selected = seopress_pro_get_service('OptionPro')->getAIDeepSeekModel() ?: 'deepseek-chat'; ?>

    <select id="seopress_ai_deepseek_model" name="seopress_pro_option_name[seopress_ai_deepseek_model]">
        <?php
        $models = [
            'deepseek-chat' => __('DeepSeek Chat (recommended)', 'wp-seopress-pro')
        ];

        if (!empty($models)) {
            foreach ($models as $key => $model) { ?>
                <option <?php if (esc_attr($key) == $selected) { ?> selected="selected" <?php } ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($model); ?>
                </option>
        <?php }
        }
        ?>
    </select>

    <p class="description">
        <?php esc_html_e('Select your DeepSeek model.', 'wp-seopress-pro'); ?>
    </p>
    
    <p class="seopress-notice">
        <?php esc_html_e('DeepSeek does not support alt text generation.', 'wp-seopress-pro'); ?>
    </p>

    <?php if (isset($options['seopress_ai_deepseek_model'])) {
        esc_attr($options['seopress_ai_deepseek_model']);
    }
}

function seopress_ai_openai_alt_text_callback()
{
    $check = seopress_pro_get_service('OptionPro')->getAIOpenaiAltText(); ?>

    <label for="seopress_ai_openai_alt_text">
        <input id="seopress_ai_openai_alt_text" name="seopress_pro_option_name[seopress_ai_openai_alt_text]" type="checkbox" <?php if ('1' == $check) { ?> checked="yes" <?php } ?> value="1" />

        <?php esc_html_e('When uploading an image file, automatically set the alternative text using AI', 'wp-seopress-pro'); ?>
    </label>

    <p class="description">
        <?php esc_html_e('This may slow down the image upload.', 'wp-seopress-pro'); ?>
    </p>

    <?php if (isset($options['seopress_ai_openai_alt_text'])) {
        esc_attr($options['seopress_ai_openai_alt_text']);
    }
}

function seopress_print_section_info_ai_logs()
{
    ?>
    <hr>
    <h3 id="seopress-ai-logs">
        <?php esc_html_e('AI Logs', 'wp-seopress-pro'); ?>
    </h3>

    <p><?php esc_html_e('Below is the latest error message obtained from the AI API:', 'wp-seopress-pro'); ?></p>

    <?php
    //Logs
    $logs = get_transient('seopress_pro_ai_logs') ? json_decode(get_transient('seopress_pro_ai_logs'), true) : '';

    echo '<pre style="width: 100%">';
    if (is_array($logs) && !empty($logs['error'])) {
        foreach ($logs['error'] as $key => $value) {
            echo esc_html($key) . ' => ' . esc_html($value) . '<br>';
        }
    ?>
<?php
    } else {
        esc_html_e('Currently no errors logged.', 'wp-seopress-pro');
    }
    echo '</pre>';
}

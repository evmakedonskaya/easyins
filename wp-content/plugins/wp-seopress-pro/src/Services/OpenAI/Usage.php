<?php

namespace SEOPressPro\Services\OpenAI;

defined('ABSPATH') || exit;

class Usage {
    public const NAME_SERVICE = 'Usage';
    private const OPENAI_URL_USAGE = 'https://api.openai.com/v1/usage';
    private const OPENAI_URL_CHAT_COMPLETIONS = 'https://api.openai.com/v1/chat/completions';
    private const DEEPSEEK_URL_BALANCE = 'https://api.deepseek.com/user/balance';
    private const DEEPSEEK_URL_COMPLETIONS = 'https://api.deepseek.com/beta/completions';

    private function getProviderEndpoints($provider) {
        $endpoints = [];
        
        // Sanitize provider parameter
        $provider = sanitize_text_field(strtolower($provider));
        
        switch ($provider) {
            case 'openai':
                $endpoints['usage'] = self::OPENAI_URL_USAGE;
                $endpoints['chat_completions'] = self::OPENAI_URL_CHAT_COMPLETIONS;
                break;
            case 'deepseek':
                $endpoints['balance'] = self::DEEPSEEK_URL_BALANCE;
                $endpoints['completions'] = self::DEEPSEEK_URL_COMPLETIONS;
                break;
            default:
                // Default to OpenAI for backward compatibility
                $endpoints['usage'] = self::OPENAI_URL_USAGE;
                $endpoints['chat_completions'] = self::OPENAI_URL_CHAT_COMPLETIONS;
                break;
        }
        
        return $endpoints;
    }

    private function getProviderName($provider) {
        // Sanitize provider parameter
        $provider = sanitize_text_field(strtolower($provider));
        
        switch ($provider) {
            case 'openai':
                return 'OpenAI';
            case 'deepseek':
                return 'DeepSeek';
            default:
                return ucfirst($provider);
        }
    }

    /**
     * Check if the provider uses chat completions format (OpenAI) or completions format (DeepSeek)
     *
     * @param string $provider The AI provider (openai, deepseek, etc.)
     * @return bool True if using chat completions format, false if using completions format
     */
    private function isChatCompletionsProvider($provider) {
        $provider = sanitize_text_field(strtolower($provider));
        
        switch ($provider) {
            case 'openai':
                return true;
            case 'deepseek':
                return false;
            default:
                return true; // Default to chat completions for backward compatibility
        }
    }

    public function getLicenseKey($provider) {
        $options = get_option('seopress_pro_option_name');

        $api_key = '';

        // Check for provider-specific constants first
        $constant_name = 'SEOPRESS_' . strtoupper($provider) . '_KEY';
        if (defined($constant_name) && !empty(constant($constant_name)) && is_string(constant($constant_name))) {
            $api_key = constant($constant_name);
        } else {
            $api_key = isset($options['seopress_ai_' . $provider . '_api_key']) ? $options['seopress_ai_' . $provider . '_api_key'] : '';
        }

        return $api_key;
    }

    public function checkLicenseKeyExists($provider) {
        $api_key = $this->getLicenseKey($provider);
        $provider_name = $this->getProviderName($provider);

        // Check for empty keys
        if (empty($api_key)) {
            $data = [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %s: provider name */
                    __('Your %s API key has not been entered. Please enter your API key.', 'wp-seopress-pro'),
                    $provider_name
                )
            ];

            return $data;
        }

        // Check for common placeholder values
        $placeholder_values = [
            'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'xxxxxxxx',
            'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'sk-xxxxxxxx'
        ];
        
        if (in_array($api_key, $placeholder_values)) {
            $data = [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %s: provider name */
                    __('Your %1$s API key appears to be a placeholder. Please enter your actual API key from %2$s website.', 'wp-seopress-pro'),
                    $provider_name,
                    $provider_name
                )
            ];

            return $data;
        }

        $endpoints = $this->getProviderEndpoints($provider);
        
        // Use different endpoints for different providers
        if (strtolower($provider) === 'openai') {
            $url = $endpoints['usage'];
            $params = array(
                'date' => date('Y-m-d'),
            );
            $url = add_query_arg($params, $url);
        } else {
            // For DeepSeek and other providers, use balance endpoint
            $url = isset($endpoints['balance']) ? $endpoints['balance'] : $endpoints['usage'];
        }

        $response = wp_remote_get($url, array(
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %1$s: provider name, %2$s: error message */
                    __('Failed to connect to %1$s API: %2$s', 'wp-seopress-pro'),
                    $provider_name,
                    $response->get_error_message()
                )
            ];
        }

        $httpCode = wp_remote_retrieve_response_code($response);

        if ($httpCode === 200) {
            return [
                'code' => 'success',
                'message' => sprintf(
                    /* translators: %s: provider name */
                    __('Your %s API key is valid.', 'wp-seopress-pro'),
                    $provider_name
                )
            ];
        } else {
            return [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %1$s: provider name, %2$s: error code */
                    __('Your %1$s API key is invalid or has expired. Error: %2$s', 'wp-seopress-pro'),
                    $provider_name,
                    esc_html($httpCode)
                )
            ];
        }
    }

    public function checkLicenseKeyExpiration($provider) {
        $api_key = $this->getLicenseKey($provider);
        $provider_name = $this->getProviderName($provider);

        $options = get_option('seopress_pro_option_name');
        $model_name = isset($options['seopress_ai_' . $provider . '_model']) ? $options['seopress_ai_' . $provider . '_model'] : $this->getDefaultModel($provider);

        $endpoints = $this->getProviderEndpoints($provider);
        
        // Use the appropriate endpoint based on provider
        if ($this->isChatCompletionsProvider($provider)) {
            $url = $endpoints['chat_completions'];
        } else {
            $url = $endpoints['completions'];
        }

        // Build request body based on provider format
        if ($this->isChatCompletionsProvider($provider)) {
            // OpenAI format
            $body = [
                'model'    => $model_name,
                'temperature' => 1,
                'max_tokens' => 10, // Minimal tokens for testing
                'messages' => [
                    ['role' => 'user', 'content' => 'test']
                ]
            ];
        } else {
            // DeepSeek completions format
            $body = [
                'model'    => $model_name,
                'temperature' => 1,
                'max_tokens' => 10, // Minimal tokens for testing
                'prompt' => 'test'
            ];
        }

        $args = [
            'body'        => wp_json_encode($body),
            'timeout'     => '30',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %1$s: provider name, %2$s: error message */
                    __('Failed to connect to %1$s API: %2$s', 'wp-seopress-pro'),
                    $provider_name,
                    $response->get_error_message()
                )
            ];
        }

        $httpCode = wp_remote_retrieve_response_code($response);

        if ($httpCode === 200) {
            return [
                'code' => 'success',
                'message' => sprintf(
                    /* translators: %s: provider name */
                    __('Your %s API key is valid.', 'wp-seopress-pro'),
                    $provider_name
                )
            ];
        } else {
            return [
                'code' => 'error',
                'message' => sprintf(
                    /* translators: %1$s: provider name, %2$s: error code, %3$s: usage url, %4$s: provider name */
                    __('Your %1$s API key is invalid or has expired. Error: %2$s. Go to your <a href="%3$s" target="_blank">%4$s Usage page</a> to check this.', 'wp-seopress-pro'),
                    $provider_name,
                    esc_html($httpCode),
                    $this->getProviderUsageUrl($provider),
                    $provider_name
                )
            ];
        }
    }

    /**
     * Get the usage/balance URL for the provider
     *
     * @param string $provider The AI provider
     * @return string The usage URL
     */
    private function getProviderUsageUrl($provider) {
        $provider = sanitize_text_field(strtolower($provider));
        
        switch ($provider) {
            case 'openai':
                return 'https://platform.openai.com/usage';
            case 'deepseek':
                return 'https://platform.deepseek.com/usage';
            default:
                return '#';
        }
    }

    private function getDefaultModel($provider) {
        $provider = sanitize_text_field(strtolower($provider));
        
        switch ($provider) {
            case 'openai':
                return 'gpt-4o';
            case 'deepseek':
                return 'deepseek-chat';
            default:
                return 'gpt-4o';
        }
    }
}

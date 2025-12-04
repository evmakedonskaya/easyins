<?php

namespace SEOPressPro\Services\OpenAI;

defined('ABSPATH') || exit;

class Completions {
	public const NAME_SERVICE = 'Completions';
	private const OPENAI_URL_CHAT_COMPLETIONS = 'https://api.openai.com/v1/chat/completions';
	private const DEEPSEEK_URL_COMPLETIONS = 'https://api.deepseek.com/beta/completions';

	private function getProviderEndpoints($provider = 'openai') {
		$endpoints = [];
		
		// Sanitize provider parameter
		$provider = sanitize_text_field(strtolower($provider));
		
		switch ($provider) {
			case 'openai':
				$endpoints['chat_completions'] = self::OPENAI_URL_CHAT_COMPLETIONS;
				break;
			case 'deepseek':
				$endpoints['completions'] = self::DEEPSEEK_URL_COMPLETIONS;
				break;
			default:
				// Default to OpenAI for backward compatibility
				$endpoints['chat_completions'] = self::OPENAI_URL_CHAT_COMPLETIONS;
				break;
		}
		
		return $endpoints;
	}

	private function getProviderName($provider = 'openai') {
		switch (strtolower($provider)) {
			case 'openai':
				return 'OpenAI';
			case 'deepseek':
				return 'DeepSeek';
			default:
				return ucfirst($provider);
		}
	}

	private function getDefaultModel($provider = 'openai') {
		switch (strtolower($provider)) {
			case 'openai':
				return 'gpt-4o';
			case 'deepseek':
				return 'deepseek-chat';
			default:
				return 'gpt-4o';
		}
	}

	/**
	 * Check if the provider uses chat completions format (OpenAI) or completions format (DeepSeek)
	 *
	 * @param string $provider The AI provider (openai, deepseek, etc.)
	 * @return bool True if using chat completions format, false if using completions format
	 */
	private function isChatCompletionsProvider($provider = 'openai') {
		switch (strtolower($provider)) {
			case 'openai':
				return true;
			case 'deepseek':
				return false; // DeepSeek uses completions format
			default:
				return true; // Default to chat completions for backward compatibility
		}
	}

	/**
	 * Check if the provider supports multimodal content (images)
	 *
	 * @param string $provider The AI provider (openai, deepseek, etc.)
	 * @return bool True if supports multimodal content, false otherwise
	 */
	private function supportsMultimodal($provider = 'openai') {
		switch (strtolower($provider)) {
			case 'openai':
				return true;
			case 'deepseek':
				return false; // DeepSeek completions API doesn't support images
			default:
				return true; // Default to supporting multimodal for backward compatibility
		}
	}

	/**
	 * Check if the provider supports response_format parameter
	 *
	 * @param string $provider The AI provider (openai, deepseek, etc.)
	 * @return bool True if supports response_format, false otherwise
	 */
	private function supportsResponseFormat($provider = 'openai') {
		switch (strtolower($provider)) {
			case 'openai':
				return true;
			case 'deepseek':
				return false; // DeepSeek completions API doesn't support response_format
			default:
				return true; // Default to supporting response_format for backward compatibility
		}
	}

	/**
	 * Build the request body based on the provider's API format
	 *
	 * @param array $body The base body parameters
	 * @param string $provider The AI provider
	 * @return array The formatted request body
	 */
	private function buildRequestBody($body, $provider = 'openai') {
		if ($this->isChatCompletionsProvider($provider)) {
			// OpenAI format - use messages array
			return $body;
		} else {
			// DeepSeek completions format - convert messages to prompt
			$completions_body = [
				'model' => $body['model'],
				'temperature' => $body['temperature'],
				'max_tokens' => $body['max_tokens'],
			];

			// Convert messages array to a single prompt string
			$prompt = '';
			$has_multimodal_content = false;
			
			foreach ($body['messages'] as $message) {
				if ($message['role'] === 'user') {
					// Handle different content formats
					if (is_string($message['content'])) {
						$prompt .= $message['content'] . "\n\n";
					} elseif (is_array($message['content'])) {
						// Handle multimodal content (text + image)
						foreach ($message['content'] as $content_item) {
							if ($content_item['type'] === 'text') {
								$prompt .= $content_item['text'] . "\n\n";
							} elseif ($content_item['type'] === 'image_url') {
								$has_multimodal_content = true;
								// For DeepSeek completions, we can't include images directly
								// Add a note about the image in the prompt
								$prompt .= "[Image: " . $content_item['image_url']['url'] . "]\n\n";
							}
						}
					}
				}
			}
			
			$completions_body['prompt'] = trim($prompt);
			
			// Remove response_format for DeepSeek completions (not supported)
			if (isset($body['response_format']) && !$this->supportsResponseFormat($provider)) {
				unset($completions_body['response_format']);
			}
			
			// Add warning about multimodal content if present
			if ($has_multimodal_content && !$this->supportsMultimodal($provider)) {
				$completions_body['prompt'] = "Note: This request contains image content which cannot be processed by this provider. The image URL has been included as text for reference.\n\n" . $completions_body['prompt'];
			}
			
			return $completions_body;
		}
	}

	/**
	 * Clean response content by removing markdown code blocks and extracting JSON
	 *
	 * @param string $content The raw content from the AI response
	 * @return string The cleaned content
	 */
	private function cleanResponseContent($content) {
		// Extract content between curly braces { and }
		if (preg_match('/\{.*\}/s', $content, $matches)) {
			$content = $matches[0];
		}
		
		// Remove any leading/trailing whitespace
		$content = trim($content);
		
		return $content;
	}

	/**
	 * Parse the response based on the provider's API format
	 *
	 * @param object $response_data The raw response data
	 * @param string $provider The AI provider
	 * @return object The parsed response
	 */
	private function parseResponse($response_data, $provider = 'openai') {
		if ($this->isChatCompletionsProvider($provider)) {
			// OpenAI format - response has choices[0].message.content
			return $response_data;
		} else {
			// DeepSeek completions format - response has choices[0].text
			// Convert to OpenAI format for consistency
			$converted_response = new \stdClass();
			$converted_response->choices = [];
			$converted_response->choices[0] = new \stdClass();
			$converted_response->choices[0]->message = new \stdClass();
			
			// Clean the content to remove markdown code blocks
			$raw_content = $response_data->choices[0]->text;
			$converted_response->choices[0]->message->content = $this->cleanResponseContent($raw_content);
			
			$converted_response->choices[0]->finish_reason = $response_data->choices[0]->finish_reason;
			$converted_response->choices[0]->index = $response_data->choices[0]->index;
			
			// Copy other response properties
			$converted_response->id = $response_data->id;
			$converted_response->created = $response_data->created;
			$converted_response->model = $response_data->model;
			$converted_response->object = $response_data->object;
			
			if (isset($response_data->usage)) {
				$converted_response->usage = $response_data->usage;
			}
			
			return $converted_response;
		}
	}

	/**
	 * Get AI model from the SEOPress options based on provider.
	 *
	 * @param string $provider The AI provider (openai, deepseek, etc.)
	 * @return string $model the AI model name
	 */
	public function getAIModel($provider = null) {
		// If no provider specified, get from user settings
		if ($provider === null) {
			$option_service = seopress_pro_get_service('OptionPro');
			$provider = $option_service->getAIProvider();
			// Fallback to openai if no provider is set
			if (empty($provider)) {
				$provider = 'openai';
			}
		}

		$options = get_option('seopress_pro_option_name');
		$model = isset($options['seopress_ai_' . $provider . '_model']) ? $options['seopress_ai_' . $provider . '_model'] : $this->getDefaultModel($provider);

		return $model;
	}

	/**
	 * Get the appropriate API key for the provider.
	 *
	 * @param string $provider The AI provider (openai, deepseek, etc.)
	 * @return string $api_key The API key for the provider
	 */
	private function getProviderApiKey($provider = null) {
		// If no provider specified, get from user settings
		if ($provider === null) {
			$option_service = seopress_pro_get_service('OptionPro');
			$provider = $option_service->getAIProvider();
			// Fallback to openai if no provider is set
			if (empty($provider)) {
				$provider = 'openai';
			}
		}

		$usage_service = seopress_pro_get_service('Usage');
		return $usage_service->getLicenseKey($provider);
	}

	/**
	 * Get the current AI provider from user settings.
	 *
	 * @return string The AI provider (openai, deepseek, etc.)
	 */
	private function getCurrentProvider() {
		$option_service = seopress_pro_get_service('OptionPro');
		$provider = $option_service->getAIProvider();
		// Fallback to openai if no provider is set
		return !empty($provider) ? $provider : 'openai';
	}

	/**
	 * Get OpenAI model from the SEOPress options (backward compatibility).
	 *
	 * @return string $model the OpenAI model name
	 */
	public function getOpenAIModel() {
		return $this->getAIModel('openai');
	}

	/**
	 * Generate titles and descriptions for a post.
	 *
	 * This function generates titles and descriptions based on the provided parameters.
	 *
	 * @param int    $post_id   The ID of the post for which to generate titles and descriptions.
	 * @param string $meta      title|desc (optional).
	 * @param string $language  The language for generating titles and descriptions (default is 'en_US').
	 * @param bool   $autosave  Whether this is an autosave operation, useful for bulk actions (default is false).
	 * @param string $provider  The AI provider to use (default is null, uses user's saved preference).
	 * @param string $nonce     Security nonce for admin requests (optional).
	 *
	 * @return array $data The answers from AI with title/desc
	 */
	public function generateTitlesDesc($post_id, $meta = '', $language = 'en_US', $autosave = false, $provider = null, $nonce = null) {
		// Validate post_id
		$post_id = absint($post_id);
		if (!$post_id || !get_post($post_id)) {
			return [
				'message' => __('Invalid post ID provided.', 'wp-seopress-pro'),
				'title' => '',
				'desc' => ''
			];
		}

		// Verify nonce if provided (for admin requests)
		if ($nonce !== null && !wp_verify_nonce($nonce, 'seopress_ai_generate_' . $post_id)) {
			return [
				'message' => __('Security check failed.', 'wp-seopress-pro'),
				'title' => '',
				'desc' => ''
			];
		}

		// If no provider specified, get from user settings
		if ($provider === null) {
			$provider = $this->getCurrentProvider();
		}

		//Init
		$title = '';
		$description = '';
		$message = '';
		if (empty($language)) {
			$language = get_locale();
		}

		$content = get_post_field('post_content', $post_id);
		$content = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($content)))));

		//Compatibility with current page and theme builders
		$theme = wp_get_theme();

		//Divi
		if ('Divi' == $theme->template || 'Divi' == $theme->parent_theme) {
			$regex = '/\[(\[?)(et_pb_[^\s\]]+)(?:(\s)[^\]]+)?\]?(?:(.+?)\[\/\2\])?|\[\/(et_pb_[^\s\]]+)?\]/';
			$content = preg_replace($regex, '', $content);
		}

		//Bricks compatibility
		if (defined('BRICKS_DB_EDITOR_MODE') && ('bricks' == $theme->template || 'Bricks' == $theme->parent_theme)) {
			$page_sections = get_post_meta($post_id, BRICKS_DB_PAGE_CONTENT, true);
			$editor_mode   = get_post_meta($post_id, BRICKS_DB_EDITOR_MODE, true);

			if (is_array($page_sections) && 'wordpress' !== $editor_mode) {
				$content = \Bricks\Frontend::render_data($page_sections);
			}
		}

		//Limit post content sent to 500 words (higher value will return a 400 error)
		$content = wp_trim_words( $content, 500 );

		//If no post_content use the permalink
		if (empty($content)) {
			$content = get_permalink($post_id);
		}

		$body = [
			'model'    => $this->getAIModel($provider),
			'temperature' => 1,
			'max_tokens' => 220,
		];

		// Add response_format only if supported by the provider
		if ($this->supportsResponseFormat($provider)) {
			$body['response_format'] = [
				'type' => 'json_object'
			];
		}

		$body['messages'] = [];

		//Get current post language for bulk actions
		if ($meta === 'title' || $meta === 'desc') {
			//Default
			if (function_exists('seopress_normalized_locale')) {
				$language = seopress_normalized_locale(get_locale());
			} else {
				$language = get_locale();
			}

			//WPML
			if (defined('ICL_SITEPRESS_VERSION')) {
				$language = apply_filters( 'wpml_post_language_details', NULL, $post_id );
				$language = !empty($language['locale']) ? $language['locale'] : get_locale();
			}

			//Polylang
			if (function_exists('pll_get_post_language')) {
				$language = !empty(pll_get_post_language($post_id, 'locale')) ? pll_get_post_language($post_id, 'locale') : get_locale();
				
			}
		}
		
		//Convert language code to readable name
		$language = locale_get_display_name($language, 'en') ? esc_html(locale_get_display_name($language, 'en')) : $language;

		//Get target keywords
		$target_keywords = !empty(get_post_meta($post_id, '_seopress_analysis_target_kw', true)) ? get_post_meta($post_id, '_seopress_analysis_target_kw', true) : null;

		//Prompt for meta title
		$prompt_title = sprintf(
			/* translators: 1: language, 2: target keywords, 3: content */
			__('Generate, in this language %1$s, an engaging SEO title metadata in one sentence of sixty characters maximum, with at least one of these keywords in the prompt response: "%2$s", based on this content: %3$s.', 'wp-seopress-pro'),
			esc_attr($language),
			esc_html($target_keywords),
			esc_html($content)
		);

		$msg   = apply_filters( 'seopress_ai_' . $provider . '_meta_title', $prompt_title, $post_id );

		if (empty($meta) || $meta === 'title') {
			$body['messages'][] = ['role' => 'user', 'content' => $msg];
		}

		//Prompt for meta description
		$prompt_desc = sprintf(
			/* translators: 1: language, 2: target keywords, 3: content */
			__('Generate, in this language %1$s, an engaging SEO meta description in less than 160 characters, with at least one of these keywords in the prompt response: "%2$s", based on this content: %3$s.', 'wp-seopress-pro'),
			esc_attr($language),
			esc_html($target_keywords),
			esc_html($content)
		);

		$msg   = apply_filters( 'seopress_ai_' . $provider . '_meta_desc', $prompt_desc, $post_id );

		if (empty($meta) || $meta === 'desc') {
			$body['messages'][] = ['role' => 'user', 'content' => $msg];
		}

		// For providers that don't support response_format, we need to be more explicit about JSON formatting
		$json_instruction = 'Provide the answer as a JSON object with "title" as first key and "desc" for second key for parsing in this language ' . $language . '. You must respect the grammar and typing of the language.';
		
		if (!$this->supportsResponseFormat($provider)) {
			$json_instruction = 'You must respond with ONLY a valid JSON object. The JSON must have exactly two keys: "title" (for the meta title) and "desc" (for the meta description). Use this language: ' . $language . '. Format: {"title": "your title here", "desc": "your description here"}';
		}
		
		$body['messages'][] = ['role' => 'user', 'content' => $json_instruction];

		// Build the request body based on provider format
		$request_body = $this->buildRequestBody($body, $provider);

		$args = [
			'body'        => wp_json_encode($request_body),
			'timeout'     => '30',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->getProviderApiKey($provider),
				'Content-Type' => 'application/json'
			],
		];

		$args = apply_filters('seopress_ai_' . $provider . '_request_args', $args);

		$endpoints = $this->getProviderEndpoints($provider);
		$url = $this->isChatCompletionsProvider($provider) ? $endpoints['chat_completions'] : $endpoints['completions'];

		$response = wp_remote_post( $url, $args );

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			if (is_wp_error($response)) {
				$message = $response->get_error_message();
			} else {
				$response_code = wp_remote_retrieve_response_code($response);
				$response_body = wp_remote_retrieve_body($response);
				$message = sprintf(
					/* translators: 1: provider name, 2: response code */
					__('An error occurred with %1$s API, please try again. Response code: %2$s', 'wp-seopress-pro'),
					$this->getProviderName($provider),
					$response_code
				);
				
				// Log detailed error information
				$error_log = [
					'provider' => $provider,
					'response_code' => $response_code,
					'response_body' => $response_body,
					'request_body' => $request_body,
					'timestamp' => current_time('mysql')
				];
				set_transient('seopress_pro_ai_logs', json_encode($error_log), 30 * DAY_IN_SECONDS);
			}
		} else {
			$raw_data = json_decode(wp_remote_retrieve_body($response));
			
			// Parse response based on provider format
			$data = $this->parseResponse($raw_data, $provider);
			
			$message = 'Success';

			if (empty($meta) || $meta === 'title') {
				$result = json_decode($data->choices[0]->message->content, true);

				$result = $result['title'];

				$title = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));

				if ($autosave === true) {
					update_post_meta( $post_id, '_seopress_titles_title', sanitize_text_field(html_entity_decode($title)));
				}
			}

			if (empty($meta)) {
				$result = json_decode($data->choices[0]->message->content, true);
				$result = $result['desc'];
			} elseif ($meta === 'desc') {
				$result = json_decode($data->choices[0]->message->content, true);
				$result = $result['desc'];
			}

			if (empty($meta) || $meta === 'desc') {
				$description = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));

				if ($autosave === true) {
					update_post_meta( $post_id, '_seopress_titles_desc', sanitize_textarea_field(html_entity_decode($description)));
				}
            }
		}

		$data = [
			'message' => $message,
			'title' => html_entity_decode($title),
			'desc' => html_entity_decode($description)
		];

		return $data;
	}

	/**
	 * Generate alt text for an image.
	 *
	 * This function generates the alternative text for an image file.
	 *
	 * @param int    $post_id   The ID of the post for which to generate titles and descriptions.
	 * @param string $action    The action to run (optional).
	 * @param string $language  The language for generating titles and descriptions (default is 'en_US').
	 * @param bool   $update_empty_alt_text  Whether to update empty alt text (default is true).
	 * @param string $provider  The AI provider to use (default is null, uses user's saved preference).
	 *
	 * @return array $data The answers from AI with title/desc
	 */
	public function generateImgAltText($post_id, $action = '', $language = 'en_US', $update_empty_alt_text = true, $provider = null) {
		// Validate post_id
		$post_id = absint($post_id);
		if (!$post_id || !get_post($post_id)) {
			return [
				'message' => __('Invalid post ID provided.', 'wp-seopress-pro'),
				'alt_text' => ''
			];
		}

		// If no provider specified, get from user settings
		if ($provider === null) {
			$provider = $this->getCurrentProvider();
		}

		// Check if provider supports multimodal content
		if (!$this->supportsMultimodal($provider)) {
			return [
				'message' => sprintf(
					/* translators: 1: provider name */
					__('Image alt text generation is not supported by %1$s. Please use OpenAI or another provider that supports multimodal content.', 'wp-seopress-pro'),
					$this->getProviderName($provider)
				),
				'alt_text' => '',
			];
		}

		// Update empty alt text only
		$current_alt_text = get_post_meta($post_id, '_wp_attachment_image_alt', true);
		if (!empty($current_alt_text) && !$update_empty_alt_text) {
			return [
				'message' => 'Alt text already exists, no need to generate it.',
				'alt_text' => $current_alt_text,
			];
		}

		if ($action === 'alt_text') {
			//Default
			if (function_exists('seopress_normalized_locale')) {
				$language = seopress_normalized_locale(get_locale());
			} else {
				$language = get_locale();
			}

			//WPML
			if (defined('ICL_SITEPRESS_VERSION')) {
				$language = apply_filters( 'wpml_post_language_details', NULL, $post_id );
				$language = !empty($language['locale']) ? $language['locale'] : get_locale();
			}

			//Polylang
			if (function_exists('pll_get_post_language')) {
				$language = !empty(pll_get_post_language($post_id, 'locale')) ? pll_get_post_language($post_id, 'locale') : get_locale();
			}
		}

		//Convert language code to readable name
		$language = locale_get_display_name($language, 'en') ? esc_html(locale_get_display_name($language, 'en')) : $language;

		$image_src = wp_get_attachment_image_src($post_id, 'full');

		//Prompt for alt text
		$prompt_alt_text = sprintf(
			/* translators: %s: language */
			esc_html__('Write in less than 10 words an alternative text to improve accessibility and SEO, in this language %s.', 'wp-seopress-pro'), 
			esc_attr($language)
		);

		$prompt_alt_text  = apply_filters( 'seopress_ai_' . $provider . '_alt_text', $prompt_alt_text, $post_id );

		// For providers that don't support response_format, we need to be more explicit about JSON formatting
		if ($this->supportsResponseFormat($provider)) {
			$prompt_alt_text .= esc_html__('Return the answer in JSON. The key containing the alternative text must be called alt_text.', 'wp-seopress-pro');
		} else {
			$prompt_alt_text .= esc_html__('You must respond with ONLY a valid JSON object. The JSON must have exactly one key: "alt_text" containing the alternative text. Format: {"alt_text": "your alt text here"}', 'wp-seopress-pro');
		}

		$body = [
			'model'    => $this->getAIModel($provider),
			'temperature' => 1,
			'messages' => [
				[
					'role' => 'user',
					'content' => [
						[
							'type' => 'text',
							'text' => $prompt_alt_text
						],
						[
							'type' => 'image_url',
							'image_url' => [
								'url' => esc_html($image_src[0])
							]
						]
					]
				]
			],
			'max_tokens' => 300,
		];

		// Add response_format only if supported by the provider
		if ($this->supportsResponseFormat($provider)) {
			$body['response_format'] = [
				'type' => 'json_object'
			];
		}

		// Build the request body based on provider format
		$request_body = $this->buildRequestBody($body, $provider);

		$args = [
			'body'        => wp_json_encode($request_body),
			'timeout'     => '30',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->getProviderApiKey($provider),
				'Content-Type' => 'application/json'
			],
		];

		$args = apply_filters('seopress_ai_' . $provider . '_request_args_alt', $args);

		$endpoints = $this->getProviderEndpoints($provider);
		$url = $this->isChatCompletionsProvider($provider) ? $endpoints['chat_completions'] : $endpoints['completions'];

		$response = wp_remote_post( $url, $args );

		$alt_text = '';

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			if (is_wp_error($response)) {
				$message = $response->get_error_message();
			} else {
				$response_code = wp_remote_retrieve_response_code($response);
				$response_body = wp_remote_retrieve_body($response);
				$message = sprintf(
					/* translators: 1: provider name, 2: response code */
					__('An error occurred with %1$s API, please try again. Response code: %2$s', 'wp-seopress-pro'),
					$this->getProviderName($provider),
					$response_code
				);
				
				// Log detailed error information
				$error_log = [
					'provider' => $provider,
					'response_code' => $response_code,
					'response_body' => $response_body,
					'request_body' => $request_body,
					'timestamp' => current_time('mysql')
				];
				set_transient('seopress_pro_ai_logs', json_encode($error_log), 30 * DAY_IN_SECONDS);
			}
		} else {
			// Get the response body once
			$response_body = wp_remote_retrieve_body($response);
			
			// Decode as object for parseResponse method
			$response_object = json_decode($response_body, false);
			
			// Check if JSON decode was successful
			if (json_last_error() !== JSON_ERROR_NONE) {
				$message = sprintf(
					/* translators: 1: provider name, 2: error message */
					__('Invalid JSON response from %1$s API: %2$s', 'wp-seopress-pro'),
					$this->getProviderName($provider),
					json_last_error_msg()
				);
				$alt_text = '';
			} else {
				// Parse response based on provider format
				$data = $this->parseResponse($response_object, $provider);

				$message = 'Success';

				// Extract the content from the response
				if (isset($data->choices[0]->message->content)) {
					$result = $data->choices[0]->message->content;
					
					// Check if content is null (AI refused to process)
					if ($result === null) {
						$refusal_message = isset($data->choices[0]->message->refusal) ? $data->choices[0]->message->refusal : __('AI refused to describe this image.', 'wp-seopress-pro');
						$message = $refusal_message;
						$result = '';
					} else {
						// Parse the JSON content to extract alt_text
						$parsed_result = json_decode($result, true);
						
						// Handle both JSON format and plain text format
						if (is_array($parsed_result) && isset($parsed_result['alt_text'])) {
							$result = $parsed_result['alt_text'];
						} elseif (is_string($result)) {
							// If it's not valid JSON, use the content as is
							$result = $result;
						} else {
							$result = '';
						}
					}
				} else {
					$result = '';
					$message = __('Unable to extract content from AI response.', 'wp-seopress-pro');
				}

				$alt_text = esc_attr(trim(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($result)))), '"'));
			}
		}

		$data = [
			'message' => $message,
			'alt_text' => html_entity_decode($alt_text),
		];

		if(!empty($alt_text)){
			update_post_meta($post_id, '_wp_attachment_image_alt', apply_filters('seopress_update_alt', sanitize_text_field(html_entity_decode($alt_text)), $post_id));
		}

		return $action === 'alt_text' ? $data['alt_text'] : $data;
	}
}

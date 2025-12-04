<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_ai()
{

	seopress_print_pro_section('ai');

	?>
	<p>
		<?php echo wp_kses_post(__('Enter your <strong>API key</strong>, select an <strong>AI model</strong>, and start automagically <strong> generating your title and description meta tags, as well as alt texts for images</strong> (from the SEO metabox or from your posts‘ list view bulk actions).', 'wp-seopress-pro')); ?>
	</p>

	<p>
		<?php echo wp_kses_post(__('We send your <strong>post content</strong>, <strong>language</strong> and <strong>target keywords</strong> to AI for better results. We ask in return to put at least one of your target keywords. However, we can‘t fully control the answers provided by the AI.', 'wp-seopress-pro')); ?>
	</p>

	<hr>
	
	<h3 id="seopress-ai-general">
		<?php esc_html_e('General settings', 'wp-seopress-pro'); ?>
	</h3>
<?php
}

function seopress_print_section_info_ai_openai()
{
	$docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : '';
	?>
	<hr>

	<h3 id="seopress-ai-openai">
		<?php esc_html_e('OpenAI', 'wp-seopress-pro'); ?>
	</h3>

	<details class="seopress-notice">
		<summary>
			<?php esc_html_e('How to connect your site with OpenAI?', 'wp-seopress-pro'); ?>
		</summary>

		<ol>
			<li>
				<?php
					/* translators: %s documentation URL */
					echo wp_kses_post(sprintf(__('Create an account on <a href="%s" target="_blank">OpenAI</a><span class="dashicons dashicons-external"></span> website.', 'wp-seopress-pro'), esc_url('https://platform.openai.com/account/api-keys')));
				?>
			</li>
			<li><?php echo wp_kses_post(__('Make a <strong>payment of at least $5</strong> on the OpenAI platform.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('Generate an <strong>OpenAI API key</strong>.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('<strong>Paste it</strong> below and <strong>Save changes</strong>.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('And There you go! Start <strong>generating titles, meta desc and alt texts using AI</strong>.', 'wp-seopress-pro')); ?></li>
		</ol>
	</details>

	<p>
		<?php 
		if (!empty($docs['ai']['openai']['errors'])) {
			/* translators: %s documentation URL */ echo wp_kses_post(sprintf(__('If you encounter any error, please read this <a href="%s" target="_blank">guide</a>.', 'wp-seopress-pro'), esc_url($docs['ai']['openai']['errors'])));
		} ?>
	</p>
<?php
}

function seopress_print_section_info_ai_deepseek()
{
	$docs     = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
	<hr>
	<h3 id="seopress-ai-deepseek">
		<?php esc_html_e('DeepSeek', 'wp-seopress-pro'); ?>
	</h3>

	<details class="seopress-notice">
		<summary>
			<?php esc_html_e('How to connect your site with DeepSeek?', 'wp-seopress-pro'); ?>
		</summary>

		<ol>
			<li>
				<?php
					/* translators: %s documentation URL */
					echo wp_kses_post(sprintf(__('Create an account on <a href="%s" target="_blank">DeepSeek</a><span class="dashicons dashicons-external"></span> website.', 'wp-seopress-pro'), esc_url('https://platform.deepseek.com/api_keys')));
				?>
			</li>
			<li><?php echo wp_kses_post(__('Make a <strong>payment of at least $2</strong> on the DeepSeek platform.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('Generate an <strong>DeepSeek API key</strong>.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('<strong>Paste it</strong> below and <strong>Save changes</strong>.', 'wp-seopress-pro')); ?></li>
			<li><?php echo wp_kses_post(__('And There you go! Start <strong>generating titles, meta desc and alt texts using AI</strong>.', 'wp-seopress-pro')); ?></li>
		</ol>
	</details>

	<p>
		<?php 
		if (!empty($docs['ai']['deepseek']['errors'])) {
			/* translators: %s documentation URL */ echo wp_kses_post(sprintf(__('If you encounter any error, please read this <a href="%s" target="_blank">guide</a>.', 'wp-seopress-pro'), esc_url($docs['ai']['deepseek']['errors'])));
		} ?>
	</p>
<?php
}

function seopress_print_section_info_ai_misc()
{
	?>
	<hr>
	<h3 id="seopress-ai-misc">
		<?php esc_html_e('Misc', 'wp-seopress-pro'); ?>
	</h3>
<?php
}
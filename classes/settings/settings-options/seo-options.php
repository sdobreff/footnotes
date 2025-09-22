<?php
/**
 * General settings of the plugin
 *
 * @package awe
 *
 * @since latest
 */

use AWEF\Helpers\Settings;
use AWEF\Controllers\Post_Settings;

	Settings::build_option(
		array(
			'title' => esc_html__( 'SEO settings', 'awesome-footnotes' ),
			'id'    => 'general-settings-tab',
			'type'  => 'tab-title',
		)
	);

	// Meta description.
	Settings::build_option(
		array(
			'title' => esc_html__( 'Meta description', 'awesome-footnotes' ),
			'id'    => 'markup-format-settings',
			'type'  => 'header',
		)
	);

	$current_post          = \get_post();

	if ( empty( $current_post->post_excerpt ) ) {
		$current_post->post_excerpt = Post_Settings::the_short_content( 160, $current_post->post_content );
	}

	Settings::build_option(
		array(
			'name'    => \esc_html__( 'Description', '0-day-analytics' ),
			'id'      => 'seo_description',
			'type'    => 'textarea',
			'hint'    => \esc_html__( 'Fill this if you want to use custom meta description in SEO header of the post', '0-day-analytics' ),
			'default' => Post_Settings::the_short_content( 160, $current_post->post_excerpt ),
		)
	);

<?php
/**
 * Option settings of the plugin
 *
 * @package awe
 *
 * @since 2.0.0
 */

use AWEF\Helpers\Settings;

	Settings::build_option(
		array(
			'title' => esc_html__( 'Options', 'awesome-footnotes' ),
			'id'    => 'options-settings-tab',
			'type'  => 'tab-title',
		)
	);

	// Markup used.
	Settings::build_option(
		array(
			'title' => esc_html__( 'Suppress footnotes', 'awesome-footnotes' ),
			'id'    => 'markup-format-settings',
			'type'  => 'header',
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'Do not autodisplay in posts', 'awesome-footnotes' ),
			'id'      => 'no_display_post',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_post'],
			'hint'    => esc_html__( 'Use this option if you want to display footnotes on separate place other than below the post (default). To achieve that you have to either use a shortcode ([awef_show_footnotes]), or direct PHP call (Footnotes_Formatter::show_footnotes();).', 'awesome-footnotes' ),
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'On the home page', 'awesome-footnotes' ),
			'id'      => 'no_display_home',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_home'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'When displaying a preview', 'awesome-footnotes' ),
			'id'      => 'no_display_preview',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_preview'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'In search results', 'awesome-footnotes' ),
			'id'      => 'no_display_search',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_search'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'In the feed (RSS, Atom, etc.)', 'awesome-footnotes' ),
			'id'      => 'no_display_feed',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_feed'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'In any kind of archive', 'awesome-footnotes' ),
			'id'      => 'no_display_archive',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_archive'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'In category archives', 'awesome-footnotes' ),
			'id'      => 'no_display_category',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_category'],
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'in date-based archives', 'awesome-footnotes' ),
			'id'      => 'no_display_date',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_display_date'],
		)
	);

	// Priority.
	Settings::build_option(
		array(
			'title' => esc_html__( 'Priority', 'awesome-footnotes' ),
			'id'    => 'priority-format-settings',
			'type'  => 'header',
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'Plugin priority', 'awesome-footnotes' ),
			'id'      => 'priority',
			'type'    => 'number',
			'default' => Settings::get_current_options()['priority'],
		)
	);

	// Combine footnotes.
	Settings::build_option(
		array(
			'title' => esc_html__( 'Combine footnotes', 'awesome-footnotes' ),
			'id'    => 'priority-format-settings',
			'type'  => 'header',
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'Combine identical footnotes', 'awesome-footnotes' ),
			'id'      => 'combine_identical_notes',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['combine_identical_notes'],
		)
	);

	// Custom CSS.
	Settings::build_option(
		array(
			'title' => esc_html__( 'Styling (CSS)', 'awesome-footnotes' ),
			'id'    => 'markup-format-settings',
			'type'  => 'header',
		)
	);

	Settings::build_option(
		array(
			'name'    => esc_html__( 'CSS footnotes', 'awesome-footnotes' ),
			'id'      => 'css_footnotes',
			'type'    => 'textarea',
			'hint'    => esc_html__( 'You can change the footnotes styling from here or leave it empty if you are using your own.', 'awesome-footnotes' ),
			'default' => Settings::get_current_options()['css_footnotes'],
		)
	);

	Settings::build_option(
		array(
			'type' => 'hint',
			'hint' => '<b><i>' . esc_html__( 'Example:', 'awesome-footnotes' ) . '</i></b><div class="symbol-example">' .
			$footnote_example
			. '</div>',
		)
	);

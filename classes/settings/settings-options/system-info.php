<?php
/**
 * System info section of the plugin settings
 *
 * @package awe
 *
 * @since 2.0.0
 */

use AWEF\Helpers\Settings;
use AWEF\Helpers\System_Status;

Settings::build_option(
	array(
		'title' => esc_html__( 'System Info', 'awesome-footnotes' ),
		'id'    => 'advanced-settings-tab',
		'type'  => 'tab-title',
	)
);

Settings::build_option(
	array(
		'type'  => 'header',
		'id'    => 'advanced-settings',
		'title' => esc_html__( 'Environment Information', 'awesome-footnotes' ),
	)
);

System_Status::print_environment_info();
System_Status::print_plugins_info();
System_Status::print_theme_info();
System_Status::print_report();

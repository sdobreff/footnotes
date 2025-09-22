<?php
/**
 * Uninstaller
 *
 * Uninstall the plugin by removing any options from the database
 *
 * @package  awe
 * @since    1.0
 */

use AWEF\Helpers\Settings;
use AWEF\Helpers\Review_Plugin;

// If the uninstall was not called by WordPress, exit.

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

require_once __DIR__ . '/awesome-footnotes.php';

// Delete any saved data.
\delete_option( AWEF_SETTINGS_NAME );
\delete_option( Settings::SETTINGS_VERSION );
\delete_option( Review_Plugin::REVIEW_OPTION_KEY );

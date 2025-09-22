<?php
/**
 * Footnotes
 *
 * Easily add footnotes and change context of a post
 *
 * @package   footnotes
 * @author    quotecites
 * @copyright Copyright (C) 2023-%%YEAR%%, Footnotes
 * @license   GPL v3
 * @link      https://wordpress.org/plugins/awesome-footnotes/
 *
 * Plugin Name:     Footnotes
 * Description:     Allows post authors to easily add and manage footnotes in posts.
 * Version:         3.8.3
 * Author:          Footnotes
 * Author URI:      https://quotecites.com
 * Text Domain:     awesome-footnotes
 * License:         GPL v3
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires PHP:    7.4
 */

use AWEF\Helpers\Context_Helper;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

define( 'AWEF_VERSION', '3.8.3' );
define( 'AWEF_TEXTDOMAIN', 'awesome-footnotes' );
define( 'AWEF_NAME', 'Footnotes' );
define( 'AWEF_PLUGIN_ROOT', \plugin_dir_path( __FILE__ ) );
define( 'AWEF_PLUGIN_ROOT_URL', \plugin_dir_url( __FILE__ ) );
define( 'AWEF_PLUGIN_BASENAME', \plugin_basename( __FILE__ ) );
define( 'AWEF_PLUGIN_ABSOLUTE', __FILE__ );
define( 'AWEF_MIN_PHP_VERSION', '7.4' );
define( 'AWEF_WP_VERSION', '6.0' );
define( 'AWEF_SETTINGS_NAME', 'awef_footnote_options' );
define( 'AWEF_TEMPLATE_DIR', AWEF_PLUGIN_ROOT . 'templates' . DIRECTORY_SEPARATOR );

if ( version_compare( PHP_VERSION, AWEF_MIN_PHP_VERSION, '<=' ) ) {
	\add_action(
		'admin_init',
		static function () {
			\deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	);
	\add_action(
		'admin_notices',
		static function () {
			echo \wp_kses_post(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						// translators: the minimum version of the PHP required by the plugin.
						__(
							'"%1$s" requires PHP %2$s or newer. Plugin is automatically deactivated.',
							'awesome-footnotes'
						),
						AWEF_NAME,
						AWEF_MIN_PHP_VERSION
					)
				)
			);
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

if ( ! extension_loaded( 'mbstring' ) ) {
	\add_action(
		'admin_init',
		static function () {
			\deactivate_plugins( \plugin_basename( __FILE__ ) );
		}
	);
	\add_action(
		'admin_notices',
		static function () {
			echo \wp_kses_post(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						// translators: the mbstring extensions is required by the plugin.
						__(
							'"%1$s" requires multi byte string extension loaded. Plugin is automatically deactivated.',
							'awesome-footnotes'
						)
					)
				)
			);
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

$plugin_name_libraries = require AWEF_PLUGIN_ROOT . 'vendor/autoload.php';

if ( ! Context_Helper::is_installing() ) {
	\register_activation_hook( AWEF_PLUGIN_ABSOLUTE, array( '\AWEF\Awesome_Footnotes', 'plugin_activate' ) );
	\add_action( 'plugins_loaded', array( '\AWEF\Awesome_Footnotes', 'init' ) );
}

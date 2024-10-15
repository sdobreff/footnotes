<?php
/**
 * Responsible for plugin initialization.
 *
 * @package    awe
 * @copyright  %%YEAR%% Footnotes
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link       https://wordpress.org/plugins/awesome-footnotes/
 *
 * @since      2.0.0
 */

declare(strict_types=1);

namespace AWEF;

use AWEF\Migration\Migration;
use AWEF\Helpers\Settings;
use AWEF\Helpers\Review_Plugin;
use AWEF\Controllers\Integrations;
use AWEF\Controllers\Footnotes_Formatter;
use AWEF\Controllers\Pointers;
use AWEF\Helpers\Ajax;
use AWEF\Helpers\Context_Helper;

if ( ! class_exists( '\AWEF\Awesome_Footnotes' ) ) {

	/**
	 * Main plugin class
	 *
	 * @since 2.0.0
	 */
	class Awesome_Footnotes {

		public const REDIRECT_OPTION_NAME = 'awef_plugin_do_activation_redirect';

		/**
		 * Inits the class and hooks
		 *
		 * @since 2.0.0
		 */
		public static function init() {
			if ( \is_admin() && ! \wp_doing_ajax() ) {
				Migration::migrate();

				\add_action( 'admin_init', array( __CLASS__, 'plugin_redirect' ) );

				\add_action( 'current_screen', array( '\AWEF\Helpers\Upgrade_Notice', 'init' ) );

				\add_filter( 'plugin_action_links', array( __CLASS__, 'add_settings_link' ), 10, 2 );
				\add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_meta' ), 10, 2 );

				Review_Plugin::init();

				Integrations::init();
				Settings::init();

				Pointers::init();

				// Hide all unrelated to the plugin notices on the plugin admin pages.
				\add_action( 'admin_print_scripts', array( __CLASS__, 'hide_unrelated_notices' ) );
			} else {
				Footnotes_Formatter::init();

				\add_action( 'wp_footer', array( __CLASS__, 'powered_by' ), \PHP_INT_MAX );
			}

			Ajax::init();
		}

		/**
		 * Add Settings link to plugin list
		 *
		 * Add a Settings link to the options listed against this plugin
		 *
		 * @param array  $links  Current links.
		 * @param string $file   File in use.
		 *
		 * @return string          Links, now with settings added.
		 *
		 * @since 2.0.0
		 */
		public static function add_settings_link( $links, $file ) {
			if ( AWEF_PLUGIN_BASENAME === $file ) {
				$settings_link = '<a href="' . Settings::get_settings_page_link() . '">' . __( 'Settings', 'awesome-footnotes' ) . '</a>';
				array_unshift( $links, $settings_link );
			}

			return $links;
		}

		/**
		 * Add meta to plugin details
		 *
		 * Add options to plugin meta line
		 *
		 * @param string $links  Current links.
		 * @param string $file   File in use.
		 *
		 * @return string Links, now with settings added.
		 *
		 * @since 2.0.0
		 */
		public static function plugin_meta( $links, $file ) {

			if ( false !== strpos( $file, 'awesome-footnotes.php' ) ) {
				$links = array_merge( $links, array( '<a href="https://wordpress.org/support/plugin/awesome-footnotes">' . __( 'Support', 'awesome-footnotes' ) . '</a>' ) );
			}

			return $links;
		}

		/**
		 * Check whether we are on an admin and plugin page.
		 *
		 * @since 2.0.0
		 *
		 * @return bool
		 */
		public static function is_admin_page() {

			return \is_admin() && ( false !== Settings::is_plugin_settings_page() );
		}

		/**
		 * Remove all non-WP Mail SMTP plugin notices from our plugin pages.
		 *
		 * @since 2.0.0
		 */
		public static function hide_unrelated_notices() {
			// Bail if we're not on our screen or page.
			if ( ! self::is_admin_page() ) {
				return;
			}

			self::remove_unrelated_actions( 'user_admin_notices' );
			self::remove_unrelated_actions( 'admin_notices' );
			self::remove_unrelated_actions( 'all_admin_notices' );
			self::remove_unrelated_actions( 'network_admin_notices' );
		}

		/**
		 * Remove all notices from the our plugin pages based on the provided action hook.
		 *
		 * @since 2.0.0
		 *
		 * @param string $action - The name of the action.
		 */
		public static function remove_unrelated_actions( $action ) {
			global $wp_filter;

			if ( empty( $wp_filter[ $action ]->callbacks ) || ! is_array( $wp_filter[ $action ]->callbacks ) ) {
				return;
			}

			foreach ( $wp_filter[ $action ]->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if (
					( // Cover object method callback case.
						is_array( $arr['function'] ) &&
						isset( $arr['function'][0] ) &&
						is_object( $arr['function'][0] ) &&
						false !== strpos( ( get_class( $arr['function'][0] ) ), 'AWEF' )
					) ||
					( // Cover class static method callback case.
						! empty( $name ) &&
						false !== strpos( ( $name ), 'AWEF' )
					)
					) {
						continue;
					}

					unset( $wp_filter[ $action ]->callbacks[ $priority ][ $name ] );
				}
			}
		}

		/**
		 * Adds a powered-by message in the footer of the page.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function powered_by() {
			if ( Context_Helper::is_front() ) {
				?><!--
				<?php
				printf(
					/* Translators: Plugin link. */
					\esc_html__( 'Proudly powered by %s', 'awesome-footnotes' ),
					'<a href="' . esc_url( __( 'https://wordpress.org/plugins/awesome-footnotes/', 'awesome-footnotes' ) ) . '" rel="nofollow">' . \esc_attr( AWEF_NAME ) . '</a>'
				);
				?>
				-->
				<?php
			}
		}

		/**
		 * Registers a plugin redirection on activate setting.
		 *
		 * @return void
		 *
		 * @since 2.4.0
		 */
		public static function plugin_activate() {
			\add_option( self::REDIRECT_OPTION_NAME, true );
		}

		/**
		 * Redirects the plugin to its settings page if it was just activated.
		 *
		 * @return void
		 *
		 * @since 2.4.0
		 */
		public static function plugin_redirect() {
			if ( \get_option( self::REDIRECT_OPTION_NAME, false ) ) {
				\delete_option( self::REDIRECT_OPTION_NAME );
				if ( ! isset( $_REQUEST['activate-multi'] ) ) {
					\wp_safe_redirect( add_query_arg( 'page', Settings::MENU_SLUG, get_admin_url( get_current_blog_id(), 'admin.php' ) ) );
				}
			}
		}
	}
}

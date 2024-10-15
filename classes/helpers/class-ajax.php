<?php
/**
 * Class: Responsible for AJAX operations in the plugin.
 *
 * Helper class to handle AJAX requests.
 *
 * @package awesome-footnotes
 *
 * @since 3.7.0
 */

declare(strict_types=1);

namespace AWEF\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Helpers\Ajax' ) ) {
	/**
	 * Responsible for handling AJAX requests.
	 *
	 * @since 3.7.0
	 */
	class Ajax {

		/**
		 * Inits the class.
		 *
		 * @return void
		 *
		 * @since 3.7.0
		 */
		public static function init() {
			if ( \is_admin() && \wp_doing_ajax() ) {

				/**
				 * Save Options
				 */
				\add_action( 'wp_ajax_awef_plugin_data_save', array( __CLASS__, 'save_settings_ajax' ) );

			}
		}

		/**
		 * Method responsible for AJAX data saving
		 *
		 * @return void
		 *
		 * @since 3.7.0
		 */
		public static function save_settings_ajax() {

			if ( \check_ajax_referer( 'awef-plugin-data', 'awef-security' ) ) {

				if ( isset( $_POST[ \AWEF_SETTINGS_NAME ] ) && ! empty( $_POST[ \AWEF_SETTINGS_NAME ] ) && \is_array( $_POST[ \AWEF_SETTINGS_NAME ] ) ) {

					$data = array_map( 'sanitize_text_field', \stripslashes_deep( $_POST[ \AWEF_SETTINGS_NAME ] ) );

					if ( isset( $_POST[ \AWEF_SETTINGS_NAME ]['css_footnotes'] ) ) {
						$data['css_footnotes'] = \_sanitize_text_fields( \wp_unslash( $_POST[ \AWEF_SETTINGS_NAME ]['css_footnotes'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}

					if ( isset( $_POST[ \AWEF_SETTINGS_NAME ]['pre_footnotes'] ) ) {
						$data['pre_footnotes'] = \wpautop( \wp_unslash( $_POST[ \AWEF_SETTINGS_NAME ]['pre_footnotes'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}

					if ( isset( $_POST[ \AWEF_SETTINGS_NAME ]['post_footnotes'] ) ) {
						$data['post_footnotes'] = \wpautop( \wp_unslash( $_POST[ \AWEF_SETTINGS_NAME ]['post_footnotes'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}
					\update_option( AWEF_SETTINGS_NAME, Settings::collect_and_sanitize_options( $data ) );

					\wp_send_json_success( 2 );
				}
				\wp_die();
			}
		}
	}
}

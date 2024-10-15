<?php
/**
 * Class: Adds integrations needed for the plugin to operate.
 *
 * @package awesome-footnotes
 *
 * @since 2.0.0
 */

declare(strict_types=1);

namespace AWEF\Controllers;

use AWEF\Helpers\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Controllers\Integrations' ) ) {
	/**
	 * Responsible for proper context determination.
	 *
	 * @since 2.0.0
	 */
	class Integrations {

		/**
		 * Inits the class.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function init() {
			if ( \is_admin() ) {
				\add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'block_editor_button' ) );

				\add_filter( 'init', array( __CLASS__, 'add_container_button' ) );

				\add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

				\add_action( 'admin_head', array( __CLASS__, 'options_dark_skin' ) );

				foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
					\add_action( "admin_head-$hook", array( __CLASS__, 'admin_head' ) );
				}
			}
		}

		/**
		 * Adds JS to set the Dark Skin based on user selections (in browser local storage)
		 *
		 * @return void
		 *
		 * @since 3.8.0
		 */
		public static function options_dark_skin() {
			if ( Settings::is_plugin_settings_page() ) {
				?>
				<script>
					if( 'undefined' != typeof localStorage ){
						var skin = localStorage.getItem('awef-backend-skin');
						if( skin == 'dark' ){
							var html = document.getElementsByTagName('html')[0].classList;
							html.add('awef-darkskin');
						}
					}
				</script>
				<?php
			}
		}

		/**
		 * Initialize the script in the admin header.
		 * There is no other way to pass the php variables to the JS script.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function admin_head() {
			?>
			<!-- TinyMCE Shortcode Plugin -->
			<script>
			var awef_gut = {
				'open' : '<?php echo Settings::get_current_options()['footnotes_open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
				'close' : '<?php echo Settings::get_current_options()['footnotes_close']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
			};
			</script>
			<!-- TinyMCE Shortcode Plugin -->
			<?php
		}

		/**
		 * Calls the specified hooks to execute when classical editor is in use.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function add_container_button() {
			if ( ! \current_user_can( 'edit_posts' ) && ! \current_user_can( 'edit_pages' ) ) {
				return;
			}
			if ( true === (bool) \get_user_option( 'rich_editing' ) ) {
				\add_filter( 'mce_external_plugins', array( __CLASS__, 'add_container_plugin' ) );
				\add_filter( 'mce_buttons', array( __CLASS__, 'register_container_button' ) );
			}
		}

		/**
		 * Adds the necessary styles to the admin
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function enqueue_admin_styles() {
			\wp_enqueue_style(
				'footnotes-gutenberg_css',
				\AWEF_PLUGIN_ROOT_URL . '/css/footnotes-gutenberg.css',
				array(),
				\AWEF_VERSION
			);
		}

		/**
		 * Registers the MCE container buttons
		 *
		 * @param array $buttons - Array with all of the MCE buttons.
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 */
		public static function register_container_button( array $buttons ): array {
			array_push( $buttons, \AWEF_TEXTDOMAIN );

			return $buttons;
		}

		/**
		 * Adds the plugin script to the MCE editor
		 *
		 * @param array $plugin_array - The array with all the registered plugins.
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 */
		public static function add_container_plugin( array $plugin_array ): array {

			$plugin_array[ \AWEF_TEXTDOMAIN ] = \AWEF_PLUGIN_ROOT_URL . 'js/footnotes-mce.js';

			return $plugin_array;
		}

		/**
		 * Fires and includes the assets needed for the gutenberg editor.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function block_editor_button() {

			$current_screen = \get_current_screen();
			if ( 'widgets' === $current_screen->id ) {
				return;
			}

			\wp_register_script(
				'footnotes-gutenberg_js',
				\AWEF_PLUGIN_ROOT_URL . 'js/footnotes-gutenberg.js',
				array( 'wp-rich-text', 'wp-element', 'wp-block-editor', 'wp-i18n' ),
				\AWEF_VERSION,
				true
			);

			\wp_localize_script(
				'footnotes-gutenberg_js',
				'awef_gut',
				array(
					'open'  => Settings::get_current_options()['footnotes_open'],
					'close' => Settings::get_current_options()['footnotes_close'],
				)
			);

			\wp_enqueue_script(
				'footnotes-gutenberg_js'
			);
			\wp_set_script_translations(
				'footnotes-gutenberg_js',
				\AWEF_TEXTDOMAIN
			);
			\wp_enqueue_style(
				'footnotes-gutenberg_css',
				\AWEF_PLUGIN_ROOT_URL . '/css/footnotes-gutenberg.css',
				array(),
				\AWEF_VERSION
			);
		}
	}
}

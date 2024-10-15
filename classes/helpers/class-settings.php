<?php
/**
 * Class: Determine the context in which the plugin is executed.
 *
 * Helper class to determine the proper status of the request.
 *
 * @package awesome-footnotes
 *
 * @since 2.0.0
 */

declare(strict_types=1);

namespace AWEF\Helpers;

use AWEF\Controllers\Post_Settings;
use AWEF\Settings\Settings_Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Helpers\Settings' ) ) {
	/**
	 * Responsible for proper context determination.
	 *
	 * @since 2.0.0
	 */
	class Settings {

		public const OPTIONS_VERSION = '12'; // Incremented when the options array changes.

		public const MENU_SLUG = 'awef_settings';

		public const OPTIONS_PAGE_SLUG = 'footnotes-options-page';

		public const SETTINGS_FILE_FIELD = 'awef_import_file';

		public const SETTINGS_FILE_UPLOAD_FIELD = 'awef_import_upload';

		public const SETTINGS_VERSION = 'awef_plugin_version';

		/**
		 * Array with the current options
		 *
		 * @var array
		 *
		 * @since 2.0.0
		 */
		private static $current_options = array();

		/**
		 * Array with the global options
		 *
		 * @var array
		 *
		 * @since 3.8.0
		 */
		private static $global_options = array();

		/**
		 * Array with the default options
		 *
		 * @var array
		 *
		 * @since 2.0.0
		 */
		private static $default_options = array();

		/**
		 * The link to the WP admin settings page
		 *
		 * @var string
		 */
		private static $settings_page_link = '';

		/**
		 * The current version of the plugin
		 *
		 * @var string
		 */
		private static $current_version = '';

		/**
		 * Global post variable - keeps track of current post. Used to null to position if current post changes. Used when in loops.
		 *
		 * @var integer
		 *
		 * @since 3.8.0
		 */
		public static $current_post = 0;

		/**
		 * Inits the class.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function init() {

			// self::get_current_options();

			// Hook me up.
			\add_action( 'admin_menu', array( __CLASS__, 'add_options_page' ) ); // Insert the Admin panel.

			/**
			 * Draws the save button in the settings
			 */
			\add_action( 'awef_settings_save_button', array( __CLASS__, 'save_button' ) );

			Post_Settings::init();
		}

		/**
		 * Collects the passed options, validates them and stores them.
		 *
		 * @param array $post_array - The collected settings array.
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 */
		public static function collect_and_sanitize_options( array $post_array ): array {
			if ( ! \current_user_can( 'manage_options' ) ) {
				\wp_die( \esc_html__( 'You do not have sufficient permissions to access this page.', 'awesome-footnotes' ) );
			}

			$footnotes_options = array();

			$footnotes_options['superscript'] = ( array_key_exists( 'superscript', $post_array ) ) ? filter_var( $post_array['superscript'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['pre_backlink']  = ( array_key_exists( 'pre_backlink', $post_array ) ) ? sanitize_text_field( $post_array['pre_backlink'] ) : '';
			$footnotes_options['backlink']      = ( array_key_exists( 'backlink', $post_array ) ) ? sanitize_text_field( $post_array['backlink'] ) : '';
			$footnotes_options['post_backlink'] = ( array_key_exists( 'post_backlink', $post_array ) ) ? sanitize_text_field( $post_array['post_backlink'] ) : '';

			$footnotes_options['pre_identifier']        = ( array_key_exists( 'pre_identifier', $post_array ) ) ? \sanitize_text_field( $post_array['pre_identifier'] ) : '';
			$footnotes_options['inner_pre_identifier']  = ( array_key_exists( 'inner_pre_identifier', $post_array ) ) ? \sanitize_text_field( $post_array['inner_pre_identifier'] ) : '';
			$footnotes_options['list_style_type']       = ( array_key_exists( 'list_style_type', $post_array ) ) ? \sanitize_text_field( $post_array['list_style_type'] ) : '';
			$footnotes_options['inner_post_identifier'] = ( array_key_exists( 'inner_post_identifier', $post_array ) ) ? \sanitize_text_field( $post_array['inner_post_identifier'] ) : '';
			$footnotes_options['post_identifier']       = ( array_key_exists( 'post_identifier', $post_array ) ) ? \sanitize_text_field( $post_array['post_identifier'] ) : '';
			$footnotes_options['list_style_symbol']     = ( array_key_exists( 'list_style_symbol', $post_array ) ) ? \sanitize_text_field( $post_array['list_style_symbol'] ) : '';

			$footnotes_options['pre_footnotes'] = ( array_key_exists( 'pre_footnotes', $post_array ) ) ? \wpautop( $post_array['pre_footnotes'], true ) : '';

			$footnotes_options['post_footnotes'] = ( array_key_exists( 'post_footnotes', $post_array ) ) ? \wpautop( $post_array['post_footnotes'], true ) : '';

			$footnotes_options['position_before_footnote'] = ( array_key_exists( 'position_before_footnote', $post_array ) ) ? filter_var( $post_array['position_before_footnote'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_home']          = ( array_key_exists( 'no_display_home', $post_array ) ) ? filter_var( $post_array['no_display_home'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_preview']       = ( array_key_exists( 'no_display_preview', $post_array ) ) ? filter_var( $post_array['no_display_preview'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_archive']       = ( array_key_exists( 'no_display_archive', $post_array ) ) ? filter_var( $post_array['no_display_archive'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_date']          = ( array_key_exists( 'no_display_date', $post_array ) ) ? filter_var( $post_array['no_display_date'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_category']      = ( array_key_exists( 'no_display_category', $post_array ) ) ? filter_var( $post_array['no_display_category'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_search']        = ( array_key_exists( 'no_display_search', $post_array ) ) ? filter_var( $post_array['no_display_search'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['no_display_feed']          = ( array_key_exists( 'no_display_feed', $post_array ) ) ? filter_var( $post_array['no_display_feed'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['no_editor_header_footer'] = ( array_key_exists( 'no_editor_header_footer', $post_array ) ) ? filter_var( $post_array['no_editor_header_footer'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['combine_identical_notes'] = ( array_key_exists( 'combine_identical_notes', $post_array ) ) ? filter_var( $post_array['combine_identical_notes'], FILTER_VALIDATE_BOOLEAN ) : false;
			$footnotes_options['priority']                = ( array_key_exists( 'priority', $post_array ) ) ? \sanitize_text_field( $post_array['priority'] ) : self::get_default_options()['priority'];

			$footnotes_options['footnotes_open']  = ( array_key_exists( 'footnotes_open', $post_array ) ) ? \sanitize_text_field( $post_array['footnotes_open'] ) : self::get_default_options()['footnotes_open']; // That one can not be without a value.
			$footnotes_options['footnotes_close'] = ( array_key_exists( 'footnotes_close', $post_array ) ) ? \sanitize_text_field( $post_array['footnotes_close'] ) : self::get_default_options()['footnotes_close']; // That one can not be without a value.

			$footnotes_options['pretty_tooltips'] = ( array_key_exists( 'pretty_tooltips', $post_array ) ) ? filter_var( $post_array['pretty_tooltips'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['vanilla_js_tooltips'] = ( array_key_exists( 'vanilla_js_tooltips', $post_array ) ) ? filter_var( $post_array['vanilla_js_tooltips'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['back_link_title'] = ( array_key_exists( 'back_link_title', $post_array ) ) ? \sanitize_text_field( $post_array['back_link_title'] ) : '';
			$footnotes_options['css_footnotes']   = ( array_key_exists( 'css_footnotes', $post_array ) ) ? \_sanitize_text_fields( $post_array['css_footnotes'], true ) : '';

			$footnotes_options['no_display_post'] = ( array_key_exists( 'no_display_post', $post_array ) ) ? filter_var( $post_array['no_display_post'], FILTER_VALIDATE_BOOLEAN ) : false;

			$footnotes_options['version'] = self::OPTIONS_VERSION;

			$footnotes_options['no_posts_footnotes'] = ( array_key_exists( 'no_posts_footnotes', $post_array ) ) ? filter_var( $post_array['no_posts_footnotes'], FILTER_VALIDATE_BOOLEAN ) : false;

			// add_settings_error(AWEF_SETTINGS_NAME, '<field_name>', 'Please enter a valid email!', $type = 'error'); .

			// update_option( AWEF_SETTINGS_NAME, $footnotes_options ); .

			self::$current_options = $footnotes_options;

			return $footnotes_options;
		}

		/**
		 * Returns the global options.
		 *
		 * @return array
		 *
		 * @since 3.8.0
		 */
		public static function get_global_options(): array {
			if ( empty( self::$global_options ) ) {

				// Get the current settings or setup some defaults if needed.
				self::$global_options = \get_option( AWEF_SETTINGS_NAME );
				if ( ! self::$global_options ) {

					self::$global_options = self::get_default_options();
					\update_option( AWEF_SETTINGS_NAME, self::$global_options );
				} elseif ( ! isset( self::$global_options['version'] ) || self::OPTIONS_VERSION !== self::$global_options['version'] ) {

					// Set any unset options.
					foreach ( self::get_default_options() as $key => $value ) {
						if ( ! isset( self::$global_options[ $key ] ) ) {
							self::$global_options[ $key ] = $value;
						}
					}

					\update_option( AWEF_SETTINGS_NAME, self::$global_options );
				}

				self::$global_options['footnotes_open']  = ( array_key_exists( 'footnotes_open', self::$global_options ) && ! empty( self::$global_options['footnotes_open'] ) ) ? self::$global_options['footnotes_open'] : self::get_default_options()['footnotes_open']; // That one can not be without a value.
				self::$global_options['footnotes_close'] = ( array_key_exists( 'footnotes_close', self::$global_options ) && ! empty( self::$global_options['footnotes_close'] ) ) ? self::$global_options['footnotes_close'] : self::get_default_options()['footnotes_close']; // That one can not be without a value.
			}

			return self::$global_options;
		}

		/**
		 * Returns the current options.
		 * Fills the current options array with values if empty.
		 *
		 * @param \WP_Post $post - The post object to extract settings from.
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 * @since 3.8.0 - The WP_Post option is added in order to override the the settings (if needed) with the once stored in the current post object (if passed.)
		 */
		public static function get_current_options( \WP_Post $post = null ): array {
			if ( empty( self::$current_options ) ) {

				// Get the current settings or setup some defaults if needed.
				self::$current_options = \get_option( AWEF_SETTINGS_NAME );
				if ( ! self::$current_options ) {

					self::$current_options = self::get_default_options();
					\update_option( AWEF_SETTINGS_NAME, self::$current_options );
				} elseif ( ! isset( self::$current_options['version'] ) || self::OPTIONS_VERSION !== self::$current_options['version'] ) {

					// Set any unset options.
					foreach ( self::get_default_options() as $key => $value ) {
						if ( ! isset( self::$current_options[ $key ] ) ) {
							self::$current_options[ $key ] = $value;
						}
					}

					\update_option( AWEF_SETTINGS_NAME, self::$current_options );
				}

				if ( ! self::$current_options['no_posts_footnotes'] ) {
					if ( null === $post ) {
						global $post;
					}

					/**
					 * If there is a post object and there are settings stored assigned to it, that takes precedence.
					 */
					if ( isset( $post ) && \is_a( $post, '\WP_Post' ) && \property_exists( $post, 'ID' )
					&& self::$current_post !== $post ) {

						self::$current_post = $post;

						$post_settings = \get_post_meta( $post->ID, Post_Settings::POST_SETTINGS_NAME, true );

						if ( isset( $post_settings ) && ! empty( $post_settings ) ) {
							self::$current_options = \array_merge( self::$current_options, $post_settings );
						}
					}

					self::$current_options['footnotes_open']  = ( array_key_exists( 'footnotes_open', self::$current_options ) && ! empty( self::$current_options['footnotes_open'] ) ) ? self::$current_options['footnotes_open'] : self::get_default_options()['footnotes_open']; // That one can not be without a value.
					self::$current_options['footnotes_close'] = ( array_key_exists( 'footnotes_close', self::$current_options ) && ! empty( self::$current_options['footnotes_close'] ) ) ? self::$current_options['footnotes_close'] : self::get_default_options()['footnotes_close']; // That one can not be without a value.
				}
			} elseif ( ! self::$current_options['no_posts_footnotes'] ) {
				/**
				 * Sometimes the init hits early and the settings are taken from the global options. With the following we are trying to merge the settings later on with the global post settings (if one is present)
				 */
				global $post;

				/**
				 * If there is a post object and there are settings stored assigned to it, that takes precedence.
				 */
				if ( isset( $post ) && \is_a( $post, '\WP_Post' ) && \property_exists( $post, 'ID' )
				&& self::$current_post !== $post ) {

					self::$current_post = $post;

					$post_settings = \get_post_meta( $post->ID, Post_Settings::POST_SETTINGS_NAME, true );

					if ( isset( $post_settings ) && ! empty( $post_settings ) ) {
						self::$current_options = \array_merge( self::$current_options, $post_settings );

						self::$current_options['footnotes_open']  = ( array_key_exists( 'footnotes_open', self::$current_options ) && ! empty( self::$current_options['footnotes_open'] ) ) ? self::$current_options['footnotes_open'] : self::get_default_options()['footnotes_open']; // That one can not be without a value.
						self::$current_options['footnotes_close'] = ( array_key_exists( 'footnotes_close', self::$current_options ) && ! empty( self::$current_options['footnotes_close'] ) ) ? self::$current_options['footnotes_close'] : self::get_default_options()['footnotes_close']; // That one can not be without a value.
					}
				}
			}

			return self::$current_options;
		}

		/**
		 * Returns the default plugin options
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 */
		public static function get_default_options(): array {

			if ( empty( self::$default_options ) ) {
				// Define default options.
				self::$default_options = array(
					'superscript'              => true,
					'pre_backlink'             => '[',
					'backlink'                 => '&#8617;',
					'post_backlink'            => ']',
					'pre_identifier'           => '',
					'inner_pre_identifier'     => '',
					'list_style_type'          => 'decimal',
					'list_style_symbol'        => '&dagger;',
					'inner_post_identifier'    => '',
					'post_identifier'          => '',
					'pre_footnotes'            => '',
					'post_footnotes'           => '',
					'no_display_home'          => false,
					'no_display_preview'       => false,
					'no_display_archive'       => false,
					'no_display_date'          => false,
					'no_display_category'      => false,
					'no_display_search'        => false,
					'no_display_feed'          => false,
					'combine_identical_notes'  => true,
					'priority'                 => 11,
					'footnotes_open'           => '((',
					'footnotes_close'          => '))',
					'pretty_tooltips'          => false,
					'vanilla_js_tooltips'      => false,
					'version'                  => self::OPTIONS_VERSION,
					'back_link_title'          => \__( 'Jump back to text', 'awesome-footnotes' ),
					'css_footnotes'            => 'ol.footnotes { color:#666666; }' . "\n" . 'ol.footnotes li { font-size:80%; }',
					'no_editor_header_footer'  => false,
					'no_display_post'          => false,
					'position_before_footnote' => false,
					'no_posts_footnotes'       => false,
				);
			}

			return self::$default_options;
		}

		/**
		 * Add to Admin
		 *
		 * Add the options page to the admin menu
		 *
		 * @since 2.0.0
		 */
		public static function add_options_page() {

			$base = 'base';

			global $footnotes_hook;

			$base .= '64_en';

			$base .= 'code';

			\add_menu_page(
				\esc_html__( 'Awesome Footnotes', 'awesome-footnotes' ),
				\esc_html__( 'Footnotes', 'awesome-footnotes' ),
				'manage_options',
				self::MENU_SLUG,
				array( __CLASS__, 'footnotes_options_page' ),
				'data:image/svg+xml;base64,' . $base( file_get_contents( \AWEF_PLUGIN_ROOT . 'assets/icon.svg' ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				30
			);

			\register_setting(
				\AWEF_SETTINGS_NAME,
				\AWEF_SETTINGS_NAME,
				array(
					'\AWEF\Helpers\Settings',
					'collect_and_sanitize_options',
				)
			);

			\add_action( 'load-' . $footnotes_hook, array( __CLASS__, 'footnotes_help' ) );

			if ( ! self::is_plugin_settings_page() ) {
				return;
			}

			// Reset settings.
			if ( isset( $_REQUEST['reset-settings'] ) && \check_admin_referer( 'reset-plugin-settings', 'reset_nonce' ) ) {

				\delete_option( AWEF_SETTINGS_NAME );

				// Redirect to the plugin settings page.
				\wp_safe_redirect(
					\add_query_arg(
						array(
							'page'  => self::MENU_SLUG,
							'reset' => 'true',
						),
						\admin_url( 'admin.php' )
					)
				);
				exit;
			} elseif ( isset( $_REQUEST['export-settings'] ) && \check_admin_referer( 'export-plugin-settings', 'export_nonce' ) ) { // Export Settings.

				global $wpdb;

				$stored_options = $wpdb->get_results(
					$wpdb->prepare( 'SELECT option_name, option_value FROM ' . $wpdb->options . ' WHERE option_name = %s', \AWEF_SETTINGS_NAME )
				);

				header( 'Cache-Control: public, must-revalidate' );
				header( 'Pragma: hack' );
				header( 'Content-Type: text/plain' );
				header( 'Content-Disposition: attachment; filename="' . AWEF_TEXTDOMAIN . '-options-' . gmdate( 'dMy' ) . '.dat"' );
				echo \wp_json_encode( unserialize( $stored_options[0]->option_value ) );
				die();
			} elseif ( isset( $_FILES[ self::SETTINGS_FILE_FIELD ] ) && \check_admin_referer( 'awef-plugin-data', 'awef-security' ) ) { // Import the settings.
				if ( isset( $_FILES ) &&
				isset( $_FILES[ self::SETTINGS_FILE_FIELD ] ) &&
				isset( $_FILES[ self::SETTINGS_FILE_FIELD ]['error'] ) &&
				! $_FILES[ self::SETTINGS_FILE_FIELD ]['error'] > 0 &&
				isset( $_FILES[ self::SETTINGS_FILE_FIELD ]['tmp_name'] ) ) {
					global $wp_filesystem;

					if ( null === $wp_filesystem ) {
						\WP_Filesystem();
					}

					if ( $wp_filesystem->exists( \sanitize_text_field( \wp_unslash( $_FILES[ self::SETTINGS_FILE_FIELD ]['tmp_name'] ) ) ) ) {
						$options = json_decode( $wp_filesystem->get_contents( \sanitize_text_field( \wp_unslash( $_FILES[ self::SETTINGS_FILE_FIELD ]['tmp_name'] ) ) ), true );
					}

					if ( ! empty( $options ) && is_array( $options ) ) {
						\update_option( AWEF_SETTINGS_NAME, self::collect_and_sanitize_options( $options ) );
					}
				}

				\wp_safe_redirect(
					\add_query_arg(
						array(
							'page'   => self::MENU_SLUG,
							'import' => 'true',
						),
						\admin_url( 'admin.php' )
					)
				);
				exit;
			}
		}

		/**
		 * Options Page
		 *
		 * Get the options and display the page
		 *
		 * @since 2.0.0
		 */
		public static function footnotes_options_page() {
			self::render();
		}

		/**
		 * Displays the settings page.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function render() {
			\wp_enqueue_script( 'awef-admin-scripts', \AWEF_PLUGIN_ROOT_URL . 'js/admin/awef-settings.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'wp-color-picker', 'jquery-ui-autocomplete' ), \AWEF_VERSION, false );
			\wp_enqueue_style( 'awef-admin-style', \AWEF_PLUGIN_ROOT_URL . 'css/admin/style.css', array(), \AWEF_VERSION, 'all' );

			self::awef_show_options();
		}

		/**
		 * Add Options Help
		 *
		 * Add help tab to options screen
		 *
		 * @since 2.0.0
		 */
		public static function footnotes_help() {

			global $footnotes_hook;
			$screen = \get_current_screen();

			if ( $screen->id !== $footnotes_hook ) {
				return; }

			$screen->add_help_tab(
				array(
					'id'      => 'footnotes-help-tab',
					'title'   => __( 'Help', 'awesome-footnotes' ),
					'content' => self::add_help_content(),
				)
			);

			$screen->set_help_sidebar( self::add_sidebar_content() );
		}

		/**
		 * Options Help
		 *
		 * Return help text for options screen
		 *
		 * @return string  Help Text
		 *
		 * @since 2.0.0
		 */
		public static function add_help_content() {

			$help_text  = '<p>' . __( 'This screen allows you to specify the default options for the Awesome Footnotes plugin.', 'awesome-footnotes' ) . '</p>';
			$help_text .= '<p>' . __( 'The identifier is what appears when a footnote is inserted into your page contents. The back-link appear after each footnote, linking back to the identifier.', 'awesome-footnotes' ) . '</p>';
			$help_text .= '<p>' . __( 'Remember to click the Save Changes button at the bottom of the screen for new settings to take effect.', 'awesome-footnotes' ) . '</p></h4>';

			return $help_text;
		}

		/**
		 * Options Help Sidebar
		 *
		 * Add a links sidebar to the options help
		 *
		 * @return string  Help Text
		 *
		 * @since 2.0.0
		 */
		public static function add_sidebar_content() {

			$help_text  = '<p><strong>' . __( 'For more information:', 'awesome-footnotes' ) . '</strong></p>';
			$help_text .= '<p><a href="https://wordpress.org/plugins/awesome-footnotes/">' . __( 'Instructions', 'awesome-footnotes' ) . '</a></p>';
			$help_text .= '<p><a href="https://wordpress.org/support/plugin/awesome-footnotes">' . __( 'Support Forum', 'awesome-footnotes' ) . '</a></p></h4>';

			return $help_text;
		}

		/**
		 * Returns the link to the WP admin settings page, based on the current WP install
		 *
		 * @return string
		 *
		 * @since 1.6.0
		 */
		public static function get_settings_page_link() {
			if ( '' === self::$settings_page_link ) {
				self::$settings_page_link = \add_query_arg( 'page', self::MENU_SLUG, \network_admin_url( 'admin.php' ) );
			}

			return self::$settings_page_link;
		}

		/**
		 * Shows the save button in the settings
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function save_button() {

			?>
			<div class="awef-panel-submit">
				<button name="save" class="awef-save-button awef-primary-button button button-primary button-hero"
						type="submit"><?php esc_html_e( 'Save Changes', 'awesome-footnotes' ); ?></button>
			</div>
			<?php
		}

		/**
		 * The Settings Panel UI
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function awef_show_options() {

			\wp_enqueue_media();

			$settings_tabs = array(

				'head-post'   => esc_html__( 'Footnotes', 'awesome-footnotes' ),

				'general'     => array(
					'icon'  => 'admin-generic',
					'title' => esc_html__( 'General', 'awesome-footnotes' ),
				),

				'formatting'  => array(
					'icon'  => 'media-text',
					'title' => esc_html__( 'Formatting', 'awesome-footnotes' ),
				),

				'options'     => array(
					'icon'  => 'admin-settings',
					'title' => esc_html__( 'Options', 'awesome-footnotes' ),
				),

				'head-global' => esc_html__( 'Global Settings', 'awesome-footnotes' ),

				'advanced'    => array(
					'icon'  => 'admin-tools',
					'title' => esc_html__( 'Advanced', 'awesome-footnotes' ),
				),

				'backup'      => array(
					'icon'  => 'migrate',
					'title' => esc_html__( 'Export/Import', 'awesome-footnotes' ),
				),

				'system-info' => array(
					'icon'  => 'wordpress-alt',
					'title' => esc_html__( 'System Info', 'awesome-footnotes' ),
				),
			);

			?>

			<div id="awef-page-overlay"></div>

			<div id="awef-saving-settings">
				<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
					<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
					<path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
					<path class="checkmark__error_1" d="M38 38 L16 16 Z" />
					<path class="checkmark__error_2" d="M16 38 38 16 Z" />
				</svg>
			</div>

			<div class="awef-panel wrap">

				<div class="awef-panel-tabs">
					<div class="awef-logo">
						<svg fill="currentColor" height="800px" width="800px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 512.001 512.001" xml:space="preserve">
							<g>
								<g>
									<path d="M510.674,193.267l-75.466-130.71c-2.735-4.734-8.788-6.359-13.525-3.624l-78.83,45.513V17.381
										c0-5.467-4.434-9.901-9.901-9.901H182.019c-5.467,0-9.901,4.434-9.901,9.901v90.494l-81.8-47.227
										c-4.738-2.734-10.79-1.112-13.525,3.624L1.327,194.982c-1.313,2.274-1.669,4.976-0.989,7.513c0.679,2.537,2.339,4.699,4.613,6.012
										l78.831,45.513l-75.4,43.533c-4.736,2.735-6.359,8.789-3.624,13.525l75.465,130.71c2.735,4.736,8.79,6.36,13.525,3.624
										l78.37-45.247v94.455c0,5.467,4.434,9.901,9.901,9.901h150.932c5.469,0,9.901-4.434,9.902-9.901v-91.026l75.4,43.533
										c2.273,1.313,4.975,1.67,7.513,0.989c2.537-0.679,4.699-2.339,6.012-4.613l75.465-130.71c2.735-4.736,1.112-10.79-3.624-13.525
										l-78.37-45.248l81.801-47.228c2.274-1.313,3.933-3.474,4.613-6.012C512.344,198.244,511.987,195.542,510.674,193.267z
										M400.496,245.447c-3.063,1.768-4.951,5.038-4.951,8.574c0,3.537,1.887,6.806,4.951,8.575l84.648,48.872l-65.564,113.56
										l-81.677-47.157c-3.063-1.769-6.838-1.769-9.901,0c-3.063,1.768-4.951,5.038-4.951,8.575v98.274h-131.13V383.016
										c0-3.537-1.887-6.806-4.951-8.574c-3.063-1.769-6.838-1.769-9.901,0l-84.648,48.871l-65.564-113.56l81.677-47.157
										c3.063-1.768,4.951-5.038,4.951-8.574c0-3.537-1.887-6.806-4.951-8.574l-85.108-49.137L88.991,82.748l88.078,50.852
										c3.063,1.769,6.838,1.769,9.901,0c3.063-1.768,4.951-5.038,4.951-8.575V27.282h131.13v94.313c0,3.537,1.887,6.806,4.951,8.574
										c3.063,1.769,6.838,1.769,9.901,0l85.107-49.137l65.565,113.562L400.496,245.447z"/>
								</g>
							</g>
							<g>
								<g>
									<path d="M213.92,76.788c-5.467,0-9.901,4.434-9.901,9.901v46.536c0,5.467,4.434,9.901,9.901,9.901s9.901-4.434,9.901-9.901V86.689
										C223.821,81.222,219.388,76.788,213.92,76.788z"/>
								</g>
							</g>
							<g>
								<g>
									<path d="M213.92,40.153c-5.467,0-9.901,4.434-9.901,9.901v5.941c0,5.467,4.434,9.901,9.901,9.901s9.901-4.434,9.901-9.901v-5.941
										C223.821,44.587,219.388,40.153,213.92,40.153z"/>
								</g>
							</g>
						</svg>
					</div>

					<ul>
					<?php
					foreach ( $settings_tabs as $tab => $settings ) {

						if ( ! empty( $settings['title'] ) ) {
							$icon  = $settings['icon'];
							$title = $settings['title'];
							?>

							<li class="awef-tabs awef-options-tab-<?php echo \esc_attr( $tab ); ?>">
								<a href="#awef-options-tab-<?php echo \esc_attr( $tab ); ?>">
									<span class="dashicons-before dashicons-<?php echo \esc_html( $icon ); ?> awef-icon-menu"></span>
								<?php echo \esc_html( $title ); ?>
								</a>
							</li>
						<?php } else { ?>
							<li class="awef-tab-menu-head"><?php echo $settings; ?></li>
							<?php
						}
					}

					?>
					</ul>
					<div class="clear"></div>
				</div> <!-- .awef-panel-tabs -->

				<div class="awef-panel-content">

					<form method="post" name="awef_form" id="awef_form" enctype="multipart/form-data">

						<div class="awef-tab-head">
							<div id="awef-options-search-wrap">
								<input id="awef-panel-search" type="text" placeholder="<?php esc_html_e( 'Search', 'awesome-footnotes' ); ?>">
								<div id="awef-search-list-wrap" class="has-custom-scroll">
									<ul id="awef-search-list"></ul>
								</div>
							</div>

							<div class="awefpanel-head-elements">

							<?php do_action( 'awef_settings_save_button' ); ?>

							
								<ul>
									<li>
										<div id="awefpanel-darkskin-wrap">
											<span class="darkskin-label"><svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><title/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="256" x2="256" y1="48" y2="96"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="256" x2="256" y1="416" y2="464"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="403.08" x2="369.14" y1="108.92" y2="142.86"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="142.86" x2="108.92" y1="369.14" y2="403.08"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="464" x2="416" y1="256" y2="256"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="96" x2="48" y1="256" y2="256"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="403.08" x2="369.14" y1="403.08" y2="369.14"/><line style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="142.86" x2="108.92" y1="142.86" y2="108.92"/><circle cx="256" cy="256" r="80" style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px"/></svg></span>
											<input id="awefpanel-darkskin" class="awef-js-switch" type="checkbox" value="true">
											<span class="darkskin-label"><svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><title/><path d="M160,136c0-30.62,4.51-61.61,16-88C99.57,81.27,48,159.32,48,248c0,119.29,96.71,216,216,216,88.68,0,166.73-51.57,200-128-26.39,11.49-57.38,16-88,16C256.71,352,160,255.29,160,136Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg></span>
											<script>
												if( 'undefined' != typeof localStorage ){
													var skin = localStorage.getItem('awef-backend-skin');
													if( skin == 'dark' ){
														document.getElementById('awefpanel-darkskin').setAttribute('checked', 'checked');
													}
												}
											</script>
										</div>
									</li>

								</ul>
							</div>
						</div>

						<?php
						foreach ( $settings_tabs as $tab => $settings ) {
							if ( ! empty( $settings['title'] ) ) {
								?>
						<!-- <?php echo \esc_attr( $tab ); ?> Settings -->
						<div id="awef-options-tab-<?php echo \esc_attr( $tab ); ?>" class="tabs-wrap">

								<?php
								include_once \AWEF_PLUGIN_ROOT . 'classes/settings/settings-options/' . $tab . '.php';

								\do_action( 'awef_plugin_options_tab_' . $tab );
								?>

						</div>
								<?php
							}
						}
						?>

						<?php \wp_nonce_field( 'awef-plugin-data', 'awef-security' ); ?>
						<input type="hidden" name="action" value="awef_plugin_data_save" />

						<div class="awef-footer">

						<?php \do_action( 'awef_settings_save_button' ); ?>
						</div>
					</form>

				</div><!-- .awef-panel-content -->
				<div class="clear"></div>

			</div><!-- .awef-panel -->

						<?php
		}

		/**
		 * The settings panel option tabs.
		 *
		 * @return array
		 *
		 * @since 2.0.0
		 */
		public static function build_options_tabs(): array {

			$settings_tabs = array(

				'general'       => array(
					'icon'  => 'admin-generic',
					'title' => \esc_html__( 'General', 'awesome-footnotes' ),
				),

				'logo'          => array(
					'icon'  => 'lightbulb',
					'title' => \esc_html__( 'Logo', 'awesome-footnotes' ),
				),

				'posts'         => array(
					'icon'  => 'media-text',
					'title' => \esc_html__( 'Article types', 'awesome-footnotes' ),
				),

				'footer'        => array(
					'icon'  => 'editor-insertmore',
					'title' => \esc_html__( 'Footer', 'awesome-footnotes' ),
				),

				'seo'           => array(
					'icon'  => 'google',
					'title' => \esc_html__( 'SEO', 'awesome-footnotes' ),
				),

				'optimization'  => array(
					'icon'  => 'dashboard',
					'title' => \esc_html__( 'Optimization', 'awesome-footnotes' ),
				),

				'miscellaneous' => array(
					'icon'  => 'shortcode',
					'title' => \esc_html__( 'Miscellaneous', 'awesome-footnotes' ),
				),
			);

			$settings_tabs['backup'] = array(
				'icon'  => 'migrate',
				'title' => \esc_html__( 'Export/Import', 'awesome-footnotes' ),
			);

			return $settings_tabs;
		}

		/**
		 * Creates an option and draws it
		 *
		 * @param array $value - The array with option data.
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function build_option( array $value ) {
			$data = false;

			if ( empty( $value['id'] ) ) {
				$value['id'] = ' ';
			}

			if ( isset( self::get_current_options()[ $value['id'] ] ) ) {
				$data = self::get_current_options()[ $value['id'] ];
			}

			Settings_Builder::create( $value, \AWEF_SETTINGS_NAME . '[' . $value['id'] . ']', $data );
		}

		/**
		 * Checks if current page is plugin settings page
		 *
		 * @return boolean
		 *
		 * @since 2.0.0
		 */
		public static function is_plugin_settings_page() {

			$current_page = ! empty( $_REQUEST['page'] ) ? \sanitize_text_field( \wp_unslash( $_REQUEST['page'] ) ) : '';

			return self::MENU_SLUG === $current_page || self::OPTIONS_PAGE_SLUG === $current_page;
		}

		/**
		 * Extracts the current version of the plugin
		 *
		 * @return string
		 *
		 * @since 2.0.0
		 */
		public static function get_version(): string {
			if ( empty( self::$current_version ) ) {
				self::$current_version = (string) \get_option( self::SETTINGS_VERSION, '' );
			}

			if ( empty( self::$current_version ) ) {
				self::$current_version = '0.0.0';
			}

			return self::$current_version;
		}

		/**
		 * Stores the current version of the plugin into the global options table
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function store_version(): void {
			\update_option( self::SETTINGS_VERSION, \AWEF_VERSION );
		}
	}
}

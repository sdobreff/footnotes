<?php
/**
 * Pointers class - showing the pointers where necessary.
 *
 * @package awesome-footnotes
 *
 * @since 2.4.3
 */

declare(strict_types=1);

namespace AWEF\Controllers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Controllers\Pointers' ) ) {
	/**
	 * Responsible for showing the pointers.
	 *
	 * @since 2.4.3
	 */
	class Pointers {

		public const POINTER_ADMIN_MENU_NAME = 'awef-admin-menu';

		/**
		 * Inits the class and sets the hooks
		 *
		 * @return void
		 *
		 * @since 2.4.3
		 */
		public static function init() {

			if ( ! self::is_dismissed( self::POINTER_ADMIN_MENU_NAME ) ) {
				\add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
			}
		}

		/**
		 * Adds the necessary scripts to the queue
		 *
		 * @return void
		 *
		 * @since 2.4.3
		 */
		public static function admin_enqueue_scripts() {
			// Using Pointers.
			\wp_enqueue_style( 'wp-pointer' );
			\wp_enqueue_script( 'wp-pointer' );

			// Register our action.
			\add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_footer_scripts' ) );
		}

		/**
		 * Prints out the JS needed to show the pointer.
		 *
		 * @return void
		 *
		 * @since 2.4.3
		 */
		public static function print_footer_scripts() {

			$element_id = 'toplevel_page_awef_settings';
			?>
			<script>
				jQuery(
					function() {
						var { __ } = wp.i18n;
						jQuery('#<?php echo \esc_attr( $element_id ); ?>').pointer( 
							{
								content:
									"<h3>Footnotes<\/h3>" +
									"<h4>" + __( 'Here is the settings menu', 'awesome-footnotes' ) + "<\/h4>" +
									"<p>" + __( 'Adjust the settings to your needs and start creating footnotes' ) + "</p>",


								position:
									{
										edge:  'left',
										align: 'left'
									},

								pointerClass:
									'wp-pointer arrow-top',

								pointerWidth: 420,
								
								close: function() {
									jQuery.post(
										ajaxurl,
										{
											pointer: '<?php echo \esc_attr( self::POINTER_ADMIN_MENU_NAME ); ?>',
											action: 'dismiss-wp-pointer',
										}
									);
								},

							}
						).pointer('open');
					}
				);
			</script>
			<?php
		}

		/**
		 * Checks if the user already dismissed the message
		 *
		 * @param string $pointer - Name of the pointer to check.
		 *
		 * @return boolean
		 *
		 * @since 2.0.0
		 */
		public static function is_dismissed( string $pointer ): bool {

			$dismissed = array_filter( explode( ',', (string) \get_user_meta( \get_current_user_id(), 'dismissed_wp_pointers', true ) ) );

			return \in_array( $pointer, (array) $dismissed, true );
		}
	}
}

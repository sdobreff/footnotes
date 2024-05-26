<?php
/**
 * Responsible for plugin updates.
 *
 * @package    awe
 * @subpackage migration
 * @copyright  %%YEAR%%
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 */

declare(strict_types=1);

namespace AWEF\Migration;

use AWEF\Migration\Abstract_Migration;


defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Migration class
 */
if ( ! class_exists( '\AWEF\Migration\Migration' ) ) {

	/**
	 * Put all you migration methods here
	 *
	 * @since 2.0.0
	 */
	class Migration extends Abstract_Migration {

		/**
		 * Migrates the plugin up-to version 2.0.0
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public static function migrate_up_to_200() {

			// Check if there is previous version installed.
			if ( \get_option( 'swas_footnote_options', false ) ) {
				\update_option( AWEF_SETTINGS_NAME, \get_option( 'swas_footnote_options', array() ), false );
				\delete_option( 'swas_footnote_options' );
			}
		}

		/**
		 * Migrates the plugin up-to version 2.0.0
		 *
		 * @return void
		 *
		 * @since 3.0.0
		 */
		public static function migrate_up_to_300() {

			// Check if there is previous version installed.
			if ( \get_option( 'fme_footnote_options', false ) ) {
				\update_option( AWEF_SETTINGS_NAME, \get_option( 'fme_footnote_options', array() ), false );
				\delete_option( 'fme_footnote_options' );
			}
		}
	}
}

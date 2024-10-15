<?php
/**
 * Class: System status info collector.
 *
 * Helper class to determine the proper status of the request.
 *
 * @package awesome-footnotes
 *
 * @since 3.2.0
 */

declare(strict_types=1);

namespace AWEF\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Helpers\System_Status' ) ) {
	/**
	 * Responsible for proper context determination.
	 *
	 * @since 3.2.0
	 */
	class System_Status {

		/**
		 * _curl_version
		 *
		 * Figure out cURL version, if installed
		 *
		 * @since 3.2.0
		 */
		private static function _curl_version() {

			$curl_version = '';
			if ( function_exists( 'curl_version' ) ) {
				$curl_version = curl_version();
				$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
			}

			return $curl_version;
		}

		/**
		 * _memory_limit
		 *
		 * Get the wp memory limit
		 *
		 * @since 3.2.0
		 */
		private static function _memory_limit() {

			$wp_memory_limit = self::_let_to_num( WP_MEMORY_LIMIT );
			if ( function_exists( 'memory_get_usage' ) ) {
				$wp_memory_limit = max( $wp_memory_limit, self::_let_to_num( @ini_get( 'memory_limit' ) ) );
			}

			return $wp_memory_limit;
		}

		/**
		 * post_request
		 *
		 * Test POST requests
		 *
		 * @since 3.2.0
		 *
		 * @return bool|\WP_Error
		 */
		private static function post_request() {

			$post_response = wp_safe_remote_post(
				'https://www.paypal.com/cgi-bin/webscr',
				array(
					'timeout'     => 60,
					'user-agent'  => 'woocommerce/',
					'httpversion' => '1.1',
					'body'        => array(
						'cmd' => '_notify-validate',
					),
				)
			);

			$post_response_successful = false;
			if ( ! is_wp_error( $post_response ) && $post_response['response']['code'] >= 200 && $post_response['response']['code'] < 300 ) {
				$post_response_successful = true;
			}

			return $post_response_successful;
		}

		/**
		 * get_request
		 *
		 * Test GET requests
		 *
		 * @return bool|\WP_Error
		 *
		 * @since 3.2.0
		 */
		private static function get_request() {

			$get_response = wp_safe_remote_get( 'https://woocommerce.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ) );

			$get_response_successful = false;
			if ( ! is_wp_error( $get_response ) && $get_response['response']['code'] >= 200 && $get_response['response']['code'] < 300 ) {
				$get_response_successful = true;
			}

			return $get_response_successful;
		}

		/**
		 * environment_info
		 *
		 * All environment info
		 *
		 * @since 3.2.0
		 */
		private static function environment_info() {
			global $wpdb;

			// $get_response_msg = '';

			// // Remote Post.
			// $post_response     = self::post_request();
			// $post_response_msg = '';

			// if ( is_wp_error( $post_response ) ) {
			// $post_response_msg = $post_response->get_error_message();
			// } elseif ( ! empty( $post_response['response']['code'] ) ) {
			// $post_response_msg = $post_response['response']['code'];
			// }

			// // Remote Get.
			// $get_response     = self::get_request();
			// $get_response_msg = '';

			// if ( is_wp_error( $get_response ) ) {
			// $get_response_msg = $get_response->get_error_message();
			// } elseif ( ! empty( $get_response['response']['code'] ) ) {
			// $get_response_msg = $get_response['response']['code'];
			// }

			return array(
				'home_url'                  => home_url( '/' ),
				'site_url'                  => site_url( '/' ),
				'wp_version'                => get_bloginfo( 'version' ),
				'wp_multisite'              => is_multisite(),
				'wp_memory_limit'           => self::_memory_limit(),
				'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
				'language'                  => get_locale(),
				'server_info'               => $_SERVER['SERVER_SOFTWARE'],
				'php_version'               => phpversion(),
				'php_post_max_size'         => self::_let_to_num( ini_get( 'post_max_size' ) ),
				'php_max_execution_time'    => ini_get( 'max_execution_time' ),
				'php_max_input_vars'        => ini_get( 'max_input_vars' ),
				'curl_version'              => self::_curl_version(),
				'suhosin_installed'         => extension_loaded( 'suhosin' ),
				'max_upload_size'           => wp_max_upload_size(),
				'mysql_version'             => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
				'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
				'mbstring_enabled'          => extension_loaded( 'mbstring' ),
				'xmlreader_enabled'         => extension_loaded( 'xmlreader' ),
				// 'remote_post_successful'    => $post_response,
				// 'remote_post_response'      => $post_response_msg,
				// 'remote_get_successful'     => $get_response,
				// 'remote_get_response'       => $get_response_msg,
				'secure_connection'         => 'https' === substr( get_home_url(), 0, 5 ),
				'hide_errors'               => ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
			);
		}

		/**
		 * _theme_info
		 *
		 * Get the theme info
		 *
		 * @since 3.2.0
		 */
		private static function _theme_info() {

			$active_theme = \wp_get_theme();

			if ( \is_child_theme() ) {
				$parent_theme      = \wp_get_theme( $active_theme->template );
				$parent_theme_info = array(
					'is_child_theme'    => true,
					'parent_name'       => $parent_theme->name,
					'parent_version'    => $parent_theme->version,
					'parent_author_url' => $parent_theme->{'Author URI'},
					'version_latest'    => $parent_theme->version,
				);

				// $api = themes_api(
				// 'theme_information',
				// array(
				// 'slug'   => $parent_theme->stylesheet,
				// 'fields' => array(
				// 'sections' => false,
				// 'tags'     => false,
				// ),
				// )
				// );

				// if ( is_object( $api ) && ! is_wp_error( $api ) && ! empty( $api->version ) ) {
				// $version_latest                      = $api->version;
				// $parent_theme_info['version_latest'] = $version_latest;
				// }
			} else {
				$parent_theme_info = array(
					'is_child_theme'    => false,
					'parent_name'       => $active_theme->name,
					'parent_version'    => $active_theme->version,
					'parent_author_url' => $active_theme->{'Author URI'},
					'version_latest'    => $active_theme->version,
				);

				// $api = themes_api(
				// 'theme_information',
				// array(
				// 'slug'   => $active_theme->stylesheet,
				// 'fields' => array(
				// 'sections' => false,
				// 'tags'     => false,
				// ),
				// )
				// );

				// if ( is_object( $api ) && ! is_wp_error( $api ) && ! empty( $api->version ) ) {
				// $version_latest                      = $api->version;
				// $parent_theme_info['version_latest'] = $version_latest;
				// }
			}

			return $parent_theme_info;
		}

		/**
		 * _get_active_plugins
		 *
		 * Get all active plugins info
		 *
		 * @since 3.2.0
		 */
		private static function _get_active_plugins() {

			// This Plugin causes an Fatal error.
			if ( class_exists( 'Envira_Gallery' ) ) {
				return;
			}

			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			// Get both site plugins and network plugins.
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
				$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
			}

			$active_plugins_data = array();

			foreach ( $active_plugins as $plugin ) {
				$data           = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
				$dirname        = dirname( $plugin );
				$version_latest = '';
				$slug           = explode( '/', $plugin );
				$slug           = explode( '.', end( $slug ) );
				$slug           = $slug[0];

				// $api = plugins_api(
				// 'plugin_information',
				// array(
				// 'slug'   => $slug,
				// 'fields' => array(
				// 'sections' => false,
				// 'tags'     => false,
				// ),
				// )
				// );

				// if ( is_object( $api ) && ! is_wp_error( $api ) && ! empty( $api->version ) ) {
				// $version_latest = $api->version;
				// }

				// convert plugin data to json response format.
				$active_plugins_data[] = array(
					'plugin'            => $plugin,
					'name'              => wp_strip_all_tags( $data['Name'] ),
					'version'           => wp_strip_all_tags( $data['Version'] ),
					'version_latest'    => $version_latest,
					'url'               => wp_strip_all_tags( $data['PluginURI'] ),
					'author_name'       => wp_strip_all_tags( str_replace( ',', ' | ', $data['AuthorName'] ), true ),
					'author_url'        => esc_url_raw( $data['AuthorURI'] ),
					'network_activated' => $data['Network'],
				);
			}

			return $active_plugins_data;
		}

		/**
		 * _let_to_num
		 *
		 * Transform the php.ini notation for numbers (like '2M') to an integer.
		 *
		 * @since 3.2.0
		 */
		public static function _let_to_num( $size ) {
			$l   = substr( $size, -1 );
			$ret = substr( $size, 0, -1 );
			switch ( strtoupper( $l ) ) {
				case 'P':
					$ret *= 1024;
					// no break.
				case 'T':
					$ret *= 1024;
					// no break.
				case 'G':
					$ret *= 1024;
					// no break.
				case 'M':
					$ret *= 1024;
					// no break.
				case 'K':
					$ret *= 1024;
			}

			return $ret;
		}

		/**
		 * _print_environment_info
		 *
		 * @since 3.2.0
		 */
		public static function print_environment_info() {
			global $wpdb;
			$environment = self::environment_info(); ?>

			<table class="awef-status-table status-report widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2" data-export-label="WordPress Environment"><?php esc_html_e( 'WordPress environment', 'awesome-footnotes' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td data-export-label="Home URL"><?php esc_html_e( 'Home URL', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $environment['home_url'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="Site URL"><?php esc_html_e( 'Site URL', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $environment['site_url'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="WP Version"><?php esc_html_e( 'WP version', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $environment['wp_version'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="WP Multisite"><?php esc_html_e( 'WP multisite', 'awesome-footnotes' ); ?>:</td>
						<td><?php echo ( $environment['wp_multisite'] ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
					</tr>
					<tr>
						<td data-export-label="WP Memory Limit"><?php esc_html_e( 'WP memory limit', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['wp_memory_limit'] < 134217728 ) {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend setting memory to at least %2$s. To import the demo data %3$s of memory limit is required. See: %4$s', 'awesome-footnotes' ), size_format( $environment['wp_memory_limit'] ), '128MB', '256MB', '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . esc_html__( 'Increasing memory allocated to PHP', 'awesome-footnotes' ) . '</a>' ) . '</mark>';
						} else {
							echo '<mark class="yes">' . size_format( $environment['wp_memory_limit'] ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="WP Debug Mode"><?php esc_html_e( 'WP debug mode', 'awesome-footnotes' ); ?>:</td>
						<td>
							<?php if ( $environment['wp_debug_mode'] ) : ?>
								<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
							<?php else : ?>
								<mark class="no">&ndash;</mark>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td data-export-label="Language"><?php esc_html_e( 'Language', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $environment['language'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="Hide errors from visitors"><?php esc_html_e( 'Hide errors from visitors', 'awesome-footnotes' ); ?></td>
						<td>
							<?php if ( $environment['hide_errors'] ) : ?>
								<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
							<?php else : ?>
								<mark class="error"><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'Error messages can contain sensitive information about your website environment. These should be hidden from untrusted visitors.', 'awesome-footnotes' ); ?></mark>
							<?php endif; ?>
						</td>
					</tr>

				</tbody>
			</table>

			<table class="awef-status-table status-report widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2" data-export-label="Server Environment"><?php esc_html_e( 'Server environment', 'awesome-footnotes' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td data-export-label="Server Info"><?php esc_html_e( 'Server info', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $environment['server_info'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="PHP Version"><?php esc_html_e( 'PHP version', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
							$php_version_requirements = '5.3';

						if ( version_compare( $environment['php_version'], $php_version_requirements, '<' ) ) {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend a minimum PHP version of %2$s.', 'awesome-footnotes' ), esc_html( $environment['php_version'] ), $php_version_requirements ) . '</mark>';
						} else {
							echo '<mark class="yes">' . esc_html( $environment['php_version'] ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<?php if ( function_exists( 'ini_get' ) ) : ?>
						<tr>
							<td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP post max size', 'awesome-footnotes' ); ?>:</td>
							<td><?php esc_html_e( size_format( $environment['php_post_max_size'] ) ); ?></td>
						</tr>
						<tr>
							<td data-export-label="PHP Execution Time Limit"><?php esc_html_e( 'PHP time limit', 'awesome-footnotes' ); ?>:</td>
							<td>
								<?php

								if ( 120 > $environment['php_max_execution_time'] && 0 != $environment['php_max_execution_time'] ) {
									echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - We recommend setting max execution time to at least %2$s.', 'awesome-footnotes' ), $environment['php_max_execution_time'], 120 ) . '</mark>';
								} else {
									esc_html_e( $environment['php_max_execution_time'] );
								}
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="PHP Max Input Vars"><?php esc_html_e( 'PHP max input vars', 'awesome-footnotes' ); ?>:</td>
							<td>
								<?php
								if ( $environment['php_max_input_vars'] < 3000 ) {
									echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' .
									sprintf(
										esc_html__( '%1$s - Recommended Value: %2$s. Max input vars limitation will truncate POST data such as menus.', 'awesome-footnotes' ),
										$environment['php_max_input_vars'],
										'3000'
									) . '</mark>';
								} else {
									echo '<mark class="yes">' . esc_html( $environment['php_max_input_vars'] ) . '</mark>';
								}
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="cURL Version"><?php esc_html_e( 'cURL version', 'awesome-footnotes' ); ?>:</td>
							<td><?php esc_html_e( $environment['curl_version'] ); ?></td>
						</tr>
						<tr>
							<td data-export-label="SUHOSIN Installed"><?php esc_html_e( 'SUHOSIN installed', 'awesome-footnotes' ); ?>:</td>
							<td><?php echo ( $environment['suhosin_installed'] ) ? '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'You have to increase the suhosin.post.max_vars and suhosin.request.max_vars parameters to 2000 or more.', 'awesome-footnotes' ) : '&ndash;'; ?></td>
						</tr>
						<?php
					endif;

					if ( $wpdb->use_mysqli && \function_exists( 'mysqli_get_server_info' ) ) {

						if ( empty( $wpdb->is_mysql ) || ! $wpdb->use_mysqli ) {
							$ver = array(
								'string' => '',
								'number' => '',
							);
						}

						$server_info = \mysqli_get_server_info( $wpdb->dbh ); // phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_get_server_info

						$ver = array(
							'string' => $server_info,
							'number' => preg_replace( '/([^\d.]+).*/', '', $server_info ),
						);

						$ver = $ver['number'];
					} else {
						$ver = 0;
					}
					if ( ! empty( $wpdb->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) :
						?>
						<tr>
							<td data-export-label="MySQL Version"><?php esc_html_e( 'MySQL version', 'awesome-footnotes' ); ?>:</td>
							<td>
								<?php
								$mysql_version_requirements = '5.0';

								if ( version_compare( $environment['mysql_version'], $mysql_version_requirements, '<' ) ) {
									echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( esc_html__( '%1$s - WordPress recommends a minimum MySQL version of %2$s. See: %3$sWordPress requirements%4$s', 'awesome-footnotes' ), esc_html( $environment['mysql_version'] ), $mysql_version_requirements, '<a href="https://wordpress.org/about/requirements/" target="_blank">', '</a>' ) . '</mark>';
								} else {
									echo '<mark class="yes">' . esc_html( $environment['mysql_version'] ) . '</mark>';
								}
								?>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td data-export-label="Max Upload Size"><?php esc_html_e( 'Max upload size', 'awesome-footnotes' ); ?>:</td>
						<td><?php echo size_format( $environment['max_upload_size'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="fsockopen/cURL"><?php esc_html_e( 'fsockopen/cURL', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['fsockopen_or_curl_enabled'] ) {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
						} else {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Your server does not have fsockopen or cURL enabled. Contact your hosting provider.', 'awesome-footnotes' ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="Multibyte String"><?php esc_html_e( 'Multibyte string', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['mbstring_enabled'] ) {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
						} else {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Your server does not support the mbstring functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'awesome-footnotes' ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="XMLReader"><?php esc_html_e( 'XMLReader', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['xmlreader_enabled'] ) {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
						} else {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'The XMLReader PHP module/extension is missing. Please contact your hosting company and ask them to install that for you.', 'awesome-footnotes' ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<?php
					/*
					<tr>
						<td data-export-label="Remote Post"><?php esc_html_e( 'Remote post', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['remote_post_successful'] ) {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
						} else {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'wp_remote_post() failed. Contact your hosting provider.', 'awesome-footnotes' ) . ' ' . esc_html( $environment['remote_post_response'] ) . '</mark>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="Remote Get"><?php esc_html_e( 'Remote get', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						if ( $environment['remote_get_successful'] ) {
							echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
						} else {
							echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'wp_remote_get() failed. Contact your hosting provider.', 'awesome-footnotes' ) . ' ' . esc_html( $environment['remote_get_response'] ) . '</mark>';
						}
						?>
						</td>
					</tr>
					*/
					?>
				</tbody>
			</table>
			<?php
		}

		/**
		 * _print_theme_info
		 *
		 * @since 3.2.0
		 */
		public static function print_theme_info() {
			$theme = self::_theme_info();
			?>

			<table class="awef-status-table status-report widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2" data-export-label="Theme"><?php esc_html_e( 'Theme', 'awesome-footnotes' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td data-export-label="Name"><?php esc_html_e( 'Name', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $theme['parent_name'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="Version"><?php esc_html_e( 'Version', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
						esc_html_e( $theme['parent_version'] );

						if ( ! $theme['is_child_theme'] && version_compare( (string) $theme['parent_version'], (string) $theme['version_latest'], '<' ) ) {
							echo ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'awesome-footnotes' ), esc_html( $theme['version_latest'] ) ) . '</strong>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="Author URL"><?php esc_html_e( 'Author URL', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $theme['parent_author_url'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="Child Theme"><?php esc_html_e( 'Child theme', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
							echo ( $theme['is_child_theme'] ) ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '&ndash;';
						?>
						</td>
					</tr>
					<?php
					if ( $theme['is_child_theme'] ) :
						?>
					<tr>
						<td data-export-label="Parent Theme Name"><?php esc_html_e( 'Parent theme name', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $theme['parent_name'] ); ?></td>
					</tr>
					<tr>
						<td data-export-label="Parent Theme Version"><?php esc_html_e( 'Parent theme version', 'awesome-footnotes' ); ?>:</td>
						<td>
						<?php
							esc_html_e( $theme['parent_version'] );

						if ( version_compare( $theme['parent_version'], $theme['version_latest'], '<' ) ) {
							echo ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'awesome-footnotes' ), esc_html( $theme['version_latest'] ) ) . '</strong>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td data-export-label="Parent Theme Author URL"><?php esc_html_e( 'Parent theme author URL', 'awesome-footnotes' ); ?>:</td>
						<td><?php esc_html_e( $theme['parent_author_url'] ); ?></td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>

			<?php
		}

		/**
		 * _custom_post_types_info
		 *
		 * @since 3.2.0
		 */
		private static function _custom_post_types_info() {

			$post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				),
				'objects'
			);

			if ( empty( $post_types ) ) {
				return;
			}

			?>

			<table class="awef-status-table status-report widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2" data-export-label="Custom Post Types"><?php esc_html_e( 'Custom Post Types', 'awesome-footnotes' ); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php	foreach ( $post_types as $data ) { ?>
					<tr id="<?php echo $data->name; ?>">
						<td><?php echo $data->label; ?></td>
						<td><?php echo $data->exclude_from_search ? '<span style="padding: 3px 8px; background: red; color: #fff;">' . esc_html__( 'Private', 'awesome-footnotes' ) . '</span>' : esc_html__( 'Public', 'awesome-footnotes' ); ?></td>
					</tr>
				<?php } ?>

				</tbody>
			</table>

			<?php
		}

		/**
		 * _print_plugins_info
		 *
		 * @since 3.2.0
		 */
		public static function print_plugins_info() {

			$active_plugins = self::_get_active_plugins();

			if ( empty( $active_plugins ) ) {
				return;
			}

			?>

			<table class="awef-status-table status-report widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2" data-export-label="Active Plugins (<?php echo count( $active_plugins ); ?>)"><?php esc_html_e( 'Active plugins', 'awesome-footnotes' ); ?> (<?php echo count( $active_plugins ); ?>)</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $active_plugins as $plugin ) {
						if ( ! empty( $plugin['name'] ) ) {

							$plugin_name = esc_html( $plugin['name'] );

							// Link the plugin name to the plugin url if available.
							if ( ! empty( $plugin['url'] ) ) {
								$plugin_name = '<a href="' . esc_url( $plugin['url'] ) . '" target="_blank">' . $plugin_name . '</a>';
							}

							$version_string = '';
							$network_string = '';

							if ( ! empty( $plugin['version_latest'] ) && version_compare( $plugin['version_latest'], $plugin['version'], '>' ) ) {
								$version_string = ' &ndash; <strong style="color:red;">' . sprintf( esc_html__( '%s is available', 'awesome-footnotes' ), $plugin['version_latest'] ) . '</strong>';
							}

							if ( false != $plugin['network_activated'] ) {
								$network_string = ' &ndash; <strong>' . esc_html__( 'Network enabled', 'awesome-footnotes' ) . '</strong>';
							}

							?>
							<tr>
								<td><?php echo ( $plugin_name ); ?></td>
								<td>
								<?php
									printf( esc_html__( 'by %s', 'awesome-footnotes' ), $plugin['author_name'] );
									echo ' &ndash; ' . esc_html( $plugin['version'] ) . $version_string . $network_string;
								?>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>

			<?php
		}

		/**
		 * _print_report
		 *
		 * @since 3.2.0
		 */
		public static function print_report() {

			?>

			<table class="awef-status-table widefat" cellspacing="0">
				<tbody>
				<tr>
					<td>
						<p><?php esc_html_e( 'Please copy and paste this information in your ticket when contacting support:', 'awesome-footnotes' ); ?> </p>
						<a id="get-debug-report" href="#" class="button-primary"><?php esc_html_e( 'Get system report', 'awesome-footnotes' ); ?></a>
						<div id="awef-debug-report">
							<textarea readonly="readonly"></textarea>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<script type="text/javascript">
				jQuery( '#get-debug-report' ).click(
					function() {
						var report = '';

						jQuery( '.status-report thead, .status-report tbody' ).each(
							function() {
								if ( jQuery( this ).is( 'thead' ) ) {
									var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
									report = report + '\n### ' + jQuery.trim( label ) + ' ###\n\n';
								} else {
									jQuery( 'tr', jQuery( this ) ).each( function() {
										var label       = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
										var the_name    = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML.

										// Find value
										var $value_html = jQuery( this ).find( 'td:eq(1)' ).clone();
										$value_html.find( '.private' ).remove();
										$value_html.find( '.dashicons-yes' ).replaceWith( '&#10004;' );
										$value_html.find( '.dashicons-no-alt, .dashicons-warning' ).replaceWith( '&#10060;' );

										// Format value
										var the_value   = jQuery.trim( $value_html.text() );
										var value_array = the_value.split( ', ' );

										if ( value_array.length > 1 ) {
											// If value have a list of plugins ','.
											// Split to add new line.
											var temp_line ='';
											jQuery.each( value_array, function( key, line ) {
												temp_line = temp_line + line + '\n';
											});

											the_value = temp_line;
										}

										report = report + '' + the_name + ': ' + the_value + '\n';
									});
								}
							}
						);

						try {
							jQuery( this ).hide();
							jQuery( "#awef-debug-report" ).slideDown();
							jQuery( "#awef-debug-report textarea" ).val( report ).focus().select();

							return false;
						} catch ( e ) {
							console.log( e );
						}

						return false;
					}
				);
			</script>
			<?php
		}

		/**
		 * Scan the template files
		 *
		 * @since 3.2.0
		 */
		public static function scan_template_files( $template_path ) {
			$files  = scandir( $template_path );
			$result = array();
			if ( $files ) {
				foreach ( $files as $key => $value ) {
					if ( ! in_array( $value, array( '.', '..' ) ) ) {
						if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
							$sub_files = self::scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
							foreach ( $sub_files as $sub_file ) {
								$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
							}
						} else {
							$result[] = $value;
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Retrieve metadata from a file. Based on WP Core's get_file_data function
		 *
		 * @since 3.2.0
		 */
		public static function get_template_version( $file ) {
			// We don't need to write to the file, so just open for reading.
			$fp = fopen( $file, 'r' );

			// Pull only the first 8kiB of the file in.
			$file_data = fread( $fp, 8192 );

			// PHP will close file handle, but we are good citizens.
			fclose( $fp );

			// Make sure we catch CR-only line endings.
			$file_data = str_replace( "\r", "\n", $file_data );
			$version   = '';

			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
				$version = _cleanup_header_comment( $match[1] );
			}

			return $version;
		}
	}
}

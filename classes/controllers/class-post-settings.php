<?php
/**
 * Post settings class - showing the plugin settings where necessary.
 *
 * @package awesome-footnotes
 *
 * @since 3.8.0
 */

declare(strict_types=1);

namespace AWEF\Controllers;

use AWEF\Helpers\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\AWEF\Controllers\Post_Settings' ) ) {
	/**
	 * Responsible for showing the settings in the posts.
	 *
	 * @since 3.8.0
	 */
	class Post_Settings {

		public const HIDDEN_FORM_ELEMENT = 'awef_hidden_flag';

		public const POST_SETTINGS_NAME = '_awef_post_settings';
		public const POST_SEO_TITLE     = '_awef_post_seo_title';

		public const POST_OPTIONS = array(
			'footnotes_open',
			'footnotes_close',
			'list_style_symbol',
			'list_style_type',
			'position_before_footnote',
			'back_link_title',
			'pre_backlink',
			'backlink',
			'post_backlink',
			'pre_footnotes',
			'post_footnotes',
			'superscript',
			'pre_identifier',
			'inner_pre_identifier',
			'inner_post_identifier',
			'post_identifier',
		);

		/**
		 * Initialize the class
		 *
		 * @since 3.8.0
		 */
		public static function init() {
			if ( ! Settings::get_current_options()['no_posts_footnotes'] ) {
				\add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
				\add_action( 'save_post', array( __CLASS__, 'save' ) );
			}

			\add_filter( 'TieLabs/meta_title', array( __CLASS__, 'get_meta_title' ) );
			\add_filter( 'TieLabs/meta_description', array( __CLASS__, 'get_meta_description' ) );
		}

		/**
		 * Saves the custom post settings
		 *
		 * @param int $post_id - The ID of the post which custom settings need to be saved.
		 *
		 * @return void|int
		 *
		 * @since 3.8.0
		 */
		public static function save( $post_id ) {
			// Check if this is an auto save.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Begin to save.
			if ( ! isset( $_POST[ self::HIDDEN_FORM_ELEMENT ] ) && ! isset( $_POST[ \AWEF_SETTINGS_NAME ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				return;
			}

			$data = \get_the_content( null, false, $post_id );

			$settings_collected = Settings::collect_and_sanitize_options( $_POST[ \AWEF_SETTINGS_NAME ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$only_values = self::POST_OPTIONS;

			$settings_collected = array_filter(
				$settings_collected,
				function( $v ) use ( $only_values ) {
					return in_array( $v, $only_values );
				},
				ARRAY_FILTER_USE_KEY
			);

			if ( isset( $_POST[ \AWEF_SETTINGS_NAME ]['seo_description'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

				$post               = \get_post( $post_id );
				$post->post_excerpt = self::the_short_content( 160, $_POST[ \AWEF_SETTINGS_NAME ]['seo_description'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				\remove_all_actions( 'save_post' );

				\wp_update_post( $post );
			}

			if ( isset( $_POST[ \AWEF_SETTINGS_NAME ]['seo_title'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

				$post            = \get_post( $post_id );
				$post_meta_title = \get_post_meta( $post->ID, self::POST_SEO_TITLE, true );
				if ( $post_meta_title !== $_POST[ \AWEF_SETTINGS_NAME ]['seo_title'] && \get_the_title( $post->ID ) !== $_POST[ \AWEF_SETTINGS_NAME ]['seo_title'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					\update_post_meta( $post_id, self::POST_SEO_TITLE, \sanitize_text_field( \wp_unslash( $_POST[ \AWEF_SETTINGS_NAME ]['seo_title'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				}

				if ( \get_the_title( $post->ID ) === $_POST[ \AWEF_SETTINGS_NAME ]['seo_title'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					\delete_post_meta( $post_id, self::POST_SEO_TITLE );
				}
			}

			if ( false === \mb_strpos( $data, Settings::get_current_options()['footnotes_open'] ) || false === \mb_strpos( $data, $settings_collected['footnotes_open'] ) ) {

				// It looks like that post does not contain footnotes formatting - there is no need to store anything remove if there is something stored and bounce.

				$global_options = Settings::get_global_options();

				$global_options = array_filter(
					$global_options,
					function( $v ) use ( $only_values ) {
						return in_array( $v, $only_values );
					},
					ARRAY_FILTER_USE_KEY
				);

				if ( $settings_collected != $global_options ) {
					\update_post_meta( $post_id, self::POST_SETTINGS_NAME, $settings_collected );
				} else {
					\delete_post_meta( $post_id, self::POST_SETTINGS_NAME );
				}

				return;
			}

			\update_post_meta( $post_id, self::POST_SETTINGS_NAME, $settings_collected );
		}

		/**
		 * Register The Meta Boxes
		 *
		 * @since 3.8.0
		 */
		public static function meta_boxes() {

			\add_meta_box(
				'awef_post_options',
				AWEF_NAME . ' - ' . esc_html__( 'Settings', 'awesome_footnotes' ),
				array( __CLASS__, 'custom_options' ),
				array( 'post', 'page' ),
				'normal',
				'high'
			);
		}

		/**
		 * Adds custom options to the post types
		 *
		 * @return void
		 *
		 * @since 3.8.0
		 */
		public static function custom_options() {
			\wp_enqueue_style( 'awef-admin-style', \AWEF_PLUGIN_ROOT_URL . 'css/admin/style.css', array(), \AWEF_VERSION, 'all' );
			\wp_enqueue_script( 'awef-admin-scripts', \AWEF_PLUGIN_ROOT_URL . 'js/admin/awef-settings.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'wp-color-picker', 'jquery-ui-autocomplete' ), \AWEF_VERSION, false );

			$settings_tabs = array(

				'head-post' => esc_html__( 'Footnotes', 'awesome-footnotes' ),

				'general'   => array(
					'icon'  => 'admin-generic',
					'title' => esc_html__( 'General', 'awesome-footnotes' ),
				),
			);

			if ( Settings::get_current_options()['seo_post_options'] ) {
				$settings_tabs['seo-options'] = array(
					'icon'  => 'admin-site',
					'title' => esc_html__( 'SEO', 'awesome-footnotes' ),
				);
			}

			?>

			<input type="hidden" name="<?php echo \esc_attr( self::HIDDEN_FORM_ELEMENT ); ?>" value="true" />

			<div class="awef-panel">
				<div class="awef-panel-tabs">
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

					<?php
					foreach ( $settings_tabs as $tab => $settings ) {
						if ( ! empty( $settings['title'] ) ) {
							?>

							<div id="awef-options-tab-<?php echo esc_attr( $tab ); ?>" class="tabs-wrap">
							<?php
							include_once \AWEF_PLUGIN_ROOT . 'classes/settings/settings-options/' . $tab . '.php';

							\do_action( 'awef_plugin_options_tab_' . $tab );
							?>

							</div>

							<?php
						}
					}

					?>

				</div><!-- .awef-panel-content -->

				<div class="clear"></div>
			</div><!-- .awef-panel -->

			<div class="clear"></div>

			<?php
		}

		/**
		 * Get a limited part of the content - sans html tags and shortcodes -
		 * according to the amount written in $limit. Make sure words aren't cut in the middle
		 *
		 * @param int    $limit - number of characters.
		 * @param string $content - The content to be shortened, if null, the current post content will be used.
		 *
		 * @return string - the shortened content
		 *
		 * @since latest
		 */
		public static function the_short_content( $limit, ?string $content = null ) {

			if ( null === $content ) {
				$content = \get_the_content();
			}
			/**
			 * Sometimes there are <p> tags that separate the words, and when the tags are removed
			 * words from adjoining paragraphs stick together.
			 * so replace the end <p> tags with space, to ensure unstickinees of words */
			$content = strip_tags( $content );
			$content = \strip_shortcodes( $content );
			$content = trim( preg_replace( '/\s+/', ' ', $content ) );
			$ret     = $content; /* if the limit is more than the length, this will be returned */
			if ( mb_strlen( $content ) >= $limit ) {
				$ret = mb_substr( $content, 0, $limit );
				// make sure not to cut the words in the middle:
				// 1. first check if the substring already ends with a space.
				if ( mb_substr( $ret, -1 ) !== ' ' ) {
					// 2. If it doesn't, find the last space before the end of the string.
					$space_pos_in_substr = mb_strrpos( $ret, ' ' );
					// 3. then find the next space after the end of the string(using the original string).
					$space_pos_in_content = mb_strpos( $content, ' ', $limit );
					// 4. now compare the distance of each space position from the limit.
					if ( false !== $space_pos_in_content && $space_pos_in_content - $limit <= $limit - $space_pos_in_substr ) {
						/* if the closest space is in the original string, take the substring from there*/
						$ret = mb_substr( $content, 0, $space_pos_in_content );
					} else {
						// else take the substring from the original string, but with the earlier (space) position.
						$ret = mb_substr( $content, 0, $space_pos_in_substr );
					}
				}
			}

			return $ret;
		}

		/**
		 * Returns the post SEO title from the stored in meta or the post title if empty.
		 *
		 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
		 *
		 * @return string
		 *
		 * @since latest
		 */
		public static function get_post_seo_title( $post ) {
			$post = get_post( $post );
			if ( ! $post ) {
				return '';
			}
			$post_meta_title = \get_post_meta( $post->ID, self::POST_SEO_TITLE, true );
			if ( ! empty( $post_meta_title ) ) {
				return $post_meta_title;
			}

			return \get_the_title( $post->ID );
		}

		/**
		 * Returns meta description for the global post.
		 *
		 * @param string $description - The current meta description.
		 *
		 * @return string
		 *
		 * @since latest
		 */
		public static function get_meta_description( $description ) {
			global $post;
			if ( $post instanceof \WP_Post ) {
				$excerpt = \get_the_excerpt( $post );
				if ( ! empty( $excerpt ) ) {
					return $excerpt;
				} else {
					return self::the_short_content( 160 );
				}
			}

			return $description;
		}

		/**
		 * Returns the seo meta title for the global post.
		 *
		 * @param string $title - The current meta title of the post.
		 *
		 * @return string
		 *
		 * @since latest
		 */
		public static function get_meta_title( $title ) {
			global $post;
			if ( $post instanceof \WP_Post ) {
				$post_meta_title = \get_post_meta( $post->ID, self::POST_SEO_TITLE, true );
				if ( isset( $post_meta_title ) && \get_the_title( $post->ID ) !== $post_meta_title ) {
					return \esc_attr( $post_meta_title );
				}

				return \esc_attr( \get_the_title( $post->ID ) );
			}

			return $title;
		}
	}
}

<?php
/**
 * Advanced settings of the plugin
 *
 * @package awe
 *
 * @since 2.0.0
 */

use AWEF\Helpers\Settings;

Settings::build_option(
	array(
		'title' => esc_html__( 'Advanced Settings', 'awesome-footnotes' ),
		'id'    => 'advanced-settings-tab',
		'type'  => 'tab-title',
	)
);

Settings::build_option(
	array(
		'type'  => 'header',
		'id'    => 'advanced-settings',
		'title' => esc_html__( 'Advanced Settings', 'awesome-footnotes' ),
	)
);

	Settings::build_option(
		array(
			'name'    => \esc_html__( 'Do not use separate footnotes settings in posts', 'awesome-footnotes' ),
			'id'      => 'no_posts_footnotes',
			'type'    => 'checkbox',
			'default' => Settings::get_current_options()['no_posts_footnotes'],
		)
	);

	// Reset the settings options.
	Settings::build_option(
		array(
			'type'  => 'header',
			'id'    => 'reset-all-settings',
			'title' => esc_html__( 'Reset All Settings', 'awesome-footnotes' ),
		)
	);

	Settings::build_option(
		array(
			'title' => esc_html__( 'Markup', 'awesome-footnotes' ),
			'id'    => 'reset-settings-hint',
			'type'  => 'hint',
			'hint'  => esc_html__( 'This is destructive operation, which can not be undone! You may want to export your current settings first.', 'awesome-footnotes' ),
		)
	);

	?>

	<div class="option-item">
		<a id="awef-reset-settings" class="awef-primary-button button button-primary button-hero awef-button-red" href="<?php print \esc_url( \wp_nonce_url( \admin_url( 'admin.php?page=' . self::MENU_SLUG . '&reset-settings' ), 'reset-plugin-settings', 'reset_nonce' ) ); ?>" data-message="<?php esc_html_e( 'This action can not be undone. Clicking "OK" will reset your plugin options to the default installation. Click "Cancel" to stop this operation.', 'awesome-footnotes' ); ?>"><?php esc_html_e( 'Reset All Settings', 'awesome-footnotes' ); ?></a>
	</div>


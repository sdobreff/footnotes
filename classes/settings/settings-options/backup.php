<?php
/**
 * Import/Export settings of the plugin
 *
 * @package awe
 *
 * @since 2.0.0
 */

use AWEF\Helpers\Settings;

	Settings::build_option(
		array(
			'title' => esc_html__( 'Export/Import Plugin Options', 'awesome-footnotes' ),
			'id'    => 'export-settings-tab',
			'type'  => 'tab-title',
		)
	);

	if ( isset( $_REQUEST['import'] ) ) {

		Settings::build_option(
			array(
				'text' => esc_html__( 'The plugin options have been imported successfully.', 'awesome-footnotes' ),
				'type' => 'message',
			)
		);
	}

	Settings::build_option(
		array(
			'title' => esc_html__( 'Export', 'awesome-footnotes' ),
			'id'    => 'export-settings',
			'type'  => 'header',
		)
	);

	?>

<div class="option-item">

	<p><?php esc_html_e( 'When you click the button below the plugin will create a .dat file for you to save to your computer.', 'awesome-footnotes' ); ?>
	</p>
	<p><?php esc_html_e( 'Once you’ve saved the download file, you can use the Import function in another WordPress installation to import the plugin options from this site.', 'awesome-footnotes' ); ?>
	</p>

	<p><a class="awef-primary-button button button-primary button-hero"
			href="
			<?php
			print \esc_url(
				\wp_nonce_url(
					\admin_url( 'admin.php?page=' . Settings::MENU_SLUG . '&export-settings' ),
					'export-plugin-settings',
					'export_nonce'
				)
			);
			?>
				"><?php esc_html_e( 'Download Export File', 'awesome-footnotes' ); ?></a>
	</p>
</div>

<?php

	Settings::build_option(
		array(
			'title' => \esc_html__( 'Import', 'awesome-footnotes' ),
			'id'    => 'import-settings',
			'type'  => 'header',
		)
	);

	?>

<div class="option-item">

	<p><?php \esc_html_e( 'Upload your .dat plugin options file and we will import the options into this site.', 'awesome-footnotes' ); ?>
	</p>
	<p><?php \esc_html_e( 'Choose a (.dat) file to upload, then click Upload file and import.', 'awesome-footnotes' ); ?></p>

	<p>
		<label for="upload"><?php \esc_html_e( 'Choose a file from your computer:', 'awesome-footnotes' ); ?></label>
		<input type="file" name="<?php echo \esc_attr( Settings::SETTINGS_FILE_FIELD ); ?>" id="awef-import-file" />
	</p>

	<p>
		<input type="submit" name="<?php echo \esc_attr( Settings::SETTINGS_FILE_UPLOAD_FIELD ); ?>" id="awef-import-upload" class="button-primary"
			value="<?php \esc_html_e( 'Upload file and import', 'awesome-footnotes' ); ?>" />
	</p>
</div>

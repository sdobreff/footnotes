<?php
/**
 * Footnotes template file
 *
 * @package awesome-footnotes
 */

use AWEF\Controllers\Footnotes_Formatter;

global $footnotes_block, $footnotes_header, $footnotes_footer, $start, $awe_post_id;

Footnotes_Formatter::insert_styles($awe_post_id);

if ( ! empty( $footnotes_header ) ) {
	?>
<div class="awesome-footnotes-header">
	<?php echo $footnotes_header; ?>
</div>
	<?php
}
?>
<ol <?php echo $start; ?> class="footnotes awepost_<?php echo \esc_attr( $awe_post_id ); ?>">
	<?php
	if ( ! empty( $footnotes_block ) ) {
		echo $footnotes_block;
	}
	?>
</ol>
<?php
if ( ! empty( $footnotes_footer ) ) {
	?>
<div class="awesome-footnotes-footer">
	<?php echo $footnotes_footer; ?>
</div>
	<?php
}

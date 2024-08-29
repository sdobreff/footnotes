<?php
/**
 * Footnotes template file
 *
 * @package awesome-footnotes
 */

?>

<?php

global $footnotes_block, $footnotes_header, $footnotes_footer, $start;

if ( ! empty( $footnotes_header ) ) {
	?>
<div class="awesome-footnotes-header">
	<?php echo $footnotes_header; ?>
</div>
	<?php
}
?>
<ol <?php echo $start; ?> class="footnotes">
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

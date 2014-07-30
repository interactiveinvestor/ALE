<?php $note=current(taxonomy_get_term_by_name('Highlights notes'))->description; ?>

<?php print $rows; ?>
<?php if (!isset($_GET['pdf-view'])): ?>
	<div class='highlights-note note'><?php echo $note ?></div>
<?php endif ?>
<?php if (isset($_GET['pdf-view'])): ?>
	<div class='highlights-note note'><?php echo $note ?></div>
<?php endif ?>


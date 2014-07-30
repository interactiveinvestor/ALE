<?php if ($block_html_id=='block-system-main'): ?>
	<div class='main-content'>
		<?php print $content ?>
	</div>

<?php elseif($block_html_id=='block-views-nodequeue-1-block'): ?>
<?php print $content ?>
	
<?php elseif($block_html_id=='block-views-nodequeue-2-block'): ?>	
<?php print $content ?>
<?php else: ?>
	<div id="<?php print $block_html_id; ?>" class="<?php print $classes; ?>"<?php print $attributes; ?>>

	  <?php print render($title_prefix); ?>
	<?php if ($block->subject): ?>
	  <h2<?php print $title_attributes; ?>><?php print $block->subject ?></h2>
	<?php endif;?>
	  <?php print render($title_suffix); ?>

	  <div class="content"<?php print $content_attributes; ?>>
	    <?php print $content ?>
	  </div>
	</div>	
<?php endif ?>


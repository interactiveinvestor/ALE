<?php if ($element['#field_name']=='field_slide_image'): ?>
	<?php foreach ($items as $delta => $item) print render($item); ?>
<?php elseif ($element['#field_type']=='image'): ?>
	
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
	<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?></div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
	<?php foreach ($items as $delta => $item): ?>
		<div class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>
			<?php print render($item); ?>
		</div>	
	<?php endforeach; ?>
	</div>
</div>
	
<?php else: ?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
	<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?></div>
	<?php endif; ?>
	<?php if (count($items)>1): ?><div class="field-items"<?php print $content_attributes; ?>><?php endif ?>
	<?php foreach ($items as $delta => $item): ?>
		<?php if (count($items)>1): ?><div class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>	<?php endif ?>		
			<?php print render($item); ?>
		<?php if (count($items)>1): ?></div><?php endif ?>		
	<?php endforeach; ?>
	<?php if (count($items)>1): ?></div><?php endif ?>
</div>
	
<?php endif ?>




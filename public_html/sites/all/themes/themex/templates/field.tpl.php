
<?php
if($element['#bundle']=='property'){
	$classes=str_replace(array('field-name-','clearfix'),'',$classes);
	$classes=preg_replace('/field-label-([a-zA-z0-9\-]+)/','',$classes);
	$classes=preg_replace('/field-type-([a-zA-z0-9\-]+)/','',$classes);
}


?>
<?php if ($field_wrapper): ?>
<<?php print $field_wrapper; ?> class="<?php print $classes; ?>"<?php print $attributes; ?>>
<?php endif; ?>
  <?php if (!$label_hidden) : ?>
    <?php if ($label_wrapper): ?>
    <<?php print $label_wrapper; ?> class="field-label"<?php print $title_attributes; ?>>
    <?php endif; ?>
      <?php print $label ?>
    <?php if ($label_wrapper): ?>
    </<?php print $label_wrapper; ?>>
    <?php endif; ?>
  <?php endif; ?>
  <?php foreach ($items as $delta => $item) : ?>
    <?php if ($item_wrapper): ?>
    <<?php print $item_wrapper; ?> class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>
    <?php endif; ?>
    <?php 
			$_item=render($item);		
			if($element['#field_name']=='field_images' && $element['#bundle']=='property'){				
				$disclaimer=current(taxonomy_get_term_by_name('Property Image Disclaimer'))->description;
				$_item=$_item.'<div class="disclaimer">'.str_replace('[year]',date('Y'),$disclaimer).'</div>';		
			}			
			print $_item;					
			?>
    <?php if ($item_wrapper): ?>
    </<?php print $item_wrapper; ?>>
    <?php endif; ?>
  <?php endforeach; ?>
<?php if ($field_wrapper): ?>
</<?php print $field_wrapper; ?>>
<?php endif; ?>

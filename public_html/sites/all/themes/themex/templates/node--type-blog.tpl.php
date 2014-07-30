<?php dpm($node); die(); ?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <div class="content" <?php print $content_attributes; ?> >	
		<?php 
			if(isset($content['title_field'])){
				print render($content['title_field']); 
			}		
		?>
		<?php if ($display_submitted): ?>
	    <div class="submitted">
	      <?php print $submitted; ?>
	    </div>
	  <?php endif; ?>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
			
    ?>
		
  </div>
 
  <?php print render($content['links']); ?>
  <?php print render($content['comments']); ?>
</div>

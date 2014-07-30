<?php //dpm($content); ?>
<div id="node-<?php print $node->nid; ?>" class="n-<?php echo _DM::machine_name($node->title) ?> <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
		<?php if ($display_submitted): ?>
	    <div class="submitted">
	      <?php print $submitted; ?>
	    </div>
	  <?php endif; ?>
    <?php			
      hide($content['comments']);
      hide($content['links']);			
      print render($content);		
    ?> 
</div>
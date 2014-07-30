<?php 
$title_duplicate=$content['group_holder']['title_field'];
$content['group_body_holder']['title_dup']=$title_duplicate;
?>

<div id="node-<?php print $node->nid; ?>" class="n-<?php echo _DM::machine_name($node->title) ?> <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
		<?php if ($display_submitted): ?>
	    <div class="submitted">
	      <?php print $submitted; ?>
	    </div>
	  <?php endif; ?>
    <?php			
      hide($content['comments']);
      hide($content['links']);
			$_content=render($content);
      print str_replace(array('km2','m2'),array('km<sup>2</sup>','m<sup>2</sup>'),$_content);	
    ?> 
  <?php print render($content['links']); ?>
  <?php print render($content['comments']); ?>
</div>

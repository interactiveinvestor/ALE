<?php
$node_ids=array();
foreach ($node->field_pages_reference['und'] as $key => $n) {	
	$node_ids[]=$n['nid'];
}

?>
<div class='accordion'>
<?php foreach ($node_ids as $key => $id): 
$nodex= node_load($id);
?>
	<div class='accordion-<?php echo $nodex->type ?>'>
		<h3 class='title'><?php echo $nodex->title ?></h3>
		<div class="accordion-expandable">
			<?php print drupal_render(node_view($nodex) ) ?>
		</div>
	</div>	
<?php endforeach ?>

</div>

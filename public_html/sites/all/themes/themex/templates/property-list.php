<?php

	$_properties=array();
	foreach ($properties as $key => $property){		
		$_property=array(
			'title'=>$property->title,
			'location'=>'',
			'nid'=>$property->nid
		);
		if (isset($property->field_suburb) && isset($property->field_suburb['und'][0])){
			$_property['location'].=$property->field_suburb['und'][0]['value'].', ';
		}		
		if(isset($property->field_city[$property->language][0])){
			$_property['location'].=taxonomy_term_load($property->field_city[$property->language][0]['tid'])->name;
		}		
		
		$_properties[]=$_property;
	}
	
	array_sort_by_column($_properties, 'title', $dir = SORT_ASC)

	
?>
<ul class='property-list'>
	<li class='labels'>
		<div class='label1'>Property</div>
		<div>Location</div>
	</li>

	<?php foreach ($_properties as $key => $property): ?>
		
	<li <?php if(count($properties)==$key+1) echo "class='last'" ?>>
		<h4>
			<a href='/<?php echo drupal_get_path_alias('node/'.$property['nid']); ?>'>
				<div class='title'><?php echo $property['title']; ?></div>
				<div class='location'>
					<?php echo $property['location'] ?>
				</div>
			</a>
		</h4>
	</li>
	<?php endforeach ?>
</ul>

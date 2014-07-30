<?php
	
	$links=render($content['links']);
	$url=trim(render($content['field_file']));
	$date=date('d / m / y', $node->created);	
	isset($node->field_file['und']) ? $uri=$node->field_file['und'][0]['uri']:$uri=$node->field_file[0]['uri'];
	$filesize=__File::formatSizeUnits(filesize(drupal_realpath($uri)));
	$info=pathinfo($uri);
	$extension=strtoupper($info['extension']);
?>
<?php echo $links ?>
<a href="/<?php echo drupal_get_path_alias('node/'.$node->nid) ?>">
	<div class='date'><?php echo $date ?></div>
	<div class='title'><?php echo $node->title ?></div>
	<div class='file-details'>
		( <?php echo $extension ?> | <?php echo $filesize ?> )
	</div>
</a>
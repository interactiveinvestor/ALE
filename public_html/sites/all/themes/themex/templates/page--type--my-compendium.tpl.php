<?php
if(strstr($_SERVER['HTTP_REFERER'],'forward?path=my-property-compendium')){
	drupal_set_message(getTermDescription('Compendium Email Thankyou'),'status');
	header('Location: /my-compendium');
}
$node_path = drupal_get_path_alias($_GET['q']);
$id=array_pop(explode('-',$node_path));
$filename=getcwd().ds.'sites'.ds.'default'.ds.'files'.ds.'user-compendiums'.ds.'My Compendium '.$id.'.pdf';
header("Content-type: application/pdf");
header("Content-Length: " . filesize($filename));
readfile($filename);
exit;
?>
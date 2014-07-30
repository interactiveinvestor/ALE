<?php 

Class InteractiveMap{
	
	public $html='';
	
	public function __construct(){
		
		
		
	}
	
	
	public static function getLocations(){	
		$query = new EntityFieldQuery();
		$query
		  ->entityCondition('entity_type', 'node', '=')
		  ->propertyCondition('status', 1, '=')
		  ->propertyCondition('type', array('property'))
		  ->propertyOrderBy('created', 'DESC');
		;
		$nodes = $query->execute();
		$html='';
		foreach ($nodes['node'] as $key => $_node) {
			$teaser= node_view(node_load($_node->nid), 'teaser');
			$teaser['links']['node']['#links']=array();
			$html.=render($teaser);
		}
		return $html;	
	}
	
	
	
}
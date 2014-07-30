<?php

Class Properties{
	
	
	public function __construct(){
		
	}
	
	public static function getAll(){
		$query = new EntityFieldQuery();
		$query
		  ->entityCondition('entity_type', 'node', '=')
		  ->propertyCondition('status', 1, '=')
		  ->propertyCondition('type', array('property'))
		  ->propertyOrderBy('created', 'DESC');
		;
		$nodes = current($query->execute());
		$properties=array();
		foreach ($nodes as $key => $node) {
			$properties[]=node_load($node->nid);
		}
		return $properties;
	}
	
	public static function getByState($state){		
		$term=current(taxonomy_get_term_by_name(Text::humanize($state)));
		$query="SELECT data.name,data.tid, b.tid,c.entity_id ";
		$query.=" from taxonomy_term_hierarchy as b  INNER JOIN taxonomy_term_data as data ";
		$query.=" INNER JOIN field_data_field_city as c on c.field_city_tid=b.tid ";
		$query.=" WHERE data.name='".Text::humanize($state)."' AND b.parent=data.tid ";
		$results=db_query($query)->fetchAll();
		
		$nodes=array();
		foreach ($results as $key => $result) {
			$nodes[]=node_load($result->entity_id);
		}		
		return $nodes;	
	}
	
	public static function getTotal($field,$nodes){	
		$total=0;
		foreach ($nodes as $key => $node) {
			$values = current(field_get_items('node', $node, $field, $node->language));
			$value=$values['safe_value'];
			$total+=$value;
		}
		return $total;		
	}
	
	public static function getAverage($field,$nodes){	
		$total=0;
		foreach ($nodes as $key => $node) {
			$values = current(field_get_items('node', $node, $field, $node->language));
			$value=$values['safe_value'];
			
			$total+=$value;
		}
		
		$average=$total/count($nodes);
		return  $average;	
	}
	
	
}

function nice_number($n) {
    // first strip any formatting;
    $n = (0+str_replace(",","",$n));

    // is this a number?
    if(!is_numeric($n)) return false;

    // now filter it;
    if($n>1000000000000) return round(($n/1000000000000),2).'T';
    else if($n>1000000000) return round(($n/1000000000),2).'B';
    else if($n>1000000) return round(($n/1000000),1).'<span class="lowercase">m</span>';
    else if($n>1000) return round(($n/1000),2).' thousand';

    return number_format($n);
}


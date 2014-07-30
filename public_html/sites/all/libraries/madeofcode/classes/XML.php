<?php
Class XML{
	
	
	public static function getArray($_xml){	
		if(is_file($_xml)){
			$xml=file_get_contents($_xml);				
		}		
		$xmlArray=simplexml_load_string($xml);
				
		return $xmlArray;		
	}
	
	public static function attribute($object, $attribute)
	{
	    if(isset($object[$attribute])) return (string) $object[$attribute];
			return false;
	}
	
	public static function formatArray($xml,&$output){	
		$__key='';
		
		$reformat=false;
		foreach ($xml as $key => $node) {
			if($key==$__key){
				$reformat=true;
				break;
			}
			$__key=$key;
		}
		
		if($reformat){
			$_xml=array();
			$count=0;
			foreach ($xml as $key => $node) {
				$_xml[$count]=$node;
				$count++;
			}
			$xml=$_xml;
		}

		foreach ($xml as $key => $node) {
			$keyValue=$key;
			$count=	count($node->children());
			$attr=$node->attributes();
			$_key=false;
			if(count($attr)>0){
				foreach ($attr as $attrKey => $a) {
					if($attrKey=='ID' || $attrKey=='KEY'){
						$_key=$keyValue=$a;	
						// if($key=='FIELD-GROUP'){
						// 	$_key=$keyValue=$key.'-'.$a;
						// }		
					}
					else{
						$output[strtolower($keyValue).'-'.strtolower($attrKey)]=(string)$a;
					}
				}
			}		
			if($count==0){
				if($key=='OPTION'&&$key==$keyValue) $output[]=(string)$node;
				else $output[strtolower($keyValue)]=(string)$node;						
			}
			else{
				self::formatArray(&$node,&$output[strtolower($keyValue)]);
			}			
		}
	}
	
	
	
	
}

<?php

Class Form_Util{
	
	
	public static function XMLtoArray($settings){
		$xml=XML::getArray($settings);
		$settings=array();
		XML::formatArray($xml,&$settings);
		return $settings;
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
	
	public static function prepareArray($form,&$origin,&$output,&$data){
		foreach ($origin as $key => $field) {
					
			if($field['type']=='html' || $field['id']=='form_hp' || $field['type']=='token') continue;
			if($field['type']=='field-group'){
				self::prepareArray($form,$field['fields'],&$output,&$data);
			}else{
				$field['value']=trim($field['value']);
				$output[$key]=$field;
				$field_id=str_replace(array('-'),array('_'),$field['id']);
				
				
				if($field['preprocessor']){
					if($field['preprocessor']=='unique-rename'){
						
						//if( empty($form->recordData) || (!empty($form->recordData) && $form->recordData[$field_id]==$data[$field_id] )  ){							
							$table=$form->options['database']['table'];
							$dbObject=new dbObject($table);
							$dbObject->instantiate=false;
							$query="SELECT {$field_id} FROM {$table} WHERE {$field_id} LIKE '{$field['value']}%' ";
							$existingRecords=array_unique(_Array::flatten($dbObject->find_by_sql($query)));
							
							$i=1;
							while(in_array($field['value'].$i,$existingRecords)){
								$i++;
							} 	
							//$field['value']=$field['value'];
							if($i!=1) $field['value'].=$i;
						//}
						
					}
				}
				
				$data[$field_id]=trim($field['value']);
				
				
			}
		}		
	}
	
	public static function createTable($table,$fields,&$options){
		
		if(!$options['database']['write']) return false;
		if(!Db::table_exists($table)){
			$sql="CREATE TABLE {$table} (id	INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
			foreach ($fields as $key => $field) {
					$sql.=str_replace('-','_',$field['id']).' ';						
					if($field['type']=='textarea') $sql.='LONGTEXT ';
					elseif($field['type']=='checkbox' && count($field['options'])==1){
						$sql.='BLOB ';
						$description=(String)current($field['options']);				
						if(!$field['label']){
							$sql.=" COMMENT '{$description}' ";
						} 
					}
					else{
						$sql.='VARCHAR(';

						if($field['validation']['max-length']>0){
							$sql.=$field['validation']['max-length'];
						}elseif($field['type']!='textarea'){
							$sql.='256';
						}				
						$sql.=')';
					} 					
					if($field['required']) $sql.=' NOT NULL ';			
					if($field['label'])	$sql.=" COMMENT '{$field['label']}' ";
					$sql.=', ';
			}			
			$sql=rtrim($sql,', ');			
			$sql.=') CHARACTER SET utf8 COLLATE utf8_general_ci ';				
			Db::query($sql);			
		}
	}
	
	
	
}

?>
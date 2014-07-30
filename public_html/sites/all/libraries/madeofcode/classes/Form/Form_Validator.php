<?php

Class Form_Validator{
	
	
	public function __construct(){
		
		
	}
	
	public static $regex=array(
		'au-phone'=>array(
			'pattern'=>'/^\({0,1}((0|\+61)(2|4|3|7|8))\){0,1}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{1}(\ |-){0,1}[0-9]{3}$/',
			'message'=>'Please make sure you have entered a valid australian phone number.',
			'description'=>'Australian Telephone Numbers',
		),
		'au-postcode'=>array(
			'pattern'=>'/^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$/',
			'message'=>'Please make sure you have entered a valid australian post code.',
			'description'=>'Australian postcode.',
		),
		'url'=>array(
			'pattern'=>'/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',
			'message'=>'',
			'description'=>'URL validation'
		),
		'email'=>array(
			'message'=>'Please make sure you have entered a valid email address.',
			'pattern'=>'/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/',
			'description'=>'e-mail',
		),
		'machine-name'=>array(
			'pattern'=>'/^([a-z0-9_]+)$/',
			'message'=>'This field only allows alphanumeric characters and underscores.',
			'description'=>'Machine name. Only lower-case, alphanumeric characters and underscores allowed.'
		)
	);
	
	
	public function validate(&$fields,&$errors,$options,&$message=''){
		
		$this->errors=&$errors;
		$this->options=&$options;
				
		foreach ($fields as $key => $field) {
			
			if($field['type']=='field-group'){
				$this->validate(&$fields[$key]['fields'],&$errors,$options);
				continue;
			}
				
			$field['value']=$fields[$key]['value']=trim($field['value']);
			
			$_field=&$fields[$key];
			if($field['required'] && empty($field['value'])){
				$this->errors['required'][]=$field['id'];	
				$_field['validation']['message']=$this->options['messages']['errors']['required'];
				continue;	
			}			
			
			//if(!$field['required']) continue;
			
			if($field['type']=='email' && !$this->validate_regex(&$_field,self::$regex['email'])){
				$this->errors['email'][]=$field['id'];		
				continue;	
			}
		
			
			$maxLength=	$field['validation']['max-length'];	
			if($maxLength>0 && !$this->validate_length($_field['value'],$maxLength)){
				$this->errors['max-length'][]=$field['id'];	
				$_field['validation']['message']=str_replace('[max-length]',$maxLength,$this->options['messages']['errors']['max-length']);
				continue;
			}		
				
			if($field['validation']['numeric']){
				if(!is_numeric($field['value'])) $this->errors['numeric'][]=$field['id'];	
				$_field['validation']['message']=$this->options['messages']['errors']['numeric'];
				continue;
			}
						
			if($field['validation']['function']){		
				if(isset(self::$regex[$field['validation']['function']]) && !$this->validate_regex(&$_field,self::$regex[$field['validation']['function']])){					
					$this->errors['regex'][]=$field['id'];	
					continue;
				}				
			}	
		}	
		
		if(!empty($errors)){
			$message=$options['messages']['error'];
		}
		
	}
	
	public function detectRobot($options,$fields){
		if($options['fields-settings']['default-fields']['form_hp']['enabled'] && !empty($fields['form_hp']['value'])){
			$this->isRobot=true; 
			WWW::redirect($options['actions']['default']['action']);
		}
	}
	
	public static function validate_length($value,$length) {
		if (strlen(trim($value)) > $length ) return false;		
		return true;
	}
	
	public static function validate_regex(&$field,$regex){
		if(!$field['validation']['message']){		
			$field['validation']['message']=$regex['message'];
		}
		return preg_match($regex['pattern'],$field['value']);
	}
	
	public static function validate_file(&$errors,$field){
		global $pic;
		require_once(ROOT.'/core/classes/photo.php');
		$pic= new Photo();
		$local_path=basename($_FILES['upload']['name']);
		$pic->attach_file($_FILES['upload']);
		$pic->checkFormat($field['formats']);
		foreach($pic->errors as $error){
			$errors[]=$error;
		}
		
	}
	
	
	
}


?>
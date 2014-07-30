<?php

Class Object{
	
	protected $defaultOptions;
	public $options=array();
	protected $templateVars=array();
	
	public function __construct($userOptions){
		$this->gatherOptions($this->defaultOptions,$userOptions,&$this->options);
	}
	
	public function formatClassName($value){	
		$value=ucFirst(str_replace(array('-'),array('_'),$value));
		return $value;		
	}
	
	public function formatMethodName($value){	
		$value=ucWords(str_replace(array('-'),array(' '),$value));
		$value=str_replace(array(' '),array('_'),$value);
		
		return $value;		
	}
	
	public static function fallBack($function,$args){
		global $www;
		$class_method=explode('->',$function);
		if(count($class_method)==2){
			if($www->AdminObjects[$class_method[0]]){
				return $www->AdminObjects[$class_method[0]]->$class_method[1]($args);
			}
		}
		
		return false;
	}
	
	
	public function Template($file){	
		
		if(isset($this->templateVars)){
			foreach ($this->templateVars as $name => $val) {
				$varName=Text::machine_name($name);
				$$varName=$val;
			}
		}		
		
		if(is_file($file)){
			ob_start();
	    include($file);
	    return ob_get_clean();
		}	
	}
	
	public static function gatherOptions(&$default,&$user,&$options, $userPreference=false){
		
		//$userPreference ? $_default=$user : $_default=$default;
		
		foreach ($default as $key => $opt) {		
			if(is_array($opt)){				
				if(isset($user[$key])){
					self::gatherOptions(&$default[$key],&$user[$key],&$options[$key],$userPreference);
				}else{
					$options[$key]=$opt;
				}
				
			}else{				
				if(isset($user[$key])){
					$options[$key]=$user[$key];
				}else{
					$options[$key]=$opt;
				}
			}		
		}	
		
		if($userPreference){
			foreach ($user as $key => $opt){
				if(!isset($default[$key])){
					$options[$key]=$opt;
				}
			}
		}
		
	}
	
	
}

?>
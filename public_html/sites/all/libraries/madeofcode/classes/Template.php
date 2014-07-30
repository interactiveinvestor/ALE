<?php
Class __Template{
	
	
	public function __construct($file,$variables=null){
		
		return $this->getTemplate($file,$variables);

		
	}
	
	private function getTemplate($file,$variables){

		if(!is_null($variables)){
			foreach ($variables as $name => $val) {
				$varName=Text::machine_name($name);
				$$varName=$val;
			}
		}		
		
		if(is_file($file)){
			ob_start();
	    include($file);
	    $this->html= ob_get_clean();
		}
	}
	
	
}
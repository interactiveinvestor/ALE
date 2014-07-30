<?php
Class Form_Router{
	//Options
	private $opt=array();
	
	public function __construct(){
		
		
	}
	
	public function preprocessData($options,&$sentData,&$dataSent,&$recordData){
		
		
	
		foreach ($_POST as $key => $value) {
			if(strstr($key,$options['form-id'].'-action-')){
				$action= str_replace($options['form-id'].'-action-','',$key);
				$_POST['submit-action']=$action;
				
				header('Location: '.$options['actions']['default']['action']);
			}
		}
		
		if(isset($_SESSION[$options['form-id']])){
			$sentData=$_SESSION[$options['form-id']];		
			unset($_SESSION[$options['form-id']]);
			$dataSent=true;
		}	
	}
	
	public function route(&$form,&$method, &$recordData,&$action){
		
		
		$action='';
		$confirmVarName=$form->options['form-id'].'-confirm';
		echo '<pre>';
		print_r($form->sentData);
		echo '</pre>';		
		//die();
		if(!empty($form->sentData)){	
			
				
			$tokenEnabled=$form->options['fields-settings']['default-fields']['token']['enabled'];		
			$tokenVarName=$form->tokenVarName=$form->options['form-id'].'-form-token';		
			if($tokenEnabled){							
				if(!isset($_SESSION[$tokenVarName]) || isset($form->sentData[$tokenVarName]) && $_SESSION[$tokenVarName]['value']!=$form->sentData[$tokenVarName]){				
					$this->html='This form has expired';
					unset($_SESSION[$tokenVarName]);
					return false;
				} 
			}
			
			if(isset($form->sentData['submit-action'])) $action=$form->sentData['submit-action'];
			else return true;
			
			$method=$action.'Action';		
			if (isset($_SESSION[$confirmVarName])){
				$confirm=&$_SESSION[$confirmVarName];				
				if($form->sentData['submit-action']=='confirm'){					
					$action=$confirm['action'];
					$form->sentData=$confirm['sent-data'];
					$form->options['actions']=$confirm['actions'];					
					$method=$action.'Action';					
				}				
				$recordData=$confirm['record-data'];				
				if($form->sentData['submit-action']=='cancel'){
					$action='';
					$form->sentData=array();
				}				
				unset($_SESSION[$confirmVarName]);
				return true;
			}


			if(isset($form->options['actions'][$action])){			
				$confirmRequired=$form->options['actions'][$action]['confirm-required'];		
				if($confirmRequired){						
					unset($_SESSION[$confirmVarName]);
					$_SESSION[$confirmVarName]=array(
						'sent-data'=>$form->sentData,
						'record-data'=>$recordData,
						'action'=>$action,
						'actions'=>$form->options['actions']
					);							
					$this->renderConfirm(&$form,$action,'Are you sure you want to '.$action.' this record?');												
					//$this->confirmAction($form->sentData,$action,);
					 return false;
				}		
			}
		}		
		
					
		return true;
		
	}
	
	private function renderConfirm(&$form,$action,$message){
		$options=$form->options;	
		$variables=array(
			'form-id'=>$options['form-id'],
			'action'=>$options['actions']['default']['action'],
			'method'=>$options['method'],
			'message'=>$message,
			'action'=>$action,
			'cancel-value'=>$options['buttons']['cancel']['value'],
			'confirm-value'=>$options['buttons']['confirm']['value']			
		);
		$template=new Template(_CLASSES.'Form'.DS.'templates'.DS.'confirm.tpt.php',$variables);
		$form->html.=$template->html;
	}
	
}

?>
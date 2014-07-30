<?php

Class Form extends Object {
	
	// VARIABLES =>
	
	protected $defaultOptions=array(		
		'form-id'=>'form',		
		'database'=>array(
			'write'=>0,
			'table'=>0,		
		),
		'actions'=>array(
			'default'=>array(
				'action'=>'/',
				'confirm-required'=>0,
				'messages'=>array(
					'error'=>'Something went wrong',
					'success'=>'Submission successful',
				),
			),
			'error'=>array(
				'action'=>'/',
				'confirm-required'=>0,
			),
			'create'=>array(
				'write'=>0,
				'action'=>'/',
				'message'=>'The record was successfully created.',
				'confirm-required'=>0,
				'messages'=>array(
					'error'=>'Cannot Create Record',
					'success'=>'Record Created',
				),
			),
			'update'=>array(
				'write'=>0,
				'action'=>'/',
				'message'=>'The record was successfully updated.',
				'confirm-required'=>0,
				'messages'=>array(
					'error'=>'The record was updated sucessfully',
					'success'=>'',
				),
			),
			'delete'=>array(
				'write'=>0,
				'action'=>'/',
				'message'=>'The record was successfully deleted.',
				'confirm-required'=>1,
				'messages'=>array(
					'error'=>'',
					'success'=>'',
				),
			),
			'confirm'=>array(
				'write'=>0,
				'action'=>'/',
				'message'=>'',
				'confirm-required'=>0
			),
			'fallback'=>array(
				'write'=>0,
				'action'=>'/',
				'message'=>'',
				'confirm-required'=>0,
				'method'=>0,
				'messages'=>array(
					'error'=>'',
					'success'=>'',
				),
			),
		),
		'method'=>'post',
		'field-error-message'=>1,
		'value-separator'=>', ',
		'html'=>array(
			'field-wrapper'=>'div',
			'form-wrapper'=>'div'
		),
		'fields-settings'=>array(
			'default-settings'=>array(
				'label'=>0,
				'type'=>'html',
				'required'=>0,
				'empty-value'=>'',
				'weight'=>NULL,
				'validation'=>array(
					'max-length'=>0,
					'numeric'=>0,
					'function'=>0,
					'message'=>0,
					'unique'=>0,
					'unique-action'=>0
				),
				'options-label-position'=>'after',
				'label-position'=>'before',
				'description'=>0,
				'placeholder'=>0,
				'description-position'=>'before',
				'default'=>'',
				'wrapper'=>1,
				'preprocessor'=>0
			),
			'required-symbol'=>'<span> *</span>',
			'date'=>array(
				'format'=>'d F Y',
				'label'=>false,
				'months'=>array(
					'January','February','March','April','May','June','July','August','September','October','November','December'
				),
				'year-range'=>array(1900,'current'),
				'year'=>true,
				'day'=>true,
				'month'=>true,
				'day-label'=>'Day',
				'year-label'=>'Year',
				'month-label'=>'Month',
			),
			
			'default-fields'=>array(
				'form_hp'=>array(
					'enabled'=>1,
					'field'=>array(
						'type'=>'text',
						'label'=>'Silence is golden, please skip this one.'
					),
				),
				'token'=>array(
					'enabled'=>1,
					'field'=>array(
						'type'=>'token',
						'value'=>'',
					),
				),
			),
			
		),
		'buttons'=>array(
			'submit'=>array(
				'value'=>'submit',
				'name'=>'submit'		
			),
			'delete'=>array(
				'value'=>'delete',
				'name'=>'delete'		
			),
			'cancel'=>array(
				'value'=>'Cancel',
				'name'=>'cancel'		
			),
			'confirm'=>array(
				'value'=>'Confirm',
				'name'=>'confirm'		
			),
		),		
		'messages'=>array(
			'errors'=>array(
				'generic'=>'There are errors in your form, please correct them and try again.',
				'required'=>'This field is required.',
				'max-length'=>'Please ensure this fields has no more than [max-length] characters.',
				'numeric'=>'Please ensure this field consists only of numbers'
			),
			'success'=>'The operation was performed successfully.',
			'error'=>'There are errors in your form, please correct them and try again.',
			'session'=>1
		),		

	);
	
	private $isRobot=false;
	public $errors=array();	
	public $html='';
	private $fieldsHtml='';
	private $fieldId='';
	private $fieldRegistry=array();
	private $fieldIndex=0;
	public $fields=array();
	public $sentData=array();
	public $dataSent=false;
	private $recordData=array();
	public $settings=array();
	private $action='';
	private $actions;
	public $tokenVarName;
	public $constructor;
	public $formErrorMessage='';
	
	public static $valuePreprocessors=array(
		'unique-ignore'=>array(
			'description'=>'Unique Ignore: ignore whole form submission if field value exists in database.'
		),
		'unique-update'=>array(
			'description'=>'Unique Update: update record if value exists in database.'
		),
		'unique-rename'=>array(
			'description'=>'Unique Rename: if an equal value already exists in the database the field will be renamed appending a numeric value at the end.'
		),
		'join'=>array(
			'description'=>'Join Values: add field value to existing values in the database',
			'value-separator'=>','
		),
		
	
	);
	
	//end
		
	public function __construct($settings,$data=null,$init=true){			
		$this->init=$init;
		$this->getOptions($settings);
		if(!is_null($data)) $this->recordData=$data;
		$this->constructor= new Form_Constructor();
		$this->validator= new Form_Validator();
		$this->router= new Form_Router();
		if($init) $this->init();
	}
	
	public function getOptions($settings){	
		$this->settings=$settings;
		foreach ($this->defaultOptions['actions'] as $key => $value) {
			$this->defaultOptions['actions'][$key]['action']=$_SERVER['REQUEST_URI'];
		}	
		if(!is_array($this->settings) && is_file($this->settings)){
			if(array_pop(explode('.',$this->settings))=='xml'){									
				$this->settings=Form_Util::XMLtoArray($this->settings);
			}
		}
	}
	
	public function init(){		
		parent::__construct(&$this->settings['options']);
			

		$this->router->preprocessData($this->options,&$this->sentData,&$this->dataSent,&$this->recordData);	
		
		//if(!$this->router->route(&$this,&$method,&$this->recordData,&$this->action)) return false;
		$this->setupFields();
		
		$actionPassed=false;	
		$method='submit';
		if(method_exists($this,$method)) $actionPassed = $this->$method();
		echo '<pre>';
		print_r($this->sentData);
		echo '</pre>';
		// if(empty($this->sentData) && isset($_SESSION[$this->options['form-id'].'-confirm'])){
		// 	unset($_SESSION[$this->options['form-id'].'-confirm']);
		// }
		
		$this->setMessage($actionPassed);
		
		if(!empty($this->action) && empty($this->errors)){			
			if(isset($this->options['actions'][$this->action])){
				redirect($this->options['actions'][$this->action]['action']);
			}else{
				redirect($_SERVER['REQUEST_URI']);
			}
		}
		
		$this->render();	
	}
		
	private function setMessage($success){
		if($this->action=='submit') $this->action='default';
		$action=$this->action;
		$options=$this->options;
		if(!empty($action)){
			
		
			$success==false ?  $status='error': $status='success';	
			$messages=$options['messages'];	
			if(isset($options['actions'][$action])){
				$message=$options['actions'][$action]['messages'][$status];
				if(empty($message)) $message=$options['messages'][$status];
				drupal_set_message($message,$status);
			}
			
		}
	}
	
	private function submitAction(){
		
		$this->validator->detectRobot($this->options,$this->fields);
		$this->validator->validate($this->fields,&$this->errors,&$this->options,&$this->formErrorMessage);
		echo '<pre>';
		print_r($this->errors);
		echo '</pre>';
		if(empty($this->errors)) return $this->save();
		else return false;
	}
	
	private function save(){
		$passed=false;
		if($this->options['database']['table']){	
			$table=$this->options['database']['table'];
			$data=$rows=$fields=array();
			//flattens array for output (field-groups)
			Form_Util::prepareArray($this,&$this->fields,&$fields,&$data);
			Form_Util::createTable($table,$fields,&$this->options);
			$actions=$this->options['actions'];
			
			$redirect=$actions['default']['action'];


			
			if(($actions['create']['write'] || $actions['update']['write'])){
				$dbObject=new DbGeneric($table);
				$dbObject->data=$data;
				if(!empty($this->recordData)){
					$dbObject->data['id']=$this->recordData['id'];
					$this->action='update';
					$redirect=$actions['update']['action'];					
					$dbObject->update();
					$passed=true;
					
				}else{
					$this->action='create';
					$id=$dbObject->save();
					if(isset($data['name_id'])) $id=$data['name_id'];
					$this->options['actions']['create']['action']=str_replace('[id]',$id,$actions['create']['action']);
					$passed=true;
				}	
			}			

			if($actions['fallback']['write'] && $actions['fallback']['method']){
				$this->action='fallback';
				if(isset($this->recordData['id'])){
					$data['id']=$this->recordData['id'];
				}
				$passed=self::fallBack($actions['fallback']['method'],$data);				
			}					
		}
		return $passed;
	}
	
	private function deleteAction(){
		$record=new DbObject($this->options['database']['table']);
		$record->data=$this->recordData;
		$record->delete();
		return true;
	}
	
	private function cancelAction(){
		$this->actions='default';
		return true;		
	}
	
	//end

	private function render(){
		
		//$this->fields=$this->orderFields($this->fields);

		$options=$this->options;	
		$variables=array(
			'form-id'=>$options['form-id'],
			'action'=>$options['actions']['default']['action'],
			'method'=>$options['method'],
			'error-message'=>$this->formErrorMessage,
			'wrapper-tag'=>$options['html']['form-wrapper'],
			'fields'=>$this->constructor->renderFields($this->fields,$this->sentData,$this->recordData,$this->options,$this->dataSent,$this->errors),
			'submit-value'=>$options['buttons']['submit']['value'],
			'delete'=>$options['actions']['delete']['write'],
			'delete-value'=>$options['buttons']['delete']['value'],
			'record-data'=>$this->recordData,			
		);
		$template=new __Template(dirname(__FILE__).ds.'templates'.ds.'form.tpt.php',$variables);
		$this->html.=$template->html;		
	}
	
	public function setupFields(){		
		$default_fields=$this->options['fields-settings']['default-fields'];		
		foreach ($default_fields as $key => $field) {
			if($field['enabled']) $this->fields[$key]=$field['field'];
		}		
		$this->fields=array_merge($this->fields,$this->settings['fields']);
		$this->processFields(&$this->fields);
	}
	
	public function processFields(&$fields){
		$counter=0;
		foreach ($fields as $key => $field) {
			if(!isset($field['type']) || !method_exists($this->constructor,'field_'.str_replace('-','_',$field['type'])) ){
				unset($fields[$key]); 
				continue;
			}
			

			
			if(!is_numeric($key)){
				$register=Text::machine_name($key);
			}else{
				isset($field['name']) ? $register=$field['name'] : $register=$field['type'];		
			}		
			
			$this->addToFieldRegistry($register);	
			$fields[$key]['id']=$this->fieldRegistry[$this->fieldIndex];
			$this->fieldIndex++;	
			
			$this->gatherOptions($this->options['fields-settings']['default-settings'],$field,&$fields[$key]);
			
			if(is_null($fields[$key]['weight'])){
				$fields[$key]['weight']=$counter;
			}
			
			if($field['type']=='field-group'){						
				$this->processFields(&$fields[$key]['fields']);
				continue;
			}				
			
			
			$_field=&$fields[$key];			
			
			
			if($field['type']=='date'){	
				$this->gatherOptions($this->options['fields-settings']['date'],$field['settings'],&$_field['options']);			
				$dateField=array(
					'type'=>'select',
					'required'=>$_field['required']
				);		
				if($_field['options']['day']){				
					$day_field=array(
						'empty-value'=>'Day',
						'id'=>$_field['id'].'-day'
					);
					$this->gatherOptions($dateField,$day_field,&$day_field);					
					$this->getFieldValue(&$day_field);
					for ($i=1; $i <=31 ; $i++) $day_field['options'][]=$i;
					$_field['fields']['day']=$day_field;	
				}
				
				if($_field['options']['month']){
					$month_field=array(
						'options'=>$_field['options']['months'],
						'empty-value'=>'Month',
						'id'=>$_field['id'].'-month'
					);				
					$this->gatherOptions($dateField,$month_field,&$month_field);
					$this->getFieldValue(&$month_field);	
					$_field['fields']['month']=$month_field;
				}
				
				if($_field['options']['year']){
					$year_field=array(
						'options'=>array(),
						'empty-value'=>'Year',
						'id'=>$_field['id'].'-year'
					);	
					$this->gatherOptions($dateField,$year_field,&$year_field);	
					$this->getFieldValue(&$year_field);
					if($_field['options']['year-range'][1]=='current'){
						$_field['options']['year-range'][1]=date('Y');
					}

					for ($i=$_field['options']['year-range'][1]; $i >=$_field['options']['year-range'][0] ; $i--) $year_field['options'][]=$i;
					$_field['fields']['year']=$year_field;
					
				}
				
			}
			
			$this->getFieldValue(&$fields[$key]);
			$counter++;
			
		}
		_Array::sort_by_column(&$fields, 'weight');
	}
	
	private function addToFieldRegistry($register){
		$count=1;
		$countStr='';
		while(in_array($register.$countStr,$this->fieldRegistry)){
			$count++;
			$countStr='-'.$count;
			if($count==1){
				$countStr='';
			}
		}			
		
		$this->fieldRegistry[]=str_replace('-','_',$register.$countStr);			
	}
	
	private function getFieldValue(&$field){		
		
		if(!empty($this->recordData) && isset($this->recordData[$field['id']]) && empty($this->sentData)){
			$field['value']=$this->recordData[$field['id']];
			return;
		}
		
		$valueExceptions=array('html','hidden');		
		if( in_array($field['type'],$valueExceptions)) return false;	
		if($field['type']=='date'){
			$values=array();
			foreach ($field['fields'] as $key => $_field) {			
				if($field['required']  && empty($_field['value'])){
					$values=array();
					break;
				}
				$values[]=$_field['value'];
			}
			
			if(!empty($values)){
				$value=join(' ',$values);
				$value=strtotime($value);
				$field['value']=date($field['options']['format'],$value);
			}else{
				$field['value']='';
			}	
		}
		else if($field['type']=='checkbox'){
			$selected=array();
			for ($i=1; $i <= count($field['options']); $i++) { 		
				if(isset($this->sentData[$field['id'].$i])){
					$selected[]=$this->sentData[$field['id'].$i];
				}
			}
			if(count($field['options'])==1){
				!empty($selected) ? $field['value']=1:$field['value']=0;			
			}else{
				$field['value']=join($this->options['value-separator'],$selected);
			}
		}		
		else if(isset($this->sentData[$field['id']])){
			$field['value']=$this->sentData[$field['id']];	
		} 
		elseif(isset($field['default'])){
			$field['value']=$field['default'];		
		} 
		else $field['value']='';
		
	}
	
	//end
	
}


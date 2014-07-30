<?php

Class Form_Constructor{
	
	private $form;
	
	public function __construct(){
		//$this->form=$form;
	}
	
	public function renderFields($fields,$sentData,$recordData,$options,$dataSent,$errors){

		$html='';	
		$this->sentData=$sentData;
		$this->recordData=$recordData;
		$this->options=$options;
		$this->dataSent=$dataSent;
		$this->errors=$errors;
		
		foreach ($fields as $key => $field) {	
						
			$field_function='field_'.str_replace('-','_',$field['type']);
			$fieldHtml=$this->$field_function($field);	
					
			!empty($errors) && _Array::in_multiarray($field['id'],$errors) ? $error=true : $error=false;				
			
			// Field Wrapper =>
			$hasWrapper=true;
			if($field['type']=='hidden' || $field['type']=='html' || $field['type']=='token'){
				$hasWrapper=false;
			}
			
			$wrapper_classes=array();
			if($field['type']=='email') $wrapper_classes[]='form-type-text';
			$field_wrapper_class='';
			if($error){
				$wrapper_classes[]='field-error';
				$field_wrapper_class=' field-wrapper-error';
				$options['field-error-message'] && $field['validation']['message'] ? $hasErrorMessage=true : $hasErrorMessage=false;
			} 

			if($hasWrapper==true){
				$html.="<{$options['html']['field-wrapper']}".
				" class='form-field ".join(' ',$wrapper_classes)." form-type-{$field['type']} field-{$field['id']}'>";
			}			
			//end
			
			//Field Label	
			$field['type']=='field-group' ? $inputWrapperClass='field-group-wrapper' : 	$inputWrapperClass='input-wrapper';
			$html.=$this->label($field,'before');
			if($hasWrapper) $html.='<div class="'.$inputWrapperClass.$field_wrapper_class.'">';
			if($options['field-error-message'] && $error && $field['validation']['message']){
				$html.='<div class="error-description">'.$field['validation']['message'].'</div>';
			}
			$html.=$this->description($field,'before');
			$html.=$fieldHtml;
			$html.=$this->description($field,'after');		
			if($hasWrapper) $html.='</div>';
			$html.=$this->label($field,'after');							
			if($hasWrapper) $html.="</{$options['html']['field-wrapper']}>";		
		}	
		
		return $html;		
	}
	
	
	public function field_token(){
		$varName=$this->options['form-id'].'-form-token';
		unset($_SESSION[$varName]);
		$_SESSION[$varName]['value']=$token=md5(uniqid(rand(), TRUE));
		$_SESSION[$varName]['time'][]=time();
		return "<input type='hidden' name='$varName' value='$token' />";
	}
	
	public function field_password($field){
		$html="<input type='password' value='".$field['value']."' name='{$field['id']}' id='{$field['id']}' />";
		return $html;
	}
	
	public function field_label($field){
		if(!isset($field['type']) || !isset($field['label']) || empty($field['label'])) return '';
		$forExceptions=array('checkbox','date','radio');
		$label="<label";
		if(!in_array($field['type'],$forExceptions)) $label.=' for="'.$field['id'].'" ';
		$label.='>';
		$label.=$field['label'];
		if(isset($field['required']) && $field['required']==true){
			$label.=$this->options['fields-settings']['required-symbol'];
		}
		
		$label.="</label>";
		return $label;		
	}
	
	public function field_html($field){
		return $field['value'];
	}
	
	public function field_text($field){
		$placeholder='';
		if($field['placeholder']) $placeholder = "placeholder='".$field['placeholder']."'";
		$html="<input type='text' $placeholder value='".$field['value']."' name='{$field['id']}' id='{$field['id']}' />";
		return $html;
	}
	
	public function field_textarea($field){
		$html="<textarea name='{$field['id']}' id='{$field['id']}'>".$field['value']."</textarea>";
	
		return $html;
	}
	
	public function field_email($field){		
		return $this->form->field_text($field);	
	}
	
	public function field_select($field){
		$html="<select id ='".$field['id']."' name='".$field['id']."'>";
		$html.="<option value=''>{$field['empty-value']}</option>";
		if(!empty($field['options'])){
			foreach ($field['options'] as $key => $option) {
				!is_numeric($key) ? $value=$key:$value=$option;				
				$field['value']==$value ? $selected='selected="selected"' : $selected='';
				$html.="<option $selected value='$value'>$option</option>";
			}
		}			
		$html.="</select>";		
		return $html;
	}
	
	public function field_checkbox($field){
		$count=0;
		$html='';
		if(!isset($field['options-label-position'])) $field['options-label-position']='before';
		
		foreach ($field['options'] as $key => $option) {
			!is_numeric($key) ? $value=$key:$value=$option;
			if(count($field['options'])==1 && !empty($option)){
				$value=1;
			}
			$count++;
			$checked='';
			if( (isset($this->sentData[$field['id'].$count]) && $value==$this->sentData[$field['id'].$count])){
				$checked='checked';
			}
			else if(!isset($this->sentData[$field['id'].$count]) && !$this->dataSent){
				empty($this->recordData) ? $default=$field['default']:$default=$this->recordData[$field['id']];
				$defaults=explode($this->options['value-separator'],$default);
				if(in_array($value,$defaults)){
					$checked='checked';
				}		
			}
			
			$html.='<div class="checkbox-option">';
			$label="<label for='".$field['id'].$count."'>$option</label>";
			if($field['options-label-position']=='before') $html.=$label;			
			$html.="<input $checked id='".$field['id'].$count."' type='checkbox' name='".$field['id'].$count."' value='$value'/>";
			if($field['options-label-position']=='after') $html.=$label;
			$html.='</div>';
		}
		return $html;
		
	}
	
	public function field_radio($field){
		$count=0;
		$html='';
		
		foreach ($field['options'] as $key => $option) {
			$count++;
			$option==$field['value'] ? $checked='checked': $checked='';
			
			$html.='<div class="radio-option">';
			$label="<label for='".$field['id'].$count."'>$option</label>";
			if($field['options-label-position']=='before') $html.=$label;
				$html.="<input $checked id='".$field['id'].$count."' type='radio' name='{$field['id']}' value='$option'/>";
			if($field['options-label-position']=='after') $html.=$label;
			$html.='</div>';
		}
		return $html;	
	}
	
	public function field_date($field){	
		$html='';		
		if(!isset($field['options'])) $field['settings']=array();	
		$options=$field['options'];		
		if($options['day']){		
			$html.='<div class="day">';
				if(isset($_field['label'])) $html.="<label for='{$_field['id']}' >{$options['day-label']}</label>";
				$html.=$this->form->field_select($field['fields']['day']);						
			$html.="</div>";		
		}
		
		if($options['month']){
			$html.='<div class="months">';
				if(isset($_field['label'])) $html.="<label for='{$_field['id']}' >{$options['month-label']}</label>";
				$html.=$this->form->field_select($field['fields']['month']);					
			$html.="</div>";		
		}		
		
		if($options['year']){						
			$html.='<div class="year">';
				if(isset($_field['label'])) $html.="<label for='{$_field['id']}' >{$options['year-label']}</label>";
				$html.=$this->form->field_select($field['fields']['year']);					
			$html.="</div>";			
		}		
		return $html;		
	}
	
	public function field_field_group($field){
		return $this->renderFields($field['fields'],$this->sentData,$this->recordData,$this->options,$this->dataSent,$this->errors);
	}
	
	public function field_hidden($field){	
		return "<input type='hidden' name='{$field['id']}' value='{$field['value']}' />";
	}
	
	public function label($field,$position){
		$html='';		
		if(!isset($field['label-position'])) $field['label-position']='before';
		if($field['label-position']==$position){
			$html.=$this->field_label($field);
		}
		return $html;
	}
	
	private function description($field,$position){
		$html='';
		if(isset($field['description']) && $field['description'] && $field['description-position']==$position){
			$html='<div class="field-description field-description-'.$position.'">'.$field['description'].'</div>';
		}
		return $html;
	}
	
}


?>
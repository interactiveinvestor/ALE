<?php

Class _Array{
	
	public static function in_multiarray($value, $array){
		foreach ($array AS $item){
			if (!is_array($item)){
				if ($item == $value) return true;
				continue;
			}
			if (in_array($value, $item)) return true;
			else if (self::in_multiarray($value, $item)) return true;
		}
		return false;
	}
	
	
	public static function _list($array,$title='',$recursion=false){
		$html='';
		if(!$recursion){
			$html.='<div class="array-list"><div class="key">';
			if(!empty($title)){
				$html.=$title;
			}else{
				$html.='- Array List';
			}
			$html.='</div>';		
		}
		$recursion ? $listClass=" class='value' " : $listClass='';
		
		$html.="<ul $listClass>";		
		foreach ($array as $key => $value) {
			$html.='<li>';
			$html.="<div class='key'>";
			if(is_array($value)){
				$html.="<div class='array'>$key <span>&nbsp;&nbsp;array = &gt;</span> </div>";
			}else{
				$html.=$key.' :';
			}
			$html.="</div>";
			if(is_array($value)){
				$html.= self::_list($value,'',true);
			}else{
				$html.="<div class='value'>$value</div>";
			}
			$html.='</li>';
		}		
		$html.='</ul>';			
		if(!$recursion) $html.='</div>';		
		return $html;
	}
	
	
	public static function sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	    $sort_col = array();
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }

	    array_multisort($sort_col, $dir, $arr);
	}
	
	public static function flatten ($array){
	    $i = 0;
	    while ($i < count ($array))
	    {
	        while (is_array ($array[$i]))
	        {
	            if (!$array[$i])
	            {
	                array_splice ($array, $i, 1);
	                --$i;
	                break;
	            }
	            else
	            {
	                array_splice ($array, $i, 1, $array[$i]);
	            }
	        }
	        ++$i;
	    }
	    return $array;
	}
	
	public static function objToArray($obj) {
	    if(is_object($obj)) $obj = (array) $obj;
	    if(is_array($obj)) {
	      $new = array();
	      foreach($obj as $key => $val) {
	        $new[$key] = self::objToArray($val);
	      }
	    }
	    else { 
	      $new = $obj;
	    }
	    return $new;
	}
	
}

<?php
Class Text{
	
	
	public static function get_string_between($string, $start, $end){	
		
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}
	
	public static function machine_name($human_name) {
	  return strtolower(preg_replace(array(
	    '/[^a-zA-Z0-9]+/',
	    '/-+/',
	    '/^-+/',
	    '/-+$/',
	  ), array('_', '_', '', ''), $human_name));
	}
	
	public static function humanize($value){	
		$value=ucFirst(str_replace(array('-','_'),array(' ',' '),$value));	
		return $value;
	}
	
}

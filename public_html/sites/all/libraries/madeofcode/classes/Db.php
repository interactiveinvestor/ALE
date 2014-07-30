<?php

class Db{
	
	private static $connection;
	public static $last_query;
	private $magic_quotes_active;
	private static $real_escape_string_exists;
	private $dbInfo;
	
	function __construct($dbInfo){
		$this->dbInfo=$dbInfo;
		$this->open_connection($dbInfo);
		$this->magic_quotes_active = get_magic_quotes_gpc();
		self::$real_escape_string_exists = function_exists("mysql_real_escape_string");
		@mysql_query("SET NAMES 'utf8'");
	}
	
	public function open_connection($dbInfo){
		self::$connection = mysql_connect($dbInfo['host'],$dbInfo['user'],$dbInfo['pass']);
		if (!self::$connection) {
			die("Database connection failed: " . mysql_error());
		}else{
			$db_select = mysql_select_db($dbInfo['name'],self::$connection);
			if (!$db_select) {
				die("Database selection failed: " . mysql_error());
			}
		}
	}
	
	public function close_connection(){
		if(isset(self::$conneciton)){
			mysql_close(self::$conneciton);
			unset(self::$conneciton);
		}	
	}

	public static function query($sql,$returnObject=false){
		
		self::$last_query=$lastQuery=$sql;
		$results= mysql_query($sql, self::$connection);
		self::confirm_query($results);
				
		if($returnObject==true){			
			$resultArray=array();			
			while ($result=mysql_fetch_array($results)) {
				$resultArray[]=$result;	
			}				
			if(count($resultArray)==1){
				if(isset($resultArray[0][0])){
					return $resultArray[0][0];
				} 
			}else{
				foreach ($resultArray as $key => $result) {
					foreach($result as $valueKey => $value){
						if(is_numeric($valueKey)){
							unset($resultArray[$key][$valueKey]);
						}
					}
				}
			}			
			return $resultArray;			
		}		
		
		return $results;			
	}
	
	private static function confirm_query($result){
		if(!$result){			
			$output= "WOOOHOO, we don't know where did you get this address from but it certainly does not exist in this website.";
			$output= "Database query failed:".mysql_error() . "<br/><br/>";
			$output.= "Last SQL query: ". self::$last_query;
			// die($output);
			Session::message($output,'error');
		}
	}

	public static function escape_value($value){		
		if(self::$real_escape_string_exists){ 
			return mysql_real_escape_string($value); 
		}	    
		return addslashes($value);	
	}
	
	public function fetch_array($result_set){
		if($result_set){
			return mysql_fetch_array($result_set);
		}
			
		
	}
	
	public function fetchAll($result_set){
		$results=array();
		while ($row = mysql_fetch_row($result_set)) {
		    $results[]=current($row);
		}
		
		return $results;
	}
	
	public static function fetch($result_set){
		return mysql_fetch_array($result_set);
	}
	
	public function get_all_tables(){	
		$tables=self::query('SHOW TABLES FROM '.$this->dbInfo['name']);
		$tables=$this->fetchAll($tables);
		return $tables;
	}
	
	public static function table_exists($table_name){
		$result=self::query("SHOW TABLES LIKE '$table_name'");

		return self::fetch($result);
	}
	
	public function num_rows($result_set){
		return mysql_num_rows($result_set);
	}
	
	public function insert_id(){
		return mysql_insert_id(self::$connection);
	}
	
	public function affected_rows(){
		return mysql_affected_rows(self::$connection);
	}
	
	public function mysql_current_db() {
	    $r = mysql_query("SELECT DATABASE()") or die(mysql_error());
	    return mysql_result($r,0);
	}

}

?>
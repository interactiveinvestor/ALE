<?php
//require_once(CLASSES.DS."database.php");
class DbObject{
	protected $table_name;
	public static $row_names=array();
	public static $raw;
	private $table_attributes;
	public static $data;
	public static $db;
	public static $session;
	public $instantiate=true;
	
	public function __construct($table_name=null,$instantiate=true){		
		$class_name=get_called_class();
		$this->instantiate=$instantiate;
		if(!is_null($table_name)){			
			$this->table_name=$table_name;	
					//echo $table_name;
			// $exists=self::find_by_sql("SHOW TABLES LIKE '$table_name'");
			// echo '<pre>';
			// print_r($exists);
			// echo '</pre>';
		}
		// elseif(!isset($this->table_name)){			
		// 	//throw new Exception('The table_name property of the declared class has no set value; $table_name="sql"');
		// }
		 $this->raw=true;

	}
	
	public function fetchAll(){	
		$class_name=get_called_class();	
		return self::find_by_sql("SELECT * FROM ".$this->table_name);		
	}
	
	public function find_by_id($id=0){
		$class_name=get_called_class();		
		$result_array=$this->find_by_sql("SELECT * FROM ".$this->table_name." WHERE id = ".Db::escape_value($id)." LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;	
	}
	
	public function find_by_attribute($row,$value='',$limit=''){
		$class_name=get_called_class();
		$table_name= $this->table_name;	
		$query="SELECT * FROM ".$table_name." WHERE {$row} = '".Db::escape_value($value)."'";
		if(!empty($limit) && is_numeric($limit)) $query.=' LIMIT '.$limit;
		$result_array=$this->find_by_sql($query);
		return $result_array;
	}	
	
	public function find_by_sql($sql=""){
		$result_set=Db::query($sql);
		$data=array();
		$class_name=get_called_class();
		
		while($row= Db::fetch_array($result_set)){
			$data[]=$row;				
		}
		foreach ($data as $key => $result) {
			foreach ($result as $prop => $value) {
				if(is_numeric($prop)) unset($data[$key][$prop]);
			}		
		}		
		
		if($this->instantiate){
			return $this->instantiate($data);
		}else{			
				return $data;
		}	
	}
	
	private  function instantiate($data){
		$class_name=get_called_class();
		$objects=array();
		
		foreach ($data as $key => $value) {		
			$object=new $class_name($this->table_name);
			$object->data=$value;
			//$object->table_name=$this->table_name;
			$objects[]=$object;
		}
		
		return $objects;
	}
	
	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(static::$data as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = self::$db->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
		return isset($this->data['id']) ? $this->update() : $this->create();
	}
	
	public function create() {
		
		$class_name=get_called_class();
		foreach($this->data as $key => $value){
			$this->data[$key]=Db::escape_value($this->data[$key]);	
		}

		$sql = "INSERT INTO ".$this->table_name." (";
		$sql .= join(", ", array_keys($this->data));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($this->data));
		$sql .= "')";

		if(Db::query($sql)) {
			$this->data['id'] = Db::insert_id();
			return $this->data['id'];
		} else {
			return false;
		}
	}

	public function update() {
		$class_name=get_called_class();
		foreach($this->data as $key => $value){
			$this->data[$key]=Db::escape_value($this->data[$key]);	
		}	
		$attribute_pairs = array();
		foreach($this->data as $key => $value) {
			$attribute_pairs[] = "{$key}='{$value}'";
		}		
		$sql = "UPDATE ".$this->table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". Db::escape_value($this->data['id']);
	  Db::query($sql);				
	 	return (Db::affected_rows() == 1) ? true : false;
	}

	public function delete() {		
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
	  $sql = "DELETE FROM ".$this->table_name;
	  $sql .= " WHERE id=". Db::escape_value($this->data['id']);
	  $sql .= " LIMIT 1";
	  Db::query($sql);
	  return (Db::affected_rows() == 1) ? true : false;
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}
	
	protected static function _delete(){
		
	}
	
	protected function buffer_contents($function) {
		$model=&$this->object;
		foreach ($GLOBALS as $key => $val) { global $$key; }
	      ob_start();
	      eval($function);
	      return ob_get_clean();
	}
	
	public static function count_all($condition=""){
		
		$sql = "SELECT COUNT(*) FROM ".$this->table_name." ".$condition;
		$result_set = self::$db->query($sql);
		$row=self::$db->fetch_array($result_set);
		return array_shift($row);
	}
	
	public static function reset_auto_increment(){			
		$sql="ALTER TABLE ".$this->table_name." AUTO_INCREMENT = 1";
		self::$db->query($sql);
	}
	
	public static function max_id(){		
		$sql = "SELECT MAX(id) FROM ".$this->table_name;
		$result_set = self::$db->fetch_array(self::$db->query($sql));
		return $result_set[0];
	}
}

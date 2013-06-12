<?php
include_once('./DBHelper.php');
include_once('./ConfigHelper.php');

class Collection extends DBHelper {

	private $id = null;
	private $path; 
	private $name;
	
	private $justCreated;
	private $config;
	
	
    public function __toString() {
        return $this->name;
    }
	
	public static function create() {
		$entity = new Collection();
		$entity->justCreated = true;
		return $entity;
	}
	
	public static function getById($id) {
		$sqlQuery = "SELECT * FROM collection ".
					"WHERE id = '$id'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		if ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
		}
		
		return $entity;
	}
	
	
	public static function getByPath($path) {
		$sqlQuery = "SELECT * FROM collection ".
					"WHERE path = '$path'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		if ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Collection();
			self::fillEntity($arr, $entity);
		}
		
		return $entity;
	}
	
	
	public static function getIdForPath($path) {
		$sqlQuery = "SELECT id FROM collection ".
					"WHERE path = '$path'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		$id = 0;
		if ($resultSet) {
			if ($row = mysql_fetch_assoc($resultSet)) {
				$id = $row['id'];
			}
		}
		
		return $id;
	}
	
	
	
	public static function count() {
		$sqlQuery = "SELECT count(*) as amount FROM collection";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		$count = 0;
		if ($arr = mysql_fetch_assoc($resultSet)){
			$count = $arr['amount'];
		}
		
		return $count;
	}
	
	
	public function save() {
		if ($this->justCreated && self::getIdForPath($this->path) == 0) {
			$sqlQuery = "INSERT INTO collection".
					 " (path, name)".
					 " VALUES".
					 " ('$this->path',".
					 " '$this->name')";
			self::getQueryResult($sqlQuery);
			$this->id = self::getIdForPath($this->path);
			$this->justCreated = false;
		} else {
			// to make sure that this file has an id (might be, if the file got rehashed und renamed but wasn't in the database before)
			if (!isset($this->id) || $this->id == 0) {
				$this->id = self::getIdForPath($this->path);
			}
			$sqlQuery = "UPDATE collection SET".
					 " path = '$this->path',".
					 " name = '$this->name'".
					 " WHERE".
					 " id = $this->id";
			self::getQueryResult($sqlQuery);
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	private static function fillEntity($arr, $entity) {
			$entity->id = $arr['id'];
			$entity->name = $arr['name'];
			$entity->path = $arr['path'];
			// Nach Wertbef�llung aus ResultSet kann es keine Neuanlage sein!
			$entity->justCreated = false;
	}
}
?>
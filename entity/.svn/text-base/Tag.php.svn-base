<?php
include_once('./DBHelper.php');
include_once('./ConfigHelper.php');

class Tag extends DBHelper {

	private $id=null;
	private $name; 
	private $weight;
	private $collection_id;
	
	private $justCreated;
	private $config;
	
	
    public function __toString() {
        return $this->filename;
    }
	
	public static function create() {
		$entity = new Tag();
		$entity->justCreated = true;
		return $entity;
	}
	
	public static function getById($id) {
		$sqlQuery = "SELECT * FROM tag ".
					"WHERE id = '$id'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		if ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
		}
		
		return $entity;
	}
	
	public static function getAll() {
		$sqlQuery = "SELECT * FROM tag ";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		while ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Tag();
			self::fillEntity($arr, $entity);
			$entities[]= $entity;
		}
		
		return $entities;
	}
	
	
	public static function getMaxWeight() {
		$result = -1;
		$sqlQuery = "SELECT max(weight) as max FROM tag";
	
		$resultSet = self::getQueryResult($sqlQuery);
		if ($arr = mysql_fetch_assoc($resultSet)){
			$result = $arr['max'];
		}
		return $result;
	}
	
	public function save() {
		if ($this->justCreated) {
			$sqlQuery = "INSERT INTO tag".
					 " (name,weight,collection_id)".
					 " VALUES".
					 " ('$this->name',".
					 " '$this->weight',".
					 " '$this->collection_id')";
			self::getQueryResult($sqlQuery);
			$this->justCreated = false;
		} else {
			$sqlQuery = "UPDATE tag SET".
					 " name = '$this->name',".
					 " weight = '$this->weight',".
					 " collection_id = '$this->collection_id',".
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
	
	public function getWeight() {
		return $this->weight;
	}
	
	public function setCollectionId($collection_id) {
		$this->collection_id = $collection_id;
	}
	
	private static function fillEntity($arr, $entity) {
			$entity->id = $arr['id'];
			$entity->name = $arr['name'];
			$entity->weight = $arr['weight'];
			$entity->collection_id = $arr['collection_id'];
			// Nach Wertbef�llung aus ResultSet kann es keine Neuanlage sein!
			$entity->justCreated = false;
	}
}
?>
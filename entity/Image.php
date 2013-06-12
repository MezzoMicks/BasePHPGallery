<?php
include_once('./DBHelper.php');
include_once('./ConfigHelper.php');

class Image extends DBHelper {

	private $id=null;
	private $filename; 
	private $filepath; 
	private $hash;
	private $comment;
	private $active;
	private $upload_date;
	private $collection_id;
	
	private $justCreated;
	private $config;
	
	
    public function __toString() {
        return $this->filename;
    }
	
	public static function create() {
		$entity = new Image();
		$entity->justCreated = true;
		return $entity;
	}
	
	public static function getById($id) {
		$sqlQuery = "SELECT * FROM image ".
					"WHERE id = '$id'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		if ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
		}
		
		return $entity;
	}
	
	
	public static function getByFilepath($filepath) {
		$sqlQuery = "SELECT * FROM image ".
					"WHERE filepath = '$filepath'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		if ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
		}
		
		return $entity;
	}
	
	
	public static function getSomeOrderByDate($collection, $offset, $limit) {
		$sqlQuery = "SELECT * FROM image".
					" WHERE collection_id = ".$collection.
					" ORDER BY upload_date ASC".
					" LIMIT ".$offset.", ".$limit.";";
		$resultSet = self::getQueryResult($sqlQuery);
		
		
		while ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
			$entities[]= $entity;
		}
		
		return $entities;
	}
	
	public static function count($collection) {
		$sqlQuery = "SELECT count(*) as amount FROM image WHERE collection_id = $collection";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		$count = 0;
		if ($arr = mysql_fetch_assoc($resultSet)){
			$count = $arr['amount'];
		}
		
		return $count;
	}
	
	public static function hashExists($hash) {
		$sqlQuery = "SELECT * FROM image ".
					"WHERE hash = '$hash'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		$numRows = 0;
		if ($resultSet) {
			$numRows = mysql_num_rows($resultSet);
		}
		return  $numRows;
	}
	
	public static function getIdForHash($hash) {
		$sqlQuery = "SELECT id FROM image ".
					"WHERE hash = '$hash'";
					
		$resultSet = self::getQueryResult($sqlQuery);
		
		$id = 0;
		if ($resultSet) {
			if ($row=mysql_fetch_assoc($resultSet)) {
				$id = $row['id'];
			}
		}
		
		return $id;
	}
	
	public static function getImagesByTag($tagId) {
		$query = "SELECT i.* FROM image as i, tagToImage as t".
				 " WHERE t.tag_id = $tagId".
				 " AND t.image_id = i.id";
				 
		$resultSet = self::getQueryResult($query);
			
		while ($arr = mysql_fetch_assoc($resultSet)){
			$entity = new Image();
			self::fillEntity($arr, $entity);
			$entities[]= $entity;
		}
		
		return $entities;
	}
	
	public function save() {
		if ($this->justCreated && self::getIdForHash($this->hash) == 0) {
			$currentDate = date("Y-m-d H:i:s");
			$sqlQuery = "INSERT INTO image".
					 " (upload_date,filename,filepath,thumbpath,hash,comment,active,collection_id)".
					 " VALUES".
					 " ('$currentDate',".
					 " '$this->filename',".
					 " '$this->filepath',".
					 " '$this->thumbpath',".
					 " '$this->hash',".
					 " '$this->comment',".
					 " '$this->active',".
					 " '$this->collection_id')";
			self::getQueryResult($sqlQuery);
			$this->id = self::getIdForHash($this->hash);
			$this->justCreated = false;
		} else {
			// to make sure that this file has an id (might be, if the file got rehashed und renamed but wasn't in the database before)
			if (!isset($this->id) || $this->id == 0) {
				$this->id = self::getIdForHash($this->hash);
			}
			$sqlQuery = "UPDATE image SET".
					 " filename = '$this->filename',".
					 " filepath = '$this->filepath',".
					 " thumbpath = '$this->thumbpath',".
					 " hash = '$this->hash',".
					 " comment = '$this->comment',".
					 " active = '$this->active'".
					 " WHERE".
					 " id = $this->id";
			self::getQueryResult($sqlQuery);
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getFilename() {
		return $this->filename;
	}
	
	public function getFilepath() {
		return $this->filepath;
	}
	
	public function getThumbpath() {
		if (!isset($this->thumbpath) || $this->thumbpath == "") {
			if (!isset($this->config)) {
				$this->config = new ConfigHelper();
			}
			$this->thumbpath = FileHelper::createThumbpath($this->filepath, $this->config->getThumbSubDir(), $this->config->getThumbFileAttach(), $this->config->getThumbFileStyle());
			$this->save();
		}
		return $this->thumbpath;
	}
	
	public function getHash() {
		return $this->hash;
	}
	
	public function getComment() {
		return $this->comment;
	}

	public function getUploadDate() {
		return $this->upload_date;
	}
	
	public function setUploadDate($upload_date) {
		$this->upload_date = $upload_date;
	}
	
	public function setFilepath($filepath) {
		// Dateinamen aus Pfad extrahieren
		$name = strrchr($filepath  , "/" );
		if (!$name) {
			$this->filename = $name;
		} else {
			$this->filename = substr($name,1); 
		}
		
		// Hash bilden
		$this->hash = hash_file("md5", $filepath);
		
		$this->filepath = $filepath;
	}
	
	public function setThumbpath($thumbpath) {
		$this->thumbpath = $thumbpath;
	}
	
	public function setComment($comment) {
		$this->comment = $comment;
	}
	
	public function setActive($active) {
		$this->active = $active;
	}
	
	public function setCollectionId($collection_id) {
		$this->collection_id = $collection_id;
	}
	
	private static function fillEntity($arr, $entity) {
			$entity->id = $arr['id'];
			$entity->upload_date = $arr['upload_date'];
			$entity->filename = $arr['filename'];
			$entity->filepath = $arr['filepath'];
			$entity->thumbpath = $arr['thumbpath'];
			$entity->hash = $arr['hash'];
			$entity->comment = $arr['comment'];
			$entity->active = $arr['active'];
			$entity->collection_id = $arr['collection_id'];
			// Nach Wertbef�llung aus ResultSet kann es keine Neuanlage sein!
			$entity->justCreated = false;
	}
}
?>
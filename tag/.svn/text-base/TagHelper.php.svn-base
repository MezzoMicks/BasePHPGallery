<?php

include_once('./DBHelper.php');

class TagHelper {
	
	public static function getTopTen() {
		$query = "SELECT name FROM tag ORDER BY weight DESC LIMIT 10";
		$resultSet = DBHelper::getQueryResult($query);
		if ($resultSet) {
			while ($arr = mysql_fetch_assoc($resultSet)){
				$tags[]= $arr['name'];
			}
		}
		return $tags;
	}
	
	public static function getTagsForImage($image_id) {
		$query = "SELECT tag.name FROM tag, tagToImage ".
				 " WHERE tagToImage.image_id = ".$image_id.
				 " AND tagToImage.tag_id = tag.id";
		$resultSet = DBHelper::getQueryResult($query);
		if ($resultSet) {
			while ($arr = mysql_fetch_assoc($resultSet)){
				$tags[]= $arr['name'];
			}
		}
		return $tags;
	}
	
	public static function addTag($tag) {
		$id = 0; //Standardmäßig 0 zum abfangen von Fehlern...
		
		$tag = trim($tag); // Von leerzeichen bereinigen
		
		if ($tag != "") {
			// query basteln
			$selectQuery = "SELECT id FROM tag".
					 " WHERE name LIKE '$tag'";
			// Statement absetzen
			$resultSet = DBHelper::getQueryResult($selectQuery);
			if (!$resultSet || mysql_num_rows($resultSet) == 0) {
				$insertQuery = "INSERT INTO tag".
							   " (name,weight)".
							   " VALUES ('$tag', 0)";
				DBHelper::executeQuery($insertQuery);
				$resultSet = DBHelper::getQueryResult($selectQuery);
			}
			// ID auslesen
			if ($arr = mysql_fetch_assoc($resultSet)){
				$id = $arr['id'];
			}
		}
		return $id;
	}
	
	public static function addTagToImage($tag, $imageId) {
		$tagId = self::addTag($tag);
		if ($tagId != 0) {
			$query = "SELECT id FROM tagToImage".
					 " WHERE tag_id = $tagId".
					 " AND image_id = $imageId";
					 
			$resultSet = DBHelper::getQueryResult($query);
			
			if (!$resultSet || mysql_num_rows($resultSet) == 0) {
				$query = "INSERT INTO tagToImage".
							   " (tag_id, image_id)".
							   " VALUES ($tagId, $imageId)";
				DBHelper::executeQuery($query);
			}
		}
	}
	
	
	public static function cleanTags($delimiter) {
		$query = "SELECT id, name FROM tag".
				 " WHERE name like '%$delimiter[0]%'";
		$i = 1;
		while (isset($delimiter[$i])) {
			$query .= " OR name like '%".$delimiter[$i++]."%'";
		}
				 
		$resultSet = DBHelper::getQueryResult($query);
		if ($resultSet && mysql_num_rows($resultSet) > 0) {
			while ($arr = mysql_fetch_assoc($resultSet)){
				$relatedImages = Image::getImagesByTag($arr['id']);
				
				$dirtyTag = $arr['name'];
				
				foreach($delimiter as $token) {
					$dirtyTag = str_replace($token, ";", $dirtyTag);
				}
				
				// Nun über die Semikolons splitten
				foreach(split(";", $dirtyTag) as $newTag) {
					// Zugewiesene Bilder neu mit diesem Tag verbinden
					foreach ($relatedImages as $image) {
						self::addTagToImage($newTag, $image->getId());
					}
				}
				
				// nun kann der alte datensatz weg
				$deleteQuery = "DELETE FROM tag WHERE id = ".$arr['id'].";";
				
				DBHelper::executeQuery($deleteQuery);
			}
		}
	}
	
	public static function refreshTagweight() {
		// Lese alle Tag-IDs aus
		$query = "SELECT id FROM tag";
		$resultSet = DBHelper::getQueryResult($query);
		if ($resultSet && mysql_num_rows($resultSet) > 0) {
			while ($arr = mysql_fetch_array($resultSet)){
				echo ($arr[0]);
				$id = $arr[0];
				
				// Zähle die Zuweisungen
				$query = "SELECT count(*) FROM tagToImage WHERE tag_id = ".$id;
				$subResultSet = DBHelper::getQueryResult($query);
				
				if ($subResultSet && mysql_num_rows($subResultSet) > 0) {
					if ($arr = mysql_fetch_array($subResultSet)) {
						// Update auf das weight
						$updateQuery = "UPDATE tag SET weight=".$arr[0]." WHERE id=".$id;
						DBHelper::executeQuery($updateQuery);
					}
				}
			}
		}
		
		mysql_free_result($resultSet);
	}
}
?>
<?php

include_once('../DBHelper.php');
include_once('./TagHelper.php');
include_once('./ViewHelper.php');
include_once('./entity/Image.php');
include_once('./ErrorHandler.php');

class UploadHelper {

	private $errorMessage = "";
	
	public static function evaluate() {
		$action = "newfile";
		if (isset($_POST['action'])) {
			$action = $_POST['action'];
		}
		
		if ($action == "upload") {
			if (!self::isValid($_FILES['file'])){
				echo ("Fehler beim Upload");
				self::drawButtonUpload();
			} else {
				$filename = $_FILES['file']['name'];
				$tmpPath = "./tmp/".$filename;
				move_uploaded_file ($_FILES['file']['tmp_name'], $tmpPath);
			
				$size = getimagesize($tmpPath);
				// get original sizes
				$width = $size[0];
				$height = $size[1];
			
				if ($width > $height) {
					$newWidth = 500;
					$newHeight = intval(($newWidth * $height) / $width);
				} else {
					$newHeight = 500;
					$newWidth = intval(($newHeight * $width) / $height);
				}
				
				echo ("<table>\n");
				echo ("	<tr>\n");
				echo ("	 <td width=\"500\">\n");
				echo ("	  Vorschau");
				echo ("	 </td>\n");
				echo ("	 <td>\n");
				echo ("   &nbsp;");
				echo ("	 </td>\n");
				echo ("	</tr>\n");
				echo ("	<tr>\n");
				echo ("	 <td>\n");
				echo ("	  <img style=\"float:left\" src=\"./tmp/".$filename."\" width=\"".$newWidth."\" height=\"".$newHeight."\"/>\n");
				echo ("	 </td>\n");
				echo ("	 <td>\n");
				echo ("   &nbsp;");
				echo ("	 </td>\n");
				echo ("	</tr>\n");
				echo ("</table>\n");
				echo ("	");
				echo ("<br/>\n");
				
				self::drawButtonSave($tmpPath, $filename);
				
				self::drawButtonAbort();
			}
		} else if ($action == "save") {
			$tmpPath = "tmpPath";
			if (isset($_POST['tmpPath']) && isset($_POST['fileName'])) {
				$tmpPath = $_POST['tmpPath'];
				$fileName = $_POST['fileName'];
				$filePath = "./".$fileName;
				$moved = rename($tmpPath, $filePath);
				if ($moved) {
					$config = new ConfigHelper();
				
					$hash = hash_file("md5", $filePath);
					if (!Image::hashExists($hash)) {
						$imageEntity = Image::create();
						$imageEntity->setFilepath($filePath);
						$imageEntity->setActive(true);
						$imageEntity->setCollectionId($config->getCurrentCollection());
						$imageEntity->save();
						echo ("Bild gespeichert<br/>");
						
						if (isset($_POST['thumb']) && $_POST['thumb']) {
							FileHelper::createThumbnail($image, $config);
							echo ("Thumbnail gespeichert<br/>");
						}
						
						FileHelper::buildImageHash($imageEntity, true, $config);
						
					} else {
						// Aktualisieren? w�re ja vielleicht sinnvoll
					}
					if (isset($_POST['tags'])) {
						$tags = split( "," , $_POST['tags']);
						foreach($tags as $tag) {
							if (trim($tag) != "") {
								TagHelper::addTagToImage($tag, $imageEntity->getId());
							}
						}
						echo ("Tags gespeichert<br/>");
					}
				}
			}
		
			self::drawButtonUpload();
		} else {
			self::drawButtonUpload();
		}
	}
	
	private static function drawButtonUpload() {
		echo ("		<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		echo ("		 <input type=\"file\" name=\"file\" />\n");
		echo ("		 <input type=\"hidden\" name=\"action\" value=\"upload\" />\n");
		echo ("      <button type=\"submit\">Upload</button>\n");
		echo ("		</form>\n");
	}
	
	private static function drawTagString() {
		$tags = TagHelper::getTopTen();
		if ($tags) {
			foreach ($tags as $tag) {
				echo $tag." ";
			}
		}
	}
	
	private static function drawButtonSave($tmpPath, $filename) {
			echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
			echo ("		 <b>Meist genutzte Tags</b><br/>\n");
			flush();
			echo (self::drawTagString()."<br/><br/>");
			echo ("		 Tags bitte durch , (Komma) getrennt eingeben<br/>\n");
			echo ("		 Tags sind kurze Bezeichnungen die den Inhalt des Bildes widerspiegeln<br/>\n");
			echo ("		 <input type=\"text\" name=\"tags\" /><br/>\n");
			//echo ("		 <input type=\"checkbox\" name=\"thumb\" value=\"true\"> Thumbnail generieren<br>");
			echo ("		 <input type=\"hidden\" name=\"action\" value=\"save\" />\n");
			echo ("		 <input type=\"hidden\" name=\"tmpPath\" value=\"".$tmpPath."\" />\n");
			echo ("		 <input type=\"hidden\" name=\"fileName\" value=\"".$filename."\" />\n");
			echo ("      <button type=\"submit\">Speichern</button>\n");
			echo ("		</form>\n");
	}
	
	private static function drawButtonAbort() {
			echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
			echo ("		 <input type=\"hidden\" name=\"action\" value=\"newfile\" />\n");
			echo ("      <button type=\"submit\">Abbrechen</button>\n");
			echo ("		</form>\n");
	}
	
	private static function isValid($file) {
		$returnValue = true;
		if (!isset($file)) {
			$errorMessage = "nichts gefunden... o.O";
			$returnValue = false;
		} else {
			$type = $file['type'];  
			$tempname = $file['tmp_name']; 
			$name = $file['name'];
			if($type != "image/gif" && $type != "image/jpeg" && $type != "image/pjpeg" && $type != "image/png") { 
				ErrorHandler::outputError(402);
				$returnValue = false;
			} else if (Image::hashExists(hash_file("md5", $tempname))) {
				ErrorHandler::outputError(401);
				$returnValue = false;
			}
		}
		return $returnValue;
	}
}
?>
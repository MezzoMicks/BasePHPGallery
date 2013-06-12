<?php

include_once('./entity/Image.php');
include_once('./FileHelper.php');
include_once('./ConfigHelper.php');
include_once('./TagHelper.php');

class Utilities {

	public static function evaluate(){
		$action = "none";
		if (isset($_POST['action'])) {
			$action = $_POST['action'];
		}
		
		if ($action == 'refresh' && isset($_POST['dir'])) {
			$dir = $_POST['dir'];
			self::dirToDB($dir);
		} else if ($action == 'repairTags') {
			$config = new ConfigHelper();
			TagHelper::cleanTags($config->getDelimiter());
		} else if ($action == 'refreshTagweight') {
			TagHelper::refreshTagweight();
		} else if ($action == 'hashAndRename') {
			if (isset($_POST['dbSupport'])) {
				$dbSupport = $_POST['dbSupport'];
			} else {
				$dbSupport = false;
			}
			FileHelper::hashAndRename($dbSupport, new ConfigHelper());
		} else {
			self::drawRefreshButton();
			self::drawRepairButton();
			self::drawRevaluateButton();
			self::drawHashAndRenameButton();
		}
	}

	public static function drawRefreshButton() {
		echo ("		<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		echo ("		 <input type=\"text\" name=\"dir\" />\n");
		echo ("		 <input type=\"hidden\" name=\"action\" value=\"refresh\" />\n");
		echo ("      <button type=\"submit\">Refresh</button>\n");
		echo ("		</form>\n<br/>\n");
	}
	

	public static function drawRepairButton() {
		echo ("		<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		echo ("		 <input type=\"hidden\" name=\"action\" value=\"repairTags\" />\n");
		echo ("      <button type=\"submit\">Repair Tags</button>\n");
		echo ("		</form>\n<br/>\n");
	}
	

	public static function drawRevaluateButton() {
		echo ("		<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		echo ("		 <input type=\"hidden\" name=\"action\" value=\"refreshTagweight\" />\n");
		echo ("      <button type=\"submit\">Refresh Tagweight</button>\n");
		echo ("		</form>\n<br/>\n");
	}
	
	

	public static function drawHashAndRenameButton() {
		echo ("		<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n");
		echo ("		 <input type=\"hidden\" name=\"action\" value=\"hashAndRename\" />\n");
		echo ("      <button type=\"submit\">Refresh Tagweight</button><br/>\n");
		echo ("		 <input type=\"checkbox\" name=\"dbSupport\" value=\"true\"/>In DB einspielen<br/>\n(falls noch nicht geschehen)\n");
		echo ("		</form>\n<br/>\n");
	}
	
	public static function dirToDB($dir) {
		$extensions []= "jpg";
		$extensions []= "png";
		$extensions []= "gif";
		$extensions []= "jpeg";
		$config = new ConfigHelper();
		$files = FileHelper::getFilesFromDir($dir, $config);
		if ($files) {
			foreach($files as $file) {
				if (substr($dir,-1,1) != "/") {
					$dir .= "/";
				}
				if (!Image::getByFilepath($dir.$file)) {
					echo $file." saved<br/>";
					$imageEntity = Image::create();
					$imageEntity->setFilepath($dir.$file);
					$imageEntity->setActive(true);
					$imageEntity->setCollectionId($config->getCurrentCollection());
					$imageEntity->save();
				}
			}
		}
	}
	
}
?>
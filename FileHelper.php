<?php

include_once('./entity/Image.php');
include_once('./ConfigHelper.php');
include_once('./ErrorHandler.php');

class FileHelper {

	public static function getFilesFromDir($path, ConfigHelper $config) {
		// check if $activeDir exists
		if (is_dir($path)) {
			$dir = opendir($path);
			// while there are still Files to read
			while ($temp = readdir($dir)) {
				// check if the pointed file is a .jpg
				$ext = substr($temp, -4, 4);
				if (self::isValid($temp, $config->getExtensions())) {
					$filepath = str_replace("//", "/", $path."/".$temp);
					$image = Image::create();
					$image->setFilepath($filepath);
					$image->setUploadDate(filemtime($filepath));
					$image->setThumbpath(self::createThumbpath($filepath, $config->getThumbSubDir(), $config->getThumbFileAttach(), $config->getThumbFileStyle()));
					
					$files[]= $image;
				}
			}
			// close directory handler
			closedir($dir);
		}
		return $files;
	}
	
	public static function isValid($file, $extensions) {
		$valid = false;
		if ($file != '.' && $file != '..') {
			$fileExt = substr(strrchr  ($file, "."), 1);
			foreach($extensions as $ext) {
				if ($ext == $fileExt) {
					$valid = true;
					break;
				}
			}
		}
		return $valid;
	}
	
	
	public static function createThumbpath($filepath, $thumbdir, $thumbattach, $thumbfilestyle) {
		$filename = strrchr($filepath, "/");
		$filedir = substr($filepath, 0, strlen($filepath) - strlen($filename));
		$filename = substr($filename, 1);
		$path = $filedir . '/' . $thumbdir;
		if (substr($path, -1, 1) == "/") {
			$path .= substr($path, 0, strlen($path) - 1);
		}
		
		
		if (!is_dir($path)) {
			if (is_file($path)) {
					ErrorHandler::outputError(302);
			} else {
				if(!mkdir($path)) {
					ErrorHandler::outputError(901);
				}
			}
		} 
		
		switch ($thumbfilestyle) {
			case ConfigHelper::THUMB_PREFIX:
				$path .= '/' . $thumbattach . $filename;
				break;
			case ConfigHelper::THUMB_SUFFIX:
				$ext = strrchr($filename, ".");
				$simple = substr($filename, 0, strlen($filename) - strlen($ext));
				$path .= '/' .  $simple . $thumbattach . $ext;
				break;
			case ConfigHelper::THUMB_PLAIN:
				$path .= '/' .  $filename;
				break;
		}
		
		return $path;
	}
	
	public static function createThumbnail($image, $config) {
		$sourceFile = $image->getFilepath();
		$targetFile = $image->getThumbpath();
		$thumbSize = (file_exists($targetFile)) ? getimagesize($targetFile) : 0;
		$canvasSize = $config->getCanvasSize();
		if (!file_exists($targetFile) || $thumbSize[0] != $canvasSize[0] || $thumbSize[1] != $canvasSize[1]) {
			$canvasWidth = $canvasSize[0];
			$canvasHeight = $canvasSize[1];
			$size = getimagesize($sourceFile);
			// get original sizes
			$width = $size[0];
			$height = $size[1];
			// get new calculated sizes and startpoints
			$newSizes = self::calculatePaintSize($width, $height, $canvasWidth, $canvasHeight);
			// create image from original file
			$ext = substr($sourceFile, -4, 4);
			if ($ext == '.jpeg' || $ext == '.jpg') {
				$origImage = ImageCreateFromJPEG($sourceFile);
			} else if ($ext == '.png') {
				$origImage = ImageCreateFromPNG($sourceFile);
			} else if ($ext == '.gif') {
				$origImage = ImageCreateFromGIF($sourceFile);
			}
			
			// create new image
			$thumbImage = imagecreatetruecolor($canvasWidth, $canvasHeight);
			// copy original image into the image
			imagecopyresampled($thumbImage, $origImage, $newSizes[2], $newSizes[3], 0, 0, $newSizes[0], $newSizes[1], $width, $height);
			// create imagefile
			ImageJPEG($thumbImage, $targetFile);
		}
		return $targetFile;
	}
	
	public static function hashAndRename($dbSupport, $config) {
		$images = self::getFilesFromDir('./', $config);
		$filecount = 0;
		foreach($images as $image) {
			if (!self::buildImageHash($image, $dbSupport, $config)) {
				ErrorHandler::outputError(410, "Operation stopped!");
				break;
			} else if ($newPath != $image->getFilePath) {
				$filecount++;
			}
		}
		echo($filecount.' files were update');
	}
	
	public static function buildImageHash($image, $dbSupport, $config) {
		$hash = hash_file("md5", $image->getFilepath());
		$newPath = self::renameFile($image->getFilepath(), $hash);
		if (!$newPath) {
			return false;
		} else {
			rename($image->getFilepath(), $newPath);
			$image->setFilepath($newPath);
			$newThumbpath = self::createThumbpath($newPath, $config->getThumbSubDir(), $config->getThumbFileAttach(), $config->getThumbFileStyle());
			rename($image->getThumbpath(), $newThumbpath);
			$image->setThumbpath($newThumbpath);
			// save to DB
			if ($dbSupport) {
				$image->save();
			}
			return true;
		}
	}

	public static function calculatePaintSize($width, $height, $canvasWidth, $canvasHeight) {
		if ($width > $height) {
			$newWidth = $canvasWidth;
			$newHeight = intval(($newWidth * $height) / $width);

			$newPosX = 0;
			$newPosY = intval(($canvasHeight - $newHeight) / 2);
		} else {
			$newHeight = $canvasHeight;
			$newWidth = intval(($newHeight * $width) / $height);

			$newPosX = intval(($canvasWidth - $newWidth) / 2);
			$newPosY = 0;
		}

		$newSizes[0] = $newWidth;
		$newSizes[1] = $newHeight;
		$newSizes[2] = $newPosX;
		$newSizes[3] = $newPosY;

		return $newSizes;
	}
	
	private static function renameFile($filePath, $newBaseName) {
		$newPath = false;
	
		$slashPos = strrpos($filePath, '/');
		if (is_bool($slashPos) && !$slashPos) {
			$slashPos = strrpos($filePath, '\\');
		}
		$dotPos = strrpos($filePath, '.');
		
		if (is_bool($slashPos) && !$slashPos) {
			ErrorHandler::outputError(411, "no slash found");
		} else if (is_bool($dotPos) && !$dotPos) {
			ErrorHandler::outputError(411, "no dot found");
		} else {
			$oldDir = substr($filePath, 0, $slashPos);
			$oldExt = substr($filePath, $dotPos);
			$newName = $newBaseName.$oldExt;
			$newPath = $oldDir.'/'.$newName;
		}
		echo $newPath;
		return $newPath;
	}
}
?>
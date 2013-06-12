<?php

/*
 * Author : Michael Feige
 *  
 * File ImageHelper.php created on 10.02.2008
 * 
 * for more information check out
 * www.mickszone.de
 *
 * copyright 2008
 * 
 * TODO Test/Debug
 * 	- testen ob übergaben funktionieren
 * 	- ordner erstellen
 *	- check getLastPage....
 * TODO Features
 * 	- ! preload
 * 	- ! Installer - damit die Permissions beim apache-user liegen
 * 		sodass keine permission probleme entstehen beim schreiben
 * 	- ? hover-effekte
 * 	- ? JAVA-Utility
 * Kapselung in andere Klassen... ? Schnittstellenlogik?
 *  - navigations ankopplung
 * 	- kommentar-funktion
 */
  
/* 
 * $Log: ViewHelper.php,v $
 * Revision 1.6  2008-07-02 21:18:23  michi
 * 	 - configurable css-styles completely implemented (and tested)
 * 	 - require_once for ErrorHandler included, so that it is now the only REALLY required file thats needed 100%
 *
 * Revision 1.5  2008-06-25 21:00:37  michi
 * New getter-methods getRows and getCols
 *
 */
include_once('./FileHelper.php');
include_once('./ErrorHandler.php');
include_once('./ConfigHelper.php');
include_once('./TagHelper.php');

class ViewHelper {
	
	const SORT_NONE = 0;
	const SORT_NAME_ASC = 10;
	const SORT_NAME_DESC = 11;
	const SORT_DATE_ASC = 20;
	const SORT_DATE_DESC = 21;
	
	protected $objectArray;

	private $files;
	private $loaded = false;
	
	private $activeDir = './';
	
	private $picsPerPage;
	private $offset = 0;
	private $page = 0;
	
	private $config;
	
	private $maxFiles = 0;
	
	private $hasMore = false;
	
	
	public function ViewHelper (ConfigHelper $configHelper) {
		$this->config = $configHelper;
		
		$this->picsPerPage = $this->config->getRows() * $this->config->getCols();
	}

	/**
	*	Sets the displayed page
	*/
	public function setPage($page) {
		if (is_int($page) && $page >= 0) {
			$this->page = $page;
		} else if (is_int($page) && $page == -1) {
			$this->getActiveDirFiles();
			$this->page = floor($this->maxFiles / $this->picsPerPage) - 1;
			$this->loaded = false;
		} else {
			ErrorHandler::outputError(213);
		}
		$this->offset = $this->page * $this->picsPerPage;
	}

	/**
	*	Gets the displayed page
	*/
	public function getPage() {
		return $this->page;
	}

	/**
	*	Gets the displayed page
	*/
	public function getHasMore() {
		$this->getActiveDirFiles();
		return $this->hasMore;
	}
	
	/**
	*	Outputs the HTML-Code to display the gallery
	*/
	public function drawHTMLTable() {
		echo ("<table ".$this->config->getCSSViewTable().">\n");
		echo ("  <tr ".$this->config->getCSSViewTr().">\n");
		if (isset($_GET['id']) && $_GET['id'] > 0) {
			$id = $_GET['id'];
			$image = Image::getById($id);
			$size = getimagesize($image->getFilepath());
			// get original sizes
			$width = $size[0];
			$height = $size[1];
			$newSize = FileHelper::calculatePaintSize($width, $height, 500, 375);
			echo ("    <td>\n");
			echo ("      <a href=\"" . $image->getFilepath() . "\">\n");
			echo ("        <img src=\"./" . $image->getFilepath() . "\" width=\"".$newSize[0]."\" height=\"".$newSize[1]."\" >\n");
			echo ("      </a>\n");
			echo ("    </td>\n");
			echo ("  </tr>\n");
			echo ("  <tr>\n");
			echo ("    <td>\n");
			$tags = TagHelper::getTagsForImage($image->getId());
			if ($tags) {
				foreach ($tags as $tag) {
					echo ($tag." ");
				}
			} else {
				echo ("Keine Tags hinterlegt");
			}
			echo ("    </td>\n");
		
		} else {
			$this->picsPerPage = $this->config->getRows() * $this->config->getCols();
			$counter = 0;
			// get Image Files
			$this->getActiveDirFiles();
			$cols = $this->config->getCols();
			if ($this->maxFiles > 0) {
				foreach ($this->files as $image) {
					$filepath = $image->getFilepath();
					$thumbpath = FileHelper::createThumbnail($image, $this->config);
					if ($counter != 0 && ($counter % $cols) == 0) {
						echo ("  </tr>\n");
						echo ("  <tr ".$this->config->getCSSViewTr().">\n");
					}
					echo ("    <td ".$this->config->getCSSViewTd().">\n");
					switch ($this->config->getDataSource()) {
						case ConfigHelper::DATA_SOURCE_DIR:
							echo ("      <a href=\"" . $filepath . "\"><img src=\"./" . $thumbpath . "\"></a>\n");
						break;
						case ConfigHelper::DATA_SOURCE_SQL:
							echo ("      <a href=\"" . $_SERVER['PHP'] . "?id=" . $image->getId() . "\"><img src=\"./" . $thumbpath . "\"></a>\n");
						break;
					}
					echo ("    </td>\n");
					
					$counter++;
				}
			}
		}
		echo ("  </tr>\n");
		echo ("</table>\n");
	}

	private function getActiveDirFiles() {
		if (!$this->loaded) {
			$maxFiles = 0;
			switch($this->config->getDataSource()) {
				case ConfigHelper::DATA_SOURCE_DIR:
					$files = FileHelper::getFilesFromDir($this->activeDir, $this->config);
					$files = $this->sortArray($files);
					$maxFiles = count($files);
					$files = array_slice($files, $this->offset, $this->picsPerPage + 1);
				    break;
				case ConfigHelper::DATA_SOURCE_SQL:
					$maxFiles = Image::count($this->config->getCurrentCollection());
				    $files = Image::getSomeOrderByDate($this->config->getCurrentCollection(), $this->offset, $this->picsPerPage + 1);
				    break;
			}
			$this->maxFiles = $maxFiles;
			$this->hasMore = count($files) > $this->picsPerPage;
			if ($maxFiles > 0) {
				$files = array_slice($files, 0, $this->picsPerPage);
			}
			$this->loaded = true;
			
			$this->files = $files;
		}
	}
	
	public function checkSubDirs() {
		// check if $activeDir exists
		if (is_dir('./' . $this->activeDir)) {
			// open directory handler
			$dir = opendir('./' . $this->activeDir);
			// while there are still Files to read
			while ($temp = readdir($dir)) {
				// check if the pointed file is a folder
				if (is_dir($temp) && $temp != $this->thumbDir) {
					$folders[]= $temp;
				}
			}
		}
		return $folders;
	}
	
	private function sortArray($array) {
		switch ($this->config->getSortType()) {
			case self::SORT_NAME_ASC:
				asort($array);
				break;
			case self::SORT_NAME_DESC:
				arsort($array);
				break;
			case self::SORT_DATE_ASC:
				$this->objectArray = $array;
				uksort($this->objectArray, array($this, "_compareFilesByDateASC"));
				$array = $this->objectArray;
				break;
			case self::SORT_DATE_DESC:
				$this->objectArray = $array;
				uksort($this->objectArray, array($this, "_compareFilesByDateDESC"));
				$array = $this->objectArray;
				break;
		}
		return $array;
	}
	
	private function _compareFilesByDateASC($a, $b) {
		//return filemtime($this->objectArray[$a]->getUploadDate()) - filemtime($this->objectArray[$b]->getUploadDate());
		return $this->objectArray[$a]->getUploadDate() - $this->objectArray[$b]->getUploadDate();
	}
	
	private function _compareFilesByDateDESC($a, $b) {
		//return filemtime($this->objectArray[$b]->getUploadDate()) - filemtime($this->objectArray[$a]->getUploadDate());
		return $this->objectArray[$b]->getUploadDate() - $this->objectArray[$a]->getUploadDate();
	}

}
?>

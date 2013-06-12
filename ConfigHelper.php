<?php
/* 
 * $Log: ConfigHelper.php,v $
 */
 
include_once('./ErrorHandler.php');
include_once('./entity/Collection.php');

class ConfigHelper {
	
	const SORT_NONE = 0;
	const SORT_NAME_ASC = 10;
	const SORT_NAME_DESC = 11;
	const SORT_DATE_ASC = 20;
	const SORT_DATE_DESC = 21;
	
	const DATA_SOURCE_DIR = 1;
	const DATA_SOURCE_SQL = 2;
	
	const THUMB_PLAIN = 0;
	const THUMB_PREFIX = 1;
	const THUMB_SUFFIX = 2;
	
	private $thumbSubDir = 'thumbs';
	private $thumbFileStyle = 1;
	private $thumbFileAttach = "th_";
	
	private $canvasWidth = 160;
	private $canvasHeight = 120;

	private $cssViewTableId = "style=\"100%\"";
	private $cssViewTdId = "";
	private $cssViewTrId = "style=\"100%\"";

	private $viewSortType = 0;
	private $viewRows = -1;
	private $viewCols = 4;
	
	private $sortType = self::SORT_DATE_ASC;
	
	private $dataSource = self::DATA_SOURCE_SQL;
	private $delimiter;
	
	private $extensions;
	
	private $currentColletion;
	
	
	public function ConfigHelper() {
		$this->extensions []= "jpg";
		$this->extensions []= "png";
		$this->extensions []= "gif";
		$this->extensions []= "jpeg";
		
		
		$this->delimiter []= ",";
		$this->delimiter []= ";";
		$this->delimiter []= "|";
		$this->delimiter []= "+";
		$currentPath = getcwd();
		$collection_id = Collection::getIdForPath($currentPath);
		if ($collection_id == 0) {
			$collection = Collection::create();
			$collection->setPath($currentPath);
			$collection->save();
			$collection_id = $collection->getId();
		}
		$this->currentCollection = $collection_id;
	}
	
	/**
	*	Sets the canvas-Size of a thumbnail.
	*/
	public function setCanvasSize($width, $height) {
		if (is_int($width) && $width > 0 && is_int($height) && $height > 0) {
			$this->canvasWidth = $width;
			$this->canvasHeight = $height;
		} else {
			ErrorHandler::outputError(210);
		}
	}
	
	/**
	*	Sets the canvas-Size of a thumbnail.
	*/
	public function getCanvasSize() {
		$canvassize[]= $this->canvasWidth;
		$canvassize[]= $this->canvasHeight;
		return $canvassize;
	}


	/**
	*	Sets the displayed columns
	*/
	public function setCols($cols) {
		if (is_int($cols) && $cols >= 0) {
			$this->viewCols = $cols;
		} else {
			ErrorHandler::outputError(211);
		}
	}
	
	public function getCols() {
		return $this->viewCols;
	}

	/**
	*	Sets the displayed columns
	*/
	public function setSortType($sortType) {
		if (is_int($sortType) && $sortType >= 0) {
			$this->sortType = $sortType;
		} else {
			//ErrorHandler::getErrorString(211);
		}
	}
	
	/**
	*	Returns the displayed columns
	*/
	public function getSortType() {
		return $this->sortType;
	}

	/**
	*	Sets the displayed rows
	*/
	public function setRows($rows) {
		if (is_int($rows) && $rows >= 0) {
			$this->viewRows = $rows;
		} else {
			ErrorHandler::outputError(212);
		}
	}
	
	public function getRows() {
		return $this->viewRows;
	}
	
	public function getExtensions() {
		return $this->extensions;
	}

	/**
	 * returns TRUE if there are more pages to show
	 */
	public function getHasMore() {
		$this->getActiveDirFiles();
		settype($this->hasMore, "bool");
		return $this->hasMore;
	}
	
	public function getActiveDir() {
		settype($this->activeDir, "String");
		return $this->activeDir;
	}
	
	
	public function getDelimiter() {
		return $this->delimiter;
	}
	
	/**
	*	Sets the displayed columns
	*/
	public function setDataSource($dataSource) {
		if (is_int($dataSource) && $dataSource >= 0) {
			$this->dataSource = $dataSource;
		} else {
			//ErrorHandler::getErrorString(211);
		}
	}
	
	/**
	*	Sets the displayed columns
	*/
	public function getDataSource() {
		return $this->dataSource;
	}
	
	/**
	*	Sets the css-Ids for the table and it's elements
	*/
	public function setCSSIds($tableId, $tdId, $trId) {
		if ($tableId != null) {
			$this->cssViewTableId = "id=\"".$tableId."\"";
		}
		if ($tdId != null) {
			$this->cssViewTdId = "id=\"".$tdId."\"";
		}
		if ($trId != null) {
			$this->cssViewTrId = "id=\"".$trId."\"";
		}
	}
	
	public function getCSSViewTable() {
		return $this->cssViewTableId;
	}
	
	public function getCSSViewTd() {
		return $this->cssViewTdId;
	}
	
	public function getCSSViewTr() {
		return $this->cssViewTrId;
	}
	
	/**
	 * Sets the active directory
	 */
	public function setActiveDir($activeDir) {
		if (!is_dir($activeDir)) {
			ErrorHandler::outputError(220);
		}
	}
	
	/**
	*	Sets the way in which thumbnails are saved
	*/
	public function setThumbnailProperties($thumbDir, $fileStyle, $fileAttach) {
		// If either the fileattach or the style is 'plain' set the countrpart
		if ($fileStyle == THUMB_PLAIN) {
			$fileAttach = "";
		} else
			if ($fileAttach == "") {
				$fileStyle = THUMB_PLAIN;
			}
		if (substr($thumbDir, 0, 1) != "/") {
			$thumbDir = $thumbDir;
		}

		$this->thumbDir = $thumbDir;
		$this->thumbFileStyle = $fileStyle;
		$this->thumbFileAttach = $fileAttach;
	}
	
	public function getThumbSubDir() {
		return $this->thumbSubDir;
	}
	
	public function getThumbFileAttach() {
		return $this->thumbFileAttach;
	}
	
	public function getThumbFileStyle() {
		return $this->thumbFileStyle;
	}
	
	public function getCurrentCollection() {
		return $this->currentCollection;
	}
}
?>

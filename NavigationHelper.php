<?php
/*
 * Author : Michael Feige
 *  
 * File PageViewer.php created on 14.02.2008
 * 
 * for more information check out
 * www.mickszone.de
 *
 * copyright 2008
 */
 
 /* 
  * $Log: NavigationHelper.php,v $
  * Revision 1.4  2008-07-02 21:10:06  michi
  * 	 - functionality for sendtype = POST completely implemented (and tested)
  * 	 - configurable css-styles completely implemented (and tested)
  * 	 - require_once for ErrorHandler included, so that it is now the only REALLY required file thats needed 100%
  *
  * Revision 1.3  2008-06-25 20:57:33  michi
  * repaired Log-Tag
  *
  * Revision 1.2  2008-06-25 20:56:47  michi
  * First implementation, containing:
  * 	- GET-Mode
  * 	- unfinished POST-Mode
  * 	- untested "Features" (needs a Debug)
  */
 include_once('./ConfigHelper.php');
  
 class NavigationHelper {
 	const GET = 0;
 	const POST = 1;
 	const AJAX = 2;
 	
  	private $sendType = 0;
 	private $currentPage = 0;
 	private $hasMore = 0;
 	private $rows = 5;
 	private $cols = 4;
 	private $attributeString = "rows=5&cols=4";
 	private $viewHelper = null;
 	
 	private $cssTableId = "style=\"width:100%\"";
	private $cssTdId = "style=\"width:25%\"";
	private $cssTrId = "style=\"width:100%\"";
 	private $cssFirst = "";
 	private $cssLast = "";
 	private $cssNext = "";
 	private $cssPrevious = "";
	
 	private $config;
 
 	
 	public function NavigationHelper($sendType, ViewHelper $viewHelper, ConfigHelper $configHelper) {
 		$this->sendType = $sendType;
 		$this->viewHelper = $viewHelper;
 		$this->config = $configHelper;
 		$this->retrieveData();
		
 		$this->currentPage = $viewHelper->getPage();
 		$this->hasMore = $viewHelper->getHasMore();
 		$this->rows = $configHelper->getRows();
 		$this->cols = $configHelper->getCols();
 		$this->sort = $configHelper->getSortType();
 		$this->attributeString = "rows=".$this->rows."&cols=".$this->cols."&sort=".$this->sort;
 	}
 	
 	public function setCSSIds($table, $td, $tr, $first, $previous, $next, $last) {
 		if ($table != null) {
 			$this->cssTableId = "id=\"".$table."\"";;
 		}
 		if ($tr != null) {
 			$this->cssTrId = "id=\"".$tr."\"";
 		}
 		if ($td != null) {
 			$this->cssTdId = "id=\"".$td."\"";
 		}
 		if ($first != null) {
 			$this->cssFirst = "id=\"".$first."\"";
 		}
 		if ($previous != null) {
 			$this->cssPrevious = "id=\"".$previous."\"";
 		}
 		if ($next != null) {
 			$this->cssNext = "id=\"".$next."\"";
 		}
 		if ($last != null) {
 			$this->cssLast = "id=\"".$last."\"";
 		}
 	}
 	public function drawFolderNavigation() {
		$folders = $this->imageHelper->checkSubDirs();
		echo ('<table width = "100%">');
		echo ('<tr>');
		foreach ($folders as $folder) {
			$path = './' . $this->imageHelper->getActiveDir() . '/' . $folder;
			echo ('<td><a href="'.$_PHP['SELF'].'?activeDir='.$path.'">'.$folder.'</a></td>');
		}
		echo ('</tr>');
		echo ('</table>');
	}
	
 	public function drawHTMLNavigation() {
		//Falls der sendType == POST ist muss ein Form vorbereitet werden 		
 		echo ("<table ".$this->cssTableId.">\n");
		echo ("  <tr ".$this->cssTrId.">\n");
		echo ("    <td ".$this->cssTdId.">&nbsp;\n");
		// FIRST 		
		$this->drawFirst();
		echo ("    </td>\n");
		echo ("    <td ".$this->cssTdId.">&nbsp;\n");
      	// PREV
      	$this->drawPrev();
		echo ("    </td>\n");
		echo ("    <td ".$this->cssTdId.">&nbsp;\n");
      	// NEXT
		$this->drawNext();
		echo ("    </td>\n");
		echo ("    <td ".$this->cssTdId.">&nbsp;\n");
      	// LAST
      	$this->drawLast();
		echo ("    </td>\n");
		echo ("  </tr>\n");
		echo ("</table>\n");
 	}
 	
 	private function drawFirst() {
		if ($this->currentPage > 0) {
			switch ($this->sendType) {
				case self::GET :
					echo ("      <a href=\"".$_SERVER["PHP_SELF"]."?".$this->attributeString."&page=0\">First</a>\n");
					break;
				case self::POST :
					echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">");
					echo ("		 <input type=\"hidden\" name=\"page\" value=\"0\" />");
					echo ("		 <input type=\"hidden\" name=\"sort\" value=\"".$this->sort."\" />");
	 				echo ("      <button ".$this->cssFirst." type=\"submit\">First</button>\n");
					echo ("		</form>");
					break;
			}
 		}
 	}
 	
 	private function drawPrev() {
		if ($this->currentPage > 0) {
			switch ($this->sendType) {
				case self::GET :
					echo ("      <a href=\"".$_SERVER["PHP_SELF"]."?".$this->attributeString."&page=".($this->currentPage - 1)."\">Prev</a>\n");
					break;
				case self::POST :
					echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">");
					echo ("		 <input type=\"hidden\" name=\"page\" value=\"".($this->currentPage - 1)."\" />");
					echo ("		 <input type=\"hidden\" name=\"sort\" value=\"".$this->sort."\" />");
	 				echo ("      <button ".$this->cssPrevious." type=\"submit\">Prev</button>\n");
					echo ("		</form>");
					break;
			}
 		}
 	}
 	
 	private function drawNext() {
 		if ($this->hasMore) {
			switch ($this->sendType) {
				case self::GET :
					echo ("      <a href=\"".$_SERVER["PHP_SELF"]."?".$this->attributeString."&page=".($this->currentPage + 1)."\">Next</a>\n");
					break;
				case self::POST :
					echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">");
					echo ("		 <input type=\"hidden\" name=\"page\" value=\"".($this->currentPage + 1)."\" />");
					echo ("		 <input type=\"hidden\" name=\"sort\" value=\"".$this->sort."\" />");
	 				echo ("      <button ".$this->cssNext." type=\"submit\">Next</button>\n");
					echo ("		</form>");
					break;
			}
		}
 	}
 	
 	private function drawLast() {
 		if ($this->hasMore) {
			switch ($this->sendType) {
				case self::GET :
					echo ("      <a href=\"".$_SERVER["PHP_SELF"]."?".$this->attributeString."&page=-1\">Last</a>\n");
					break;
				case self::POST :
					echo ("		<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">");
					echo ("		 <input type=\"hidden\" name=\"page\" value=\"-1\" />");
					echo ("		 <input type=\"hidden\" name=\"sort\" value=\"".$this->sort."\" />");
	 				echo ("      <button ".$this->cssLast." type=\"submit\">Last</button>\n");
					echo ("		</form>");
					break;
			}
		}
 	}
 	
 	private function retrieveData() {
	
		$activeDir = $_GET['activeDir'];
		if (is_dir($activeDir)) {
			settype($activeDir, "string");
			$this->config->setActiveDir($activeDir);
					}
 		switch ($this->sendType) {
			case self::GET:
				if (isset($_GET['rows'])) {
					$rows = $_GET['rows'];
					settype($rows, "integer");
					$this->config->setRows($rows);
				}
				if (isset($_GET['cols'])) {
					$cols = $_GET['cols'];
					settype($cols, "integer");
					$this->config->setCols($cols);
				}
				if (isset($_GET['page'])) {
					$page = $_GET['page'];
					settype($page, "integer");
					$this->viewHelper->setPage($page);
				}
				if (isset($_GET['sort'])) {
					$sort = $_GET['sort'];
					settype($sort, "integer");
					$this->config->setSortType($sort);
				}
				break;
			case self::POST:
				if (isset($_POST['rows'])) {
					$rows = $_POST['rows'];
					settype($rows, "integer");
					$this->config->setRows($rows);
				}
				if (isset($_POST['cols'])) {
					$cols = $_POST['cols'];
					settype($cols, "integer");
					$this->config->setCols($cols);
				}
				if (isset($_POST['page'])) {
					$page = $_POST['page'];
					settype($page, "integer");
					$this->viewHelper->setPage($page);
				}
				if (isset($_POST['sort'])) {
					$sort = $_POST['sort'];
					settype($sort, "integer");
					$this->config->setSortType($sort);
				}
				break;
		}
 	}
	
	private function outputError($errorCode) {
		if (file_exists('./ErrorHandler.php')) {
			require_once('./ErrorHandler.php');
			ErrorHandler::getErrorString($errorCode);
		}
	}
 }

?>

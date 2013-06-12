<?php
/*
 * Author : Michael Feige
 *  
 * File main.php created on 10.02.2008
 * 
 * for more information check out
 * www.mickszone.de
 *
 * copyright 2008
 */
 
/* 
 * $Log: main.php,v $
 * Revision 1.4  2008-07-02 21:17:43  michi
 * 	- main.php is now being used for demonstrating the more advanced features of combining all (or many) of the provided classes
 *
 * Revision 1.3  2008-06-25 20:59:29  michi
 * First commit with working Log-Tag (no interesting changes)
 *
 */

include_once('./ViewHelper.php');
include_once('./ErrorHandler.php');
include_once('./NavigationHelper.php');

$configHelper = new ConfigHelper();
$configHelper->setRows(4);
$configHelper->setCols(5);
$configHelper->setCanvasSize(140, 110);
$configHelper->setSortType(ViewHelper::SORT_DATE_ASC);
$configHelper->setCSSIds("imgTable", null, "imgTableRow");

$viewHelper = new ViewHelper($configHelper);

$navigationHelper = new NavigationHelper(NavigationHelper::POST, $viewHelper, $configHelper);
$navigationHelper->setCSSIds("navTable", null, null, "navButton", "navButton", "navButton", "navButton");
?>
<html>
	<head>
		
		<title>Gallery</title>
		<link rel="stylesheet" media="screen" href="gallery.css">
	</head>
	<body>
	
<!--?php $navigationHelper->drawFolderNavigation(); ?-->
	<br />
<?php $navigationHelper->drawHTMLNavigation(); ?>
		<br />
<?php $viewHelper->drawHTMLTable(); ?>
	</body>
</html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">

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

require_once('./ViewHelper.php');
require_once('./ErrorHandler.php');
require_once('./NavigationHelper.php');


$viewHelper = new ViewHelper();
$viewHelper->setRows(5);
$viewHelper->setCols(5);

$navigationHelper = new NavigationHelper(NavigationHelper::GET, $viewHelper);
$navigationHelper->setCSSIds("navTable", null, null, "navButton", "navButton", "navButton", "navButton");
$viewHelper->setCSSIds("imgTable", null, "imgTableRow");
?>

<html>
	<head>
		<title>Gallery</title>
		<link rel="stylesheet" media="screen" href="gallery.css">
	</head>
	<body>
	<div>
<?php $navigationHelper->drawHTMLNavigation(); ?>
		<br />
<?php $viewHelper->drawHTMLTable(); ?>
	</div>
	
	
	</body>
</html>


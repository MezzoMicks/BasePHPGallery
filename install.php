<?php
/*
 * Author : Michael Feige
 *  
 * File install.php created on 28.02.2008
 * 
 * for more information check out
 * www.mickszone.de
 *
 * copyright 2008
 */

/* 
 * $Log: install.php,v $
 * Revision 1.3  2008-06-25 19:44:45  michi
 * 	- log-Tag-check
 *
 */

	copy("./ImageHelper.inst", "./ImageHelper.php");
	copy("./NavigationHelper.inst", "./NavigationHelper.php");
	copy("./ErrorHandler.inst", "./ErrorHandler.php");
 
?>

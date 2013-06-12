<?php
/*
 * Author : Michael Feige
 *  
 * File ErrorHandler.php created on 25.02.2008
 * 
 * for more information check out
 * www.mickszone.de
 *
 * copyright 2008
 * 
 * see ErrorCodes for complete reference
 */
 
/* 
 * $Log: ErrorHandler.php,v $
 * Revision 1.2  2008-06-25 21:01:58  michi
 * typos corrected
 *
 */
 class ErrorHandler {
 	
 	static $errors = array(
 			100 => "Unknown error",
 			210 => "Submitted values for canvas-width and height must be a positive integer",
 			211 => "Submitted value for columns must be a positive integer",
 			212 => "Submitted value for rows must be a positive integer",
 			213 => "Submitted value for page must be a positive integer",
 			220 => "Submitted active dir is not a valid directory",
 			302 => "Specified thumbdir-name is already a file, couldn't create directory!",
 			303 => "Creation of thumbdir failed, please check file/dir-permissions!",
 			401 => "File already in database",
 			402 => "Unknown Fileformat supported formats \"jpg,png,gif\"",
 			410 => "Fileoperation failed",
 			411 => "Fileparsing failed",
 			901 => "Creation of thumbdir not yet implemented, please create thumbdir manually");
 	

	
 	public static function outputError($errorCode ,$info='') {
 		echo ("\n<b>Error :</b> ");
 		if (is_int($errorCode) && isset(self::$errors[$errorCode])) {
 			echo (self::$errors[$errorCode]." ".$info);
 		} else {
 			echo (self::$errors[100]." ".$info);
 		}
 	}
 }
 
?>

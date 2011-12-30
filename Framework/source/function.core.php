<?php
/**
 * @version 	PHP 5.2.3
 * @package		Core
 * @author		Matthias Mohr
 * @since 		1.0
 */

// Set error reporting to E_ALL + E_STRICT
error_reporting(E_ALL|E_STRICT);

/** Load Core class */
require_once("source/Core/class.Core.php");
// Load class ErrorHandler and get Object of type ErrorHandling and load error_handler and exception_handler
//$errorHandler = Core::getObject('Core.System.ErrorHandling');

// Try to set Magic Quotes while script runs: off
@set_magic_quotes_runtime(0);
// Try to set Magic quotes for incoming variables: off
@ini_set('magic_quotes_gpc',0);

define('NL', "\r\n");

/**
 * Loads the required classes automatically from ClassManager (only indexed classes).
 *
 * @param string Class Name
 */
function __autoload($className) {
	if ($className === 'parent') {
		// Avoid calling parent when using callback function.
		// See: http://www.php.net/manual/de/function.call-user-func.php#106391
		return; 
	}
	$classManager = Core::getObject('Core.System.ClassManager');
	if (Config::get('core.debug')) {
		Core::throwError("Autoloaded class with name '{$className}'", INTERNAL_DEBUG);
	}
    $file = $classManager->loadFile($className);
    if ($file == null) {
    	Core::throwError('Class "{$className}" not found', INTERNAL_ERROR);
    }
}
// Load class Config
Core::loadClass('Core.Util.Config');
// Load class validator (for SystemEnvironment)
Core::loadClass('Core.Security.Validator');
// Load class SystemEnvironment
Core::loadClass('Core.System.SystemEnvironment');
// Load classes for Files and Folders (often used)
Core::loadClass('Core.FileSystem.File');
Core::loadClass('Core.FileSystem.Folder');
// Load ModuleObject class (each module needs it)
Core::loadClass('Core.ModuleObject');

/**
 * Checks whether the specified number is a natural number (or an id).
 *
 * An natural number is an integer greater than 0. (1,2,3,4,5,...,100,...)
 *
 * @param int Number to check
 * @return boolean true if the parameter is an id, false if not.
 **/
function is_id ($x) {
   return ((is_numeric($x) == true && $x > 0) ? (intval($x) == $x) : false);
}

/**
 * Chacks a comparison and returns the first parameter for true, the second parameter for false.
 *
 * Note: Don't use undefined variables etc. in the second or third parameter. You'll get a PHP Notice.
 *
 * @param mixed Statement to check
 * @param mixed Return on true
 * @param mixed Return on false (default is an empty string)
 * @return If the specified statement is correct the second parameter otherwise the third parameter
 **/
function iif($if, $true, $false = '') {
	return ($if ? $true : $false);
}


?>

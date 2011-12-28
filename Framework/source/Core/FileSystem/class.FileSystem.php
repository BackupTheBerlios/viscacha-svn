<?php
Core::loadClass('Core.System.SystemEnvironment');

/**
 * Some static utilities for the file system.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FileSystem {

    /**
     * Returns canonicalized absolute pathname to a file or directory.
     *
     * This works with non-existant paths. For directories there won't be a trailing slash.
	 *
	 * LICENSE:
	 * This source file is subject to version 3.0 of the PHP license
	 * that is available through the world-wide-web at the following URI:
	 * http://www.php.net/license/3_0.txt. If you did not receive a copy of
	 * the PHP License and are unable to obtain it through the web, please
	 * send a note to license@php.net so we can mail you a copy immediately.
     *
	 * @author	Michael Wallner <mike@php.net>
	 * @copyright	2004-2005 Michael Wallner
	 * @license	PHP License 3.0 http://www.php.net/license/3_0.txt
	 * @link	http://pear.php.net/package/File
     * @param	string	Path to canonicalize to absolute path
     * @param	string	Directory Seperator (default: Value from DIRECTORY_SEPERATOR)
     * @return	string	Canonicalized absolute pathname
     * @static
     */
    public static function realPath($path, $separator = DIRECTORY_SEPARATOR) {
        if (!strlen($path)) {
            return $separator;
        }

        $drive = '';
        if (SystemEnvironment::isWindows() == true) {
            $path = preg_replace('/[\\\\\/]/', $separator, $path);
            if (preg_match('/([a-zA-Z]\:)(.*)/', $path, $matches)) {
                $drive = $matches[1];
                $path  = $matches[2];
            }
            else {
                $cwd   = getcwd();
                $drive = substr($cwd, 0, 2);
                if ($path{0} !== $separator{0}) {
                    $path  = substr($cwd, 3) . $separator . $path;
                }
            }
        }
        elseif ($path{0} !== $separator) {
            $path = getcwd() . $separator . $path;
        }

        $dirStack = array();
        foreach (explode($separator, $path) as $dir) {
            if (strlen($dir) && $dir !== '.') {
                if ($dir == '..') {
                    array_pop($dirStack);
                }
                else {
                    $dirStack[] = $dir;
                }
            }
        }

        return $drive . $separator . implode($separator, $dirStack);
    }
}
?>

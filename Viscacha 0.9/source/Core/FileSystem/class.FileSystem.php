<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Utility functions for files and folders.
 *
 * This class also handles the ftp connection and debug information for the File and Folder objects.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class FileSystem {

	private static $ftp = null;
	private static $debug = null;

	/**
	 * Returns an object that is a child of the FTPClient class or null on failure.
	 *
	 * @todo Replace host data / working dir with config data
	 */
	public static function initializeFTP() {
		if (self::$ftp === null) {
			self::$ftp = FTPClient::getObject();
			if (self::$ftp !== null && self::$ftp->setServer('localhost', 21) !== true) {
				self::addDebugText('Could not set ftp server to ...');
				self::$ftp = null;
			}
			if (self::$ftp !== null && self::$ftp->connect() !== true) {
				self::addDebugText('Could not connect to ftp server');
				self::$ftp->quit();
				self::$ftp = null;
			}
			if (self::$ftp !== null && self::$ftp->login('user', 'password') !== true) {
				self::addDebugText('Could not login on ftp server with user data ...');
				self::$ftp->quit();
				self::$ftp = null;
			}
			if (self::$ftp !== null && self::$ftp->chdir(DIRECTORY_SEPARATOR) !== true) {
				self::addDebugText('Could not change directory on ftp server to ...');
				self::$ftp->quit();
				self::$ftp = null;
			}
		}
		return self::$ftp;
	}

	public static function getDebug() {
		if (self::$debug === null) {
			self::$debug = new Debug('filesystem.log');
		}
		return self::$debug;
	}

	public static function addDebugText($text) {
		self::getDebug()->addText($text);
	}

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
    public static function unifyPath($path, $separator = DIRECTORY_SEPARATOR) {
        if (!strlen($path)) {
            return $separator;
        }

        $drive = '';
        if (System::getOS() == System::WINDOWS) {
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

	public static function checkTrailingSlash($path, $addSlash = false) {
		$path = rtrim($path, '\\/');
		if ($addSlash == true) {
			$path .= Folder::SEPARATOR;
		}
		return $path;
	}
}
?>
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
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Internal Version Number of Viscacha.
 *
 * Version 1 was Viscacha 0.8, Version 2 is Viscacha Core.
 */
define('VISCACHA_CORE', '2');

// Define several paths
define('VISCACHA_CACHE_DIR', 'data/cache/');
define('VISCACHA_TEMP_DIR', 'data/temp/');
define('VISCACHA_LOGS_DIR', 'data/logs/');
define('VISCACHA_UPLOAD_DIR', 'data/upload/');
define('VISCACHA_CONFIG_FILE', 'data/config.php');
define('VISCACHA_ERROR_CSS_FILE', 'client/error.css');
define('VISCACHA_JS_DIR', 'client/js/');
define('VISCACHA_DESIGN_DIR', 'client/designs/');
define('VISCACHA_SOURCE_DIR', 'source/');
// Get the script start time for benchmarks
$scriptStart = microtime(true);

// Boot the object oriented system...
require_once('source/Core/Kernel/class.Core.php');
require_once('source/Core/Kernel/function.core.php');

// Load the class manager for autoload support
Core::loadClass('Core.Kernel.ClassManager');

 // Store temporary entries (Registry like) - Namespace: temp
Config::setConfigHandler(new TempConfig(), 'temp');
// Load/Write entries from a native php array in file data/config.php - Namespace: base
Config::setConfigHandler(new PHPConfig(VISCACHA_CONFIG_FILE), 'base');
// Load/Write entries from a database table named config - Namespace: core
// Config::setConfigHandler(new DBConfig('config'), 'core');

// set the script start and cwd to temp config
Config::set('temp.benchmark.start', $scriptStart);
Config::set('temp.system.cwd', getcwd()); // see FileSystem::resetWorkdingDir() for more information

// Set up database connection
if (Config::get('base.database.enabled') == true) {
	$db = Database::getObject(Config::get('base.database.driver'));
	$db->connect(
		Config::get('base.database.username'),
		Config::get('base.database.password'),
		Config::get('base.database.host'),
		Config::get('base.database.port'),
		Config::get('base.database.socket')
	);
	Core::storeObject($db, 'DB');
}
?>
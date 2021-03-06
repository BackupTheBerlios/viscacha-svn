<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2007  Matthias Mohr, MaMo Net
	
	Author: Matthias Mohr
	Publisher: http://www.mamo-net.de
	Start Date: May 22, 2004

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

include('classes/class.gpc.php');
$gpc = new GPC();

// Thanks to phpBB for this code
if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on') {
	unset($not_used, $input);
	$not_unset = array('_GET', '_POST', '_COOKIE', '_SERVER', '_SESSION', '_ENV', '_FILES', 'config', 'gpc', 'imagetype_extension');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_ENV, $_FILES);
	if (isset($_SERVER)) {
		$input = array_merge($input, $_SERVER);
	}
	if (isset($_SESSION) && is_array($_SESSION)) {
		$input = array_merge($input, $_SESSION);
	}

	unset($input['input'], $input['not_unset']);

	while (list($var,) = @each($input)) {
		if (!in_array($var, $not_unset)) {
			unset($$var);
			if (isset($GLOBALS[$var])) {
				unset($GLOBALS[$var]);
			}
		}
	}

	unset($input);
}

if (get_magic_quotes_gpc() == 1) {
	$_GET = $gpc->stripslashes($_GET);
	$_POST = $gpc->stripslashes($_POST);
	$_REQUEST = $gpc->stripslashes($_REQUEST);
}

// Thanks to phpBB for this 6 lines
if (isset($_POST['GLOBALS']) || isset($_FILES['GLOBALS']) || isset($_GET['GLOBALS']) || isset($_COOKIE['GLOBALS'])) {
	die("Hacking attempt (Globals)");
}
if (isset($_SESSION) && !is_array($_SESSION)) {
	die("Hacking attempt (Session Variable)");
}

$http_svars = array(
	'PHP_SELF',
	'HTTP_USER_AGENT',
	'SERVER_SOFTWARE',
	'REMOTE_ADDR',
	'SCRIPT_NAME',
	'SERVER_PORT',
	'SERVER_NAME',
	'HTTP_REFERER',
	'HTTP_X_FORWARDED_FOR',
	'HTTP_CLIENT_IP',
	'REQUEST_URI',
	'HTTP_ACCEPT_ENCODING',
	'DOCUMENT_ROOT'
);
if (function_exists('getallheaders')) {
	$ref = @getallheaders();
}
else {
	$ref = array();
}
foreach ($http_svars as $http_var) {
	$func_key = '';
	if (substr($http_var, 0, 5) == 'HTTP_') {
		$func_key = strtolower(str_replace('_', ' ', substr($http_var, 5)));
		$func_key = str_replace(' ', '-', ucwords($func_key));
	}
	if (empty($_SERVER[$http_var]) && !empty($ref[$func_key])) {
		$_SERVER[$http_var] = $ref[$func_key];
	}
	else {
		$_SERVER[$http_var] = getenv($http_var);
	}
}
unset($ref);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = getDocumentRoot();
	if (empty($_SERVER['DOCUMENT_ROOT'])) {
		$_SERVER['DOCUMENT_ROOT'] = $config['fpath'];
	}
}

$_SERVER = $gpc->secure_null($_SERVER);
$_ENV = $gpc->secure_null($_ENV);
?>

<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2009  The Viscacha Project

	Author: Matthias Mohr (et al.)
	Publisher: The Viscacha Project, http://www.viscacha.org
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

error_reporting(E_ALL);

define('SCRIPTNAME', 'lang2js');
define('VISCACHA_CORE', '1');

include('../data/config.inc.php');
include('../classes/function.phpcore.php');
include('../classes/class.language.php');

header('Content-type: text/javascript');

if (!empty($_REQUEST['id'])) {
	// prepare the data
	$id = intval($_REQUEST['id']);
	$lang = new lang($id);
	$file = !empty($_REQUEST['admin']) ? 'admin/javascript' : 'javascript';

	// Send the cache header (or not)
	$modified = @filemtime($lang->file);
	$time = gmdate('D, d M Y H:i:s', $modified);
	viscacha_header("Last-Modified: {$time} GMT");
	viscacha_header('Cache-Control: must-revalidate');
	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $modified) {
		sendStatusCode(304, $modified);
	}
	else {
		sendStatusCode(200, $modified);
		echo "var cookieprefix = '{$config['cookie_prefix']}';";
		$code = $lang->javascript($file);
		if ($code !== false) {
			echo $code;
		}
		else {
			echo 'alert("Could not load or parse language file (JS)!");';
		}
	}
}
else {
	echo 'alert("Could not find language file (JS) without id!");';
}
?>
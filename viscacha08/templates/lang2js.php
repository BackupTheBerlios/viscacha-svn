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
	$id = intval(trim($_REQUEST['id']+0));
	$lang = new lang($id);
	$file = !empty($_REQUEST['admin']) ? 'admin/javascript' : 'javascript';
	echo $lang->javascript($file);
	echo "var cookieprefix = '{$config['cookie_prefix']}';";
}
else {
	echo 'alert("Could not load language file for javascript without id!");';
}
?>
<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2007  Matthias Mohr, MaMo Net

	Author: Matthias Mohr
	Publisher: http://www.viscacha.org
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

define('SCRIPTNAME', 'forum');
if (!defined('VISCACHA_CORE')) {
	define('VISCACHA_CORE', '1');
}

require_once("data/config.inc.php");

if ($config['indexpage'] == SCRIPTNAME && !defined('IS_INCLUDED')) {
	require_once("classes/function.phpcore.php");
	sendStatusCode(301, 'index.php');
    exit;
}

require_once("classes/function.viscacha_frontend.php");

$my->p = $slog->Permissions();
$my->pb = $slog->GlobalPermissions();

echo $tpl->parse("header");
echo $tpl->parse("menu");

$memberdata_obj = $scache->load('memberdata');
$memberdata = $memberdata_obj->get();

($code = $plugins->load('forum_start')) ? eval($code) : null;

BoardSelect();

($code = $plugins->load('forum_end')) ? eval($code) : null;

$slog->updatelogged();
$zeitmessung = t2();
echo $tpl->parse("footer");
$phpdoc->Out();
$db->close();
?>
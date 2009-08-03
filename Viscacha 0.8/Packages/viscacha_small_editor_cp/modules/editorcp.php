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

define('SCRIPTNAME', 'editorcp');
define('VISCACHA_CORE', '1');

include ("data/config.inc.php");
include ("admin/data/config.inc.php");

if (empty($config['cryptkey']) || empty($config['database']) || empty($config['dbsystem'])) {
	trigger_error('Viscacha is currently not installed. How to install Viscacha is described in the file "_docs/readme.txt"!', E_USER_ERROR);
}
if (empty($config['dbpw']) || empty($config['dbuser'])) {
	trigger_error('You have specified database authentification data that is not safe. Please change your database user and the database password!', E_USER_ERROR);
}

include ("editorcp/lib/function.viscacha_backend.php");

if ($my->p['admin'] == 1 || $my->p['editor'] == 1) {

	if ($action == "frames") {
		include('editorcp/frames.php');
	}
	elseif ($action == 'cms') {
		include('editorcp/cms.php');
	}
	elseif ($action == 'logout') {
		$slog->sid_logout();
		echo head();
		ok('editorcp.php', $lang->phrase('admin_successfully_logged_off'));
	}
	else {
		if (strlen($action) == 0) {
			include('editorcp/frames.php');
		}
		else {
			echo head();
			error('editorcp.php?action=frames&job=start'.SID2URL_x, $lang->phrase('admin_requested_page_doesnot_exist'));
		}
	}
}
else {
	if (!($my->p['editor'] == 1 || $my->p['admin'] == 1) && $my->vlogin) {
		echo head();
		error('index.php'.SID2URL_1, $lang->phrase('admin_not_allowed_to_view_this_page'));
	}

	include("classes/function.flood.php");

	$addr = rawurldecode($gpc->get('addr', none));
	if ($action == "login2") {
		$log_status = $slog->sid_login(true);
		if ($log_status == false) {
			$attempts = set_failed_login();
			if ($attempts == $config['login_attempts_max']) {
				header('Location: index.php'.SID2URL_1);
			}
			else {
				echo head();
				error('editorcp.php'.iif(!empty($addr), '?addr='.rawurlencode($addr)), $lang->phrase('admin_incorrect_username_or_password_entered'));
			}
		}
		else {
			clear_login_attempts();
			echo head();
			ok('editorcp.php'.iif(!empty($addr), '?addr='.rawurlencode($addr)), $lang->phrase('admin_successfully_logged_in'));
		}
	}
	else {
		echo head();
		AdminLogInForm();
	}
	echo foot();
}

$slog->updatelogged();
$db->close();
?>
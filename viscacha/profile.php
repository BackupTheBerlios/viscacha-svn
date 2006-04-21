<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2006  Matthias Mohr, MaMo Net
	
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
error_reporting(E_ALL);

DEFINE('SCRIPTNAME', 'profile');

include ("data/config.inc.php");
include ("classes/function.viscacha_frontend.php");

$zeitmessung1 = t1();

$slog = new slog();
$my = $slog->logged();
$lang->init($my->language);
$tpl = new tpl();
$my->p = $slog->Permissions();

$is_guest = false;
$is_member = false;
$url_ext = '';
$guest = $gpc->get('guest', int);

$memberdata_obj = $scache->load('memberdata');
$memberdata = $memberdata_obj->get();

if (isset($memberdata[$_GET['id']])) {
	$username = $memberdata[$_GET['id']];
}
else {
	$username = $lang->phrase('fallback_no_username');
}

if ($my->p['profile'] != 1) {
	errorLogin();
}

if ($guest > 0) {
	$result = $db->query("SELECT email, name, guest FROM {$db->pre}replies WHERE id = '{$guest}' AND guest = '1' LIMIT 1");
	$guest_data = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 1) {
		$is_guest = true;
		$username = $guest_data['name'];
		$email = $guest_data['email'];
		$url_ext = '&amp;guest='.$guest;
	}
	else {
		$is_guest = false;
	}
}
else {
	$is_guest = false;
}
if (isset($memberdata[$_GET['id']])) {
	$is_member = true;
}


$breadcrumb->Add($lang->phrase('members'), 'members.php'.SID2URL_1);
$breadcrumb->Add($lang->phrase('profile_title'), 'profile.php?id='.$_GET['id'].$url_ext.SID2URL_x);

if ($_GET['action'] == "vcard" && $is_member && $config['vcard_dl'] == 1 && ((!$my->vlogin && $config['vcard_dl_guests'] == 1) || $my->vlogin)) {
	require ("classes/class.vCard.inc.php");

	$result = $db->query("SELECT id, name, mail, hp, birthday, location, fullname, groups FROM {$db->pre}user WHERE id = ".$_GET['id'],__LINE__,__FILE__);
	$row = $gpc->prepare($db->fetch_object($result));
	$row->level = $slog->getStatus($row->groups, ', ');
	
	$vCard = new vCard('','');
	$vCard->setNickname($row->name);
	$vCard->setEMail($row->mail);
	$vCard->setNote($lang->phrase('vcard_note'));
	if (!empty($row->fullname)) {
		$names = explode(' ', $row->fullname);
		$anz = count($names);
		$middle = '';
		foreach ($names as $middlename) {
			if ($middlename != $names[0] && $middlename != $names[$anz-1]) {
				$middle .= $middlename;
			}
		}
			
		$vCard->setFirstName($names[0]);
		$vCard->setMiddleName($middle);
		$vCard->setLastName($names[$anz-1]);
	}
	if (!empty($row->location)) {
		$vCard->setHomeCity($row->location);
		$vCard->setPostalCity($row->location);
	}
	if (!empty($row->hp)) {
		$vCard->setURLWork($row->hp);
	}
	if (!empty($row->birthday) && $row->birthday != '0000-00-00') {
		$bday = str_replace('-', '', $row->birthday);
		$vCard->setBirthday($bday,1);
	}
		
	$filename = $row->id . '.vcf';
	$text = $vCard->getCardOutput();
	viscacha_header("Content-Disposition: attachment; filename=$filename");
	viscacha_header('Content-Length: '. strlen($text));
	viscacha_header("Content-Type: text/x-vcard; name=$filename");
	echo $text;
	exit();
}
elseif (($_GET['action'] == 'mail' || $_GET['action'] == 'sendmail') && $is_member) {
	$result=$db->query('SELECT id, name, opt_hidemail, mail FROM '.$db->pre.'user WHERE id = '.$_GET['id'],__LINE__,__FILE__);
	$row = $gpc->prepare($db->fetch_object($result));
	$breadcrumb->Add($lang->phrase('profile_mail_2'));

	if ($my->vlogin && $row->opt_hidemail != 1) {
		if ($_GET['action'] == 'sendmail') {
		
			$error = array();
			if (flood_protect() == FALSE) {
				$error[] = $lang->phrase('flood_control');
			}
			if (strxlen($_POST['comment']) > $config['maxpostlength']) {
				$error[] = $lang->phrase('comment_too_long');
			}
			if (strxlen($_POST['comment']) < $config['minpostlength']) {
				$error[] = $lang->phrase('comment_too_short');
			}
			if (strxlen($_POST['topic']) > $config['maxtitlelength']) {
				$error[] = $lang->phrase('title_too_long');
			}
			if (strxlen($_POST['topic']) < $config['mintitlelength']) {
				$error[] = $lang->phrase('title_too_short');
			}
			if (count($error) > 0) {
				$data = array(
					'topic' => $_POST['topic'],
					'comment' => $_POST['comment']
				);
				$fid = save_error_data($data);
				error($error,"profile.php?action=mail&amp;id={$_GET['id']}&amp;fid=".$fid.SID2URL_x);
			}
			else {
				set_flood();
				$to = array('0' => array('name' => $row->name, 'mail' => $row->mail));
				$from = array('name' => $my->name, 'mail' => $my->mail);
				xmail($to, $from, $_POST['topic'], $gpc->unescape($_POST['comment']));
				ok($lang->phrase('email_sent'),"profile.php?id=".$_GET['id'].SID2URL_x);
			}
		
		}
		else {
			if ($row->opt_hidemail == 0) {
				$chars = array('@','.');
				$entities = array('&#64;','&#46;');
				$row->mail = str_replace($chars, $entities, $row->mail);
			}
			
			if (strlen($_GET['fid']) == 32) {
				$data = $gpc->prepare(import_error_data($_GET['fid']));
			}
			else {
				$data = array(
					'comment' => '',
					'topic' => ''
				);
			}
			echo $tpl->parse("header");
			$plugins->load('profile_mail_top');
			echo $tpl->parse("profile/mail");
			$plugins->load('profile_mail_bottom');
			
		}
	}
	else {
		errorLogin();
	}
}
elseif ($_GET['action'] == "sendjabber" && $is_member) {

	$error = array();
	if (flood_protect() == FALSE) {
		$error[] = $lang->phrase('flood_control');
	}
	if (strxlen($_POST['comment']) > $config['maxpostlength']) {
		$error[] = $lang->phrase('comment_too_long');
	}
	if (strxlen($_POST['comment']) < $config['minpostlength']) {
		$error[] = $lang->phrase('comment_too_short');
	}
	if (count($error) > 0) {
		error($error,"profile.php?action=ims&amp;type=jabber&amp;id=".$_GET['id'].SID2URL_x);
	}
	else {
		set_flood();
		$result = $db->query('SELECT jabber FROM '.$db->pre.'user WHERE id = "'.$_GET['id'].'"',__LINE__,__FILE__);
		$row = $gpc->prepare($db->fetch_assoc($result));
		include('classes/function.jabber.php');
		$jabber = new Viscacha_Jabber();
		$connid = $jabber->connect();
		if ($connid != TRUE) {
			error($connid,"profile.php?action=ims&amp;type=jabber&amp;id=".$_GET['id'].SID2URL_x);
		}
		$msgid = $jabber->send_message($row['jabber'], $gpc->unescape($_POST['comment']));
		if ($msgid != TRUE) {
			error($msgid,"profile.php?action=ims&amp;type=jabber&amp;id=".$_GET['id'].SID2URL_x);
		}
		$jabber->disconnect();
		ok($lang->phrase('post_sent'), "profile.php?action=ims&amp;type=jabber&amp;id=".$_GET['id'].SID2URL_x);
	}
}
elseif ($_GET['action'] == "ims" && $is_member) {
	$error = array();
	if ($_GET['type'] == 'icq' || $_GET['type'] == 'aol' || $_GET['type'] == 'yahoo' || $_GET['type'] == 'msn' || $_GET['type'] == 'jabber' || $_GET['type'] == 'skype') {
		$imtext = $lang->phrase('im_'.$_GET['type']);
	}
	else {
		$error[] = $lang->phrase('query_string_error');
	}
	if ($my->p['profile'] == 0) {
		$error[] = $lang->phrase('not_allowed');
	}
		
	$result = $db->query('SELECT id, name, icq, aol, yahoo, msn, jabber, skype FROM '.$db->pre.'user WHERE id = "'.$_GET['id'].'"',__LINE__,__FILE__);
	$row = $gpc->prepare($db->fetch_assoc($result));
	if ($row[$_GET['type']] == NULL || $row[$_GET['type']] == '') {
		$error[] = $lang->phrase('im_no_data');
	}
	if (count($error) > 0) {
		errorLogin($error, 'profile.php?id='.$_GET['id'].SID2URL_x);
	}
	else {
		$t = $_GET['type'];
		$d = $row[$_GET['type']];
	
		$breadcrumb->Add($imtext);
		echo $tpl->parse("header");
		echo $tpl->parse("menu");
		include("classes/class.imstatus.php");
		$imstatus = new IMStatus();
		if ($t == 'aol') {
			$status = $imstatus->aim($d);
		}
		else {
			$status = $imstatus->$t($d);
		}
		if ($status) {
			$imstatus = $lang->phrase('im_status_'.$status);	
		}
		else {
			$imstatus = $lang->phrase('im_no_connection').'<!-- Error #'.$imstatus->error(IM_ERRNO).' occurred during query: '.$imstatus->error(IM_ERRSTR).' -->';
		}
		echo $tpl->parse("profile/ims");
		$plugins->load('profile_ims_bottom');
	}
}
elseif ($_GET['action'] == 'emailimage' && $is_guest) {
	if (headers_sent()) {
		exit;
	}
	include('classes/graphic/class.text2image.php');
	$img = new text2image();
	$img->prepare($email, 0, 10, 'classes/fonts/trebuchet.ttf');
	$img->build();
	$img->output();
	exit;
}
elseif ($is_guest) {
	$breadcrumb->resetUrl();
	echo $tpl->parse("header");
	echo $tpl->parse("menu");
	$group = 'fallback_no_username';
	echo $tpl->parse("profile/guest");
}
elseif ($is_member) {
	$result = $db->query("SELECT u.*, f.* FROM {$db->pre}user AS u LEFT JOIN {$db->pre}userfields AS f ON u.id = f.ufid WHERE id = {$_GET['id']} LIMIT 1",__LINE__,__FILE__);
	if ($db->num_rows($result) == 1) {
		$row = $gpc->prepare($db->fetch_object($result));
	
		if ($config['showpostcounter']) {
			$anz= $db->fetch_array($db->query('SELECT COUNT(name) FROM '.$db->pre.'replies WHERE name = "'.$_GET['id'].'"',__LINE__,__FILE__)); // etwas ungenau, aber noch recht schnell
			
			$days2 = $anz[0] / ((times() - $row->regdate) / 86400);
			$days2 = sprintf("%01.2f", $days2);
		}
		if ($anz[0] < $days2) {
			$days2 = $anz[0];
		}

		$breadcrumb->resetUrl();
		echo $tpl->parse("header");
		echo $tpl->parse("menu");
		
		$row->p = $slog->Permissions(0,$row->groups, true);
		$row->level = $slog->getStatus($row->groups);
		
		$row->regdate = gmdate($lang->phrase('dformat2'),times($row->regdate));
		$row->lastvisit = str_date($lang->phrase('dformat1'),times($row->lastvisit));
		
		$vcard = ($config['vcard_dl'] == 1 && ((!$my->vlogin && $config['vcard_dl_guests'] == 1) || $my->vlogin));
			
		BBProfile($bbcode);
		$bbcode->setSmileys(1);
		$bbcode->setReplace(0);
		$bbcode->setAuthor($row->id);
		$row->about = $bbcode->parse($row->about);
		
		BBProfile($bbcode, 'signature');
		$row->signature = $bbcode->parse($row->signature);
		
		// Set the instant-messengers
		if ($row->jabber || $row->icq > 0 || $row->aol || $row->msn || $row->yahoo || $row->skype) {
			$imanz = 1;
		}
		else {
			$imanz = 0;
		}
		
		if ($row->gender == 'm') {
			$gender = $lang->phrase('gender_m');
		}
		elseif ($row->gender == 'w') {
			$gender = $lang->phrase('gender_w');
		}
		else {
			$gender = $lang->phrase('gender_na');
		}
		$bday = explode('-',$row->birthday);
		if ($row->birthday != NULL && $row->birthday != '0000-00-00') {
			if ($bday[0] > 0) {
				$bday_age = getAge($bday);
			}
			$show_bday = TRUE;
		}
		else {
			$show_bday = FALSE;
		}
		if (isset($bday[1]) && $bday[1] > 0 && $bday[1] < 13) {
			$bday[1] = $lang->phrase('months_'.intval($bday[1]));
		}
		
		$osi = '';
		$vcarddl = '';
		if ($config['osi_profile'] == 1) {
			$result = $db->query('SELECT mid, active FROM '.$db->pre.'session WHERE mid = '.$_GET['id'],__LINE__,__FILE__);
			$wwo = $db->fetch_array($result);
			if ($wwo[0] > 0) {
				$wwo[1] = gmdate($lang->phrase('dformat3'),times($wwo[1]));
				$osi = 1;
			}
			else {
				$osi = 0;
			}
		}

		// Custom Profile Fields
		$customfields = array('1' => array(), '2' => array(), '3' => array());
		$query = $db->query("SELECT * FROM ".$db->pre."profilefields WHERE viewable != '0' ORDER BY disporder");
		while($profilefield = $db->fetch_assoc($query)) {
			$select = array();
			$thing = explode("\n", $profilefield['type'], 2);
			$type = $thing[0];
			if (!isset($thing[1])) {
				$options = '';
			}
			else {
				$options = $thing[1];
			}
			$field = "fid{$profilefield['fid']}";
			if($type == "multiselect") {
				$useropts = @explode("\n", $row->$field);
				while(list($key, $val) = each($useropts)) {
					$seloptions[$val] = $val;
				}
				$expoptions = explode("\n", $options);
				if(is_array($expoptions)) {
					while(list($key, $val) = each($expoptions)) {
						list($key, $val) = explode('=', $val, 2);
						if(isset($seloptions[$key]) && $key == $seloptions[$key]) {
							$select[] = trim($val);
						}
					}
					$code = implode(', ', $select);
				}
			}
			elseif($type == "select") {
				$expoptions = explode("\n", $options);
				if(is_array($expoptions)) {
					while(list($key, $val) = each($expoptions)) {
						list($key, $val) = explode('=', $val, 2);
						if ($key == $row->$field) {
							$code = trim($val);
						}
					}
				}
			}
			elseif($type == "radio") {
				$expoptions = explode("\n", $options);
				if(is_array($expoptions)) {
					while(list($key, $val) = each($expoptions)) {
						list($key, $val) = explode('=', $val, 2);
						if ($key == $row->$field) {
							$code = trim($val);
						}
					}
				}
			}
			elseif($type == "checkbox") {
				$useropts = @explode("\n", $row->$field);
				while(list($key, $val) = each($useropts)) {
					$seloptions[$val] = $val;
				}
				$expoptions = explode("\n", $options);
				if(is_array($expoptions)) {
					while(list($key, $val) = each($expoptions)) {
						list($key, $val) = explode('=', $val, 2);
						if (isset($seloptions[$key]) && $key == $seloptions[$key]) {
							$select[] = trim($val);
						}
					}
					$code = implode(', ', $select);
				}
			}
			elseif($type == "textarea") {
				$code = nl2br($row->$field);
			}
			else {
				$code = $row->$field;
			}
			if (empty($code)) {
				$code = $lang->phrase('profile_na');
			}
			$customfields[$profilefield['viewable']][] = array(
				'value' => $code,
				'name' => $profilefield['name'],
				'description' => $profilefield['description'],
				'maxlength' => $profilefield['maxlength']
			);
			unset($code, $select, $val, $options, $expoptions, $useropts, $seloptions);
		}
		
		if ($config['memberrating'] == 1) {
			$result = $db->query("SELECT rating FROM {$db->pre}postratings WHERE aid = '{$row->id}'");
			$ratings = array();
			while ($dat = $db->fetch_assoc($result)) {
				$ratings[] = $dat['rating'];
			}
			$ratingcounter = count($ratings);
			if ($ratingcounter> 0 && $ratingcounter >= $config['memberrating_counter']) {
				$row->rating = round(array_sum($ratings)/$ratingcounter*50)+50;
			}
			else {
				$row->rating = $lang->phrase('profile_na');
			}
		}

		$plugins->load('profile_top');
		echo $tpl->parse("profile/index");
		$plugins->load('profile_bottom');

	}
	else {
		$group = 'fallback_no_username_group';
		echo $tpl->parse("profile/guest");
	}
}
else {
	viscacha_header('Location: members.php');
}

$slog->updatelogged();
$zeitmessung = t2();
echo $tpl->parse("footer");
$phpdoc->Out();
$db->close();		
?>

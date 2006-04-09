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

if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "function.global.php") die('Error: Hacking Attempt');

// INI-File-Class
include_once("classes/class.ini.php");
// A class for Languages
require_once("classes/class.language.php");
// Gets modules
require_once("classes/class.plugins.php");
// Gets a file with Output-functions
require_once("classes/class.docoutput.php");
$myini = new INI();
$lang = new lang();
$mymodules = new MyModules();
// Database functions
require_once('classes/database/'.$config['dbsystem'].'.inc.php');
$db = new DB($config['host'], $config['dbuser'], $config['dbpw'], $config['database'], $config['pconnect'], true, $config['dbprefix']);
$db->pre = $db->prefix();

function send_nocache_header() {
	if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2')) {
		header ('Cache-Control: no-cache, pre-check=0, post-check=0');
	}
	else {
		header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
	}
	header ('Expires: 0');
	header ('Pragma: no-cache');
}

function doctypes() {
	$data = file('data/documents.php');
	$arr = array();
	foreach ($data as $line) {
		list($id, $title, $tpl, $parser, $inline, $file) = explode("\t", $line, 6);
		$arr[$id] = array(
			'title' => $title,
			'template' => $tpl,
			'parser' => $parser,
			'inline' => $inline,
			'remote' => $file
		);
	}
	return $arr;
}

function file2array($file) {

	$filearray = array();
	$lines = file($file);
	
	foreach ($lines as $row) {
		$row = rtrim($row);
		$row = explode("\t",$row, 2);
		$filearray[$row[0]] = $row[1];
	}

	return $filearray;
}

function invert ($int) {
	if ($int == 1) {
		$int = 0;
	}
	elseif (empty($int)) {
		$int = 1;
	}
	else {
		$int = NULL;
	}

	return $int;
}

function extract_dir($source, $realpath = true) { 
	if ($realpath) {
		$source = realpath($source);
	}
	else {
		$source = rtrim($source, '/\\');
	}
	$pos = strrpos($source, '/');
	if ($pos === false) {
		$pos = strrpos($source, '\\');
	}
	if ($pos > 0) {
		$dest = substr($source, 0, $pos+1);
	}
	else {
		$dest = '';
	}
	return $dest; 
}

/*	Delete a file, or a folder and its contents
*	@author      Aidan Lister <aidan@php.net>
*	@version     1.0.0
*	@param       string   $dirname    The directory to delete
*	@return      bool     Returns true on success, false on failure
*/
function rmdirr($dirname) {
	global $filesystem;
	if (is_file($dirname)) {
		return $filesystem->unlink($dirname);
	} 
	$dir = dir($dirname);
	while (false !== $entry = $dir->read()) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		if (is_dir("$dirname/$entry")) {
			rmdirr("$dirname/$entry");
		} 
		else {
			$filesystem->unlink("$dirname/$entry");
		}
	}  
	$dir->close(); 
	return $filesystem->rmdir($dirname);
}
/** 
 * Copy a file, or recursively copy a folder and its contents 
 * 
 * @author      Aidan Lister <aidan@php.net> 
 * @version     1.0.1 
 * @link        http://aidanlister.com/repos/v/function.copyr.php 
 * @param       string   $source    Source path 
 * @param       string   $dest      Destination path 
 * @return      bool     Returns TRUE on success, FALSE on failure 
 */ 
function copyr($source, $dest) {
	global $filesystem;
    if (is_file($source)) {
        return $filesystem->copy($source, $dest);
    }
    if (!is_dir($dest)) {
        $filesystem->mkdir($dest);
    }
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        if ($dest !== "$source/$entry") {
            copyr("$source/$entry", "$dest/$entry");
        }
    }

    $dir->close();
    return true;
}

function serverload($int = false) {
	if ($int == false) {
		$unknown = 'Unknown';
	}
	else {
		$unknown = -1;
	}
	if(strtolower(substr(PHP_OS, 0, 3)) === 'win') {
		return $unknown;
	}
	elseif(@file_exists("/proc/loadavg")) {
		$load = @file_get_contents("/proc/loadavg");
		$serverload = explode(" ", $load);
		$serverload[0] = round($serverload[0], 4);
		if(!$serverload) {
			$load = @exec("uptime");
			$load = split("load averages?: ", $load);
			$serverload = @explode(",", $load[1]);
		}
	}
	else {
		$load = @exec("uptime");
		$load = split("load averages?: ", $load);
		$serverload = @explode(",", $load[1]);
	}
	$returnload = trim($serverload[0]);
	if(!$returnload) {
		$returnload = $unknown;
	}
	return $returnload;
}

function convert2adress($url) { 
    
   $url = strtolower($url); 
    
   $find = array(' ', 
      '&quot;', 
      '&', 
      '\r\n', 
      '\n', 
      '/', 
      '\\', 
      '+', 
      '<', 
      '>'); 
   $url = str_replace ($find, '-', $url); 
    
   $find = array('�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�'); 
   $url = str_replace ($find, 'e', $url); 
    
   $find = array('�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�'); 
   $url = str_replace ($find, 'i', $url); 
    
   $find = array('�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�'); 
   $url = str_replace ($find, 'o', $url); 
    
   $find = array('�', 
       '�'); 
   $url = str_replace ($find, 'oe', $url); 
    
   $find = array('�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�'); 
   $url = str_replace ($find, 'a', $url); 
    
   $find = array('�', 
       '�'); 
   $url = str_replace ($find, 'ae', $url); 
    
   $find = array('�', 
      '�', 
      '�', 
      '�', 
      '�', 
      '�'); 
   $url = str_replace ($find, 'u', $url); 
    
   $find = array('�', 
       '�'); 
   $url = str_replace ($find, 'ue', $url); 
    
   $find = array('�'); 
   $url = str_replace ($find, 'ss', $url); 
    
   $find = array('/[^a-z0-9\-<>]/', 
      '/[\-]+/', 
      '/<[^>]*>/'); 
    
   $repl = array('', 
      '-', 
      ''); 
    
   $url =  preg_replace ($find, $repl, $url); 
    
   $url = str_replace ('--', '-', $url); 

   return $url; 
}

function is_id ($x) {
   return (is_numeric($x) && $x >= 1 ? intval($x) == $x : false);
}

function removeOldImages ($dir, $name) {
    $dir = realpath($dir);
    $dir_open = @opendir($dir);
    while (($dir_content = readdir($dir_open)) !== false) {
        if ($dir_content != '.' && $dir_content != '..') {
            $ext = get_extension($dir_content);
            $fname = str_ireplace($ext, '', $dir_content);
            if ($fname == $name) {
                @unlink($dir.'/'.$dir_content);
            }
        }
    }
    closedir($dir_open);
}

function secure_path($path) {
	global $gpc;
	$path = $gpc->secure_null($path);
	$sd = realpath($path);
	$dr = realpath($_SERVER['DOCUMENT_ROOT']);
	if (!file_exists($sd)) {
		trigger_error('File '.$sd.' does not exist!', E_USER_WARNING);
	}
	if (strpos($path, '://') !== FALSE) {
		die('Hacking attemp (Path: Protocol)');
	}
	if (strpos($sd, $dr) === FALSE && file_exists($sd)) {
		die('Hacking attemp (Path: Not in Document_Root)');
	}
	$sd = str_replace($dr, '', $sd);
	if (DIRECTORY_SEPARATOR != '/') {
		$sd = str_replace(DIRECTORY_SEPARATOR, '/', $sd);
	}
	$char = substr($sd, strlen($sd)-1, 1);
	if (!is_file($sd) && $char != '/') {
		$sd .= '/';
	}
	return $sd;
}

function check_hp($hp) {
	if (preg_match("~^https?://[a-zA-Z0-9\-\.@]+\.[a-zA-Z0-9]{1,7}(:[A-Za-z0-9]*)?/?([a-zA-Z0-9\-\.:_\?\,;/\\\+&%\$#\=\~]*)?$~i", $hp)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}
function check_mail($email) {
	global $config;
	if(preg_match("/^([_a-zA-Z���0-9-]+(\.[_a-zA-Z0-9��u-]+)*@[a-zA-Z��u0-9-]+(\.[a-zA-Z0-9���-]+)*(\.[a-zA-Z]{2,}))/si",$email)){
	    if ($config['sessionmails'] == 0) {
	    	// get the domain in lower case
	    	$domain = strstr($email, '@');
	    	$domain = substr($domain, 1);
	    	$domain = strtolower($domain);
	    	// get the known doamins in lower case
	    	$sessionmails = file('data/sessionmails.php');
	    	$sessionmails = array_map("trim", $sessionmails);
	    	$sessionmails = array_map("strtolower", $sessionmails);
			// compare the data and return the result
			if (in_array($domain, $sessionmails)) {
				return FALSE;
			}
			else {
				return TRUE;
			}
	    }
	    else {
	    	return TRUE;
	    }
	}
	else {
		return FALSE;
	}
}

function benchmarktime() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function strxlen($string) {
	$string = preg_replace('~&#([0-9]+);~', '-', $string);
	return strlen($string);
}

function random_word($laenge=8) {
    $newpass = "";
    $string="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    mt_srand((double)microtime()*1000000);

    for ($i=1; $i <= $laenge; $i++) {
        $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
    }

    return $newpass;
}

function leading_zero($int,$length=2) {
	return sprintf("%0{$length}d", $int);
}

function times ($time=FALSE) {
	global $my;

	if ($time == FALSE) {
		$stime = time();
	}
	else {
		$stime = $time;
	}

	//$retime = $stime + 3600*($my->timezone + gmdate("I"));
	// gmdate('I') has a crazy bug. it shows something else (wrong) than date('I')
	$retime = $stime + 3600*($my->timezone + date("I"));

	return $retime;

}

function str_date($format, $time=FALSE) {
	global $config, $lang;

	if ($config['new_dformat4'] == 1) {

		if ($time == FALSE) {
			$stime = times();
		}
		else {
			$stime = $time;
		}

		$today 		= times() - gmmktime (0, 0, 0, gmdate('m',$stime), gmdate('d',$stime), gmdate('Y',$stime));
		if ($today < 0) {
			$returndate = gmdate($format, $time);
		}
		elseif ($today < 86400) {
			$returndate = $lang->phrase('date_today').gmdate($lang->phrase('dformat4'), $time);
		}
		elseif ($today < 172800) {
			$returndate = $lang->phrase('date_yesterday').gmdate($lang->phrase('dformat4'), $time);
		}
		else {
			$returndate = gmdate($format, $time);
		}
	
	}
	else {
		$returndate = gmdate($format, $time);
	}
	
	return $returndate;
}

// Returns the extension ( using pathinfo() ) of an file with a leading dot (e.g. '.gif' or '.php') or not ($leading = true)
function get_extension($url, $leading=FALSE) {
	$path_parts = pathinfo($url);
	if (!isset($path_parts["extension"])) {
		$path_parts["extension"] = '';
	}
	if ($leading == TRUE) {
		return strtolower($path_parts["extension"]);
	}
	else {
		return '.'.strtolower($path_parts["extension"]);
	}
}
function UpdateBoardStats ($board) {
	global $config, $db;
	if ($config['updateboardstats'] == '1') {
		$result = $db->query("SELECT COUNT(*) FROM {$db->pre}replies WHERE board='$board'",__LINE__,__FILE__);
		$count = $db->fetch_array ($result);

		$result = $db->query("SELECT COUNT(*) FROM {$db->pre}topics WHERE board='$board'",__LINE__,__FILE__);
		$count2 = $db->fetch_array($result);
		
		$replies = $count[0]-$count2[0];
		$topics = $count2[0];

		$result = $db->query("SELECT id FROM {$db->pre}topics WHERE board = '$board' ORDER BY last DESC LIMIT 1",__LINE__,__FILE__);
	    $last = $db->fetch_array($result);
	    if (empty($last[0])) {
			$last[0] = 0;
		}
		$db->query("UPDATE {$db->pre}cat SET topics = '".$topics."', replys = '".$replies."', last_topic = '".$last[0]."' WHERE id = '".$board."'",__LINE__,__FILE__);
		$scache = new scache('cat_bid');
		$scache->deletedata();
	}
}

function BotDetection ($source, $bot, $type=FALSE) {
	foreach ($source as $spider) {
		if (stristr($bot, $spider['user_agent']) !== FALSE) {
			if ($type == TRUE) {
				return array($spider['name'], $spider['type']);
			}
			else {
				return $spider['name'];
			}
		}
	}
	if ($type == TRUE) {
		return array(false, false);
	}
	else {
		return false;
	}
}

function iif($if, $true, $false = '') {
	return ($if ? $true : $false);
}

function getip($dots = 4) {
	$ips = array();

	if (@getenv("HTTP_CLIENT_IP")) {
		$ips[] = getenv("HTTP_CLIENT_IP");
	}
	if(@getenv("HTTP_X_FORWARDED_FOR")) {
		$ips[] = getenv("HTTP_X_FORWARDED_FOR");
	}
	if(@getenv("REMOTE_ADDR")) {
		$ips[] = getenv("REMOTE_ADDR");
	}
	// sometimes for a windows server which can't handle getenv()
	if(isset($_SERVER["REMOTE_ADDR"])) {
		$ips[] = $_SERVER["REMOTE_ADDR"];
	}
	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ips[] = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	if (isset($_SERVER["HTTP_CLIENT_IP"])) {
		$ips[] = $_SERVER["HTTP_CLIENT_IP"];
	}

   	$private_ips = array("/^0\./", "/^127\.0\.0\.1/", "/^192\.168\..*/", "/^172\.16\..*/", "/^10..*/", "/^224..*/", "/^240..*/");
	$ips = array_unique($ips);

	foreach ($ips as $ip) {
		foreach ($private_ips as $pip) {
			if (!preg_match($pip, $ip)) {
				return ext_iptrim($ip, $dots);
			}
		}
	}
	if(empty($ips[0])) {
		srand((double)microtime()*1000000);
		$randval = rand(0,255);
		$ips[0] = '0.'.$r.'.'.$r.'.'.$r;
	}
	return ext_iptrim($ips[0], $dots);
}

function ext_iptrim ($text, $peaces) {
	if ($peaces > 3) {
		return $text;
	}
	$arr = explode('.', $text);
	if ($peaces > count($arr)) {
		$peaces = count($arr);
	}
	$str = '';
	for ($i = 0; $i < $peaces; $i++) {
		$str .= $arr[$i].'.';
	}
	return $str;
}

function getAge($bday) {
	$now = times();
    if (gmdate("Y", $now) == $bday[0] && gmdate("m", $now) == $bday[1] && gmdate("d", $now) == $bday[2]) {
		$result = 0;
    }
	else {
    	$result = gmdate("Y", $now) - $bday[0];
    	if ($bday[1] > gmdate("m", $now)) {
    		$result--;
    	}
    	elseif ($bday[1] == gmdate("m", $now)) {
        	if ($bday[2] > gmdate("d",$now)) {
        		$result--;
        	}
    	}
    }
    return $result;
}

function CheckForumTree($tree, &$tree2, $board) {
	foreach ($tree as $cid => $boards) {
		foreach ($boards as $bid => $sub) {
			$bdata = $board[$bid];
			if ($bdata['opt'] == 're' || !check_forumperm($bdata)) {
				//unset();
			}
	    	CheckForumTree($sub, $tree2, $board);
	    }
	}
}

function BoardSubs ($group = true) {
	$tree = cache_forumtree();
	$categories = cache_categories();
	$boards = cache_cat_bid();
	$tree2 = array();
	$forums = SelectForums(array(), $tree, $categories, $boards, $group);
	return implode("\n", $forums);
}

function SelectForums($html, $tree, $cat, $board, $group = true, $char = '&nbsp;&nbsp;', $level = 0) {
	foreach ($tree as $cid => $boards) {
		$cdata = $cat[$cid];
		if ($group) {
			$html[] = '<optgroup label="'.str_repeat($char, $level).$cdata['name'].'"></optgroup>'; //We have to close it beacuse we can not nest optgroup
		}
		else {
			$html[] = '<option style="font-weight: bold;" value="'.$cdata['id'].'">'.str_repeat($char, $level).$cdata['name'].'</option>';
		}
		$i = 0;
		foreach ($boards as $bid => $sub) {
			$bdata = $board[$bid];
			if ($bdata['opt'] == 're' || !check_forumperm($bdata)) {
				continue;
			}
			$i++;
			$html[] = '<option value="'.$bdata['id'].'">'.str_repeat($char, $level+1).$bdata['name'].'</option>';
	    	$html = SelectForums($html, $sub, $cat, $board, $group, $char, $level+2);
	    }
	    if ($i == 0) {
	    	$x = array_pop($html);
	    }
	}
	return $html;
}

// This function is simply for understanding the if-clauses better
function check_forumperm($forum) {
	global $my;
	if ($forum['opt'] == 'pw') {
		if (!isset($my->pwfaccess[$forum['id']]) || $forum['optvalue'] != $my->pwfaccess[$forum['id']]) {
			return false;
		}
		else {
			return true;
		}
	}
	elseif ($my->p['forum'] == 0) {
		if (isset($my->pb[$forum['id']]) && $my->pb[$forum['id']]['forum'] == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		if (isset($my->pb[$forum['id']]) && $my->pb[$forum['id']]['forum'] == 0) {
			return false;
		}
		else {
			return true;
		}
	}
}

/*
Params:
	(array/string)	$to		= Absender
					$to[]['name'] = Empf�ngername (opt)
					$to[]['mail'] = Empf�ngeremail
	(array/string)	$from		= Absender (opt)
					$from['name'] = Absendername (opt)
					$from['mail'] = Absenderemail (opt)
	(string)		$topic 		= Titel
	(string)		$comment 	= Inhalt
	(int)			$html 		= HTML-Modus (0/1)
	(array)			$attachment = Empf�nger (opt)
					$attachment[]['file'] = Anhang der verschickt werden soll
					$attachment[]['name'] = Dateiname f�r dei zu verschickende Datei
					$attachment[]['type'] = [path|string] (opt)
*/

function xmail ($to, $from = array(), $topic, $comment, $type='plain', $attachment = array()) {
	global $config, $my, $lang;
	
	require_once("classes/mail/class.phpmailer.php");
	require_once('classes/mail/extended.phpmailer.php');
	
	$mail = new PHPMailer();
	$mail->CharSet = $lang->phrase('charset');
	
	// Added Check_mail for better security
	// Now it is not possible to add various headers to the mail
	if (!isset($from['mail']) || !check_mail($from['mail'])) {
		$mail->From = $config['forenmail'];
	}
	else {
		$mail->From = $from['mail'];
	}
	if (!isset($from['name'])) {
		$mail->FromName = $config['fname'];
	}
	else {
		$mail->FromName = $from['name'];
	}
	if ($config['smtp'] == 1) {
		$mail->Mailer   = "smtp";
		$mail->IsSMTP();
		$mail->Host     = $config['smtp_host'];
		if ($config['smtp_auth'] == 1) {
			$mail->SMTPAuth = TRUE;
			$mail->Username = $config['smtp_username'];
			$mail->Password = $config['smtp_password'];
		}
	}
	elseif ($config['sendmail'] == 1) {
		$mail->IsSendmail();
		$mail->Mailer   = "sendmail";
		$mail->Sendmail = $config['sendmail_host'];
	}
	else {
		$mail->IsMail();
	}
	
	$mail->Subject = $topic;
	if (!is_array($to)) {
		$to = array('0' => array('mail' => $to));
	}
	$i = 0;
	foreach ($to as $email) {
		if ($type == 'bb') {
			$bbcode = initBBCodes();
			$bbcode->setSmileys(0);
			$bbcode->setReplace($config['wordstatus']);
			$row->comment = ($row->comment);
			$mail->IsHTML(TRUE);
	    	$mail->Body    = $bbcode->parse($comment);
	    	$mail->AltBody = $bbcode->parse($comment, 'plain');
		}
		elseif ($type == 'html') {
			$mail->IsHTML(TRUE);
	    	$mail->Body    = $comment;
	    	$mail->AltBody = html_entity_decode(strip_tags($comment));
	    }
	    else {
	    	$mail->Body    = html_entity_decode($comment);
	    }
	    
	    if (isset($email['name'])) {
	    	$mail->AddAddress($email['mail'], $email['name']);
	    }
	    else {
	    	$mail->AddAddress($email['mail']);
	    }
	    
	    foreach ($attachment as $file) {
	    	if ($file['type'] == 'string') {
	    		$mail->AddStringAttachment($file['file'], $file['name']);
	    	}
	    	else {
	    		$mail->AddAttachment($file['file'], $file['name']);
	    	}
	    }
	
		if ($mail->Send()) {
			$i++;
		}
		
	    $mail->ClearAddresses();
	    $mail->ClearAttachments();
	}
	return $i;
}

function getcookie($name) {
    global $config;
    if (isset($_COOKIE[$config['cookie_prefix'].'_'.$name])) {
    	return $_COOKIE[$config['cookie_prefix'].'_'.$name];
	}
	else {
		return NULL;
	}
}

function makecookie($name, $value = '', $expire = 31536000) {

	if (SCRIPTNAME == 'external') {
		return FALSE;
	}

//	if ($_SERVER['SERVER_PORT'] == '443') {
//		$secure = 1;
//	}
//	else {
//		$secure = 0;
//	}
	if ($expire != null) {
		$expire = time() + $expire;
	}
	else {
		$expire = 0;
	}
	setcookie($name, $value, $expire);
}
?>

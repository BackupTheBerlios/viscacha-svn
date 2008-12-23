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

if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

class lang {

	var $dir;
	var $dirid;
	var $file;
	var $assign;
	var $benchmark;
	var $lngarray;
	var $cache;
	var $js;

	// ToDo: Alternatives Verzeichnis f�r den Fall, dass eine ID �bergeben wurde, die nichtmehr aktiv ist...
	function lang($js = false, $level = E_USER_ERROR) {
		$this->file = '';
		$this->vars = array();
		$this->benchmark = array('all' => 0, 'ok' => 0, 'error' => 0);
		$this->lngarray = array();
		$this->cache = array();
		$this->assign = array();
		$this->js = $js;

		if ($this->js > 0) {
			$dir = $this->js;
			if (!$this->setdir($dir)) {
				die('alert("Language-Directory not found!");');
			}
		}
		else {
			global $config;
			$dir = $config['langdir'];
			if (!$this->setdir($dir)) {
				trigger_error('Language-Directory not found!', $level);
			}
		}
	}

	function init($dir = null) {
		if ($dir != null) {
			$this->setdir($dir);
		}
		$this->group('settings');
		$this->group('global');
		$this->group('modules');
		$this->group('custom');

		@ini_set('default_charset', '');
		if (!headers_sent()) {
			viscacha_header('Content-type: text/html; charset='.$this->charset());
		}

		global $slog;
		if (isset($slog) && is_object($slog) && method_exists($slog, 'setlang')) {
			$slog->setlang($this->phrase('fallback_no_username'), $this->phrase('timezone_summer'));
		}
		global $config, $breadcrumb;
		if (isset($breadcrumb)) {
			$isforum = array('addreply','attachments','edit','forum','manageforum','managetopic','misc','newtopic','pdf','search','showforum','showtopic');
			if ($config['indexpage'] != 'forum' && in_array(SCRIPTNAME, $isforum)) {
				$breadcrumb->Add($this->phrase('forumname'), iif(SCRIPTNAME != 'forum', 'forum.php'));
			}
		}
	}

	function initAdmin($dir = null) {
		global $admconfig, $my;
		if (!empty($my->settings['default_language'])) {
			$dir = $my->settings['default_language'];
		}
		elseif (is_id($admconfig['default_language'])) {
			$dir = $admconfig['default_language'];
		}
		if ($dir != null) {
			$this->setdir($dir);
		}
		$this->group('settings');
		$this->group('admin/global');
		$this->group('modules');
		$this->group('custom');

		@ini_set('default_charset', '');
		if (!headers_sent()) {
			viscacha_header('Content-type: text/html; charset='.$this->charset());
		}
	}

	function javascript($file = 'javascript') {
		@require($this->get_path($file));
		if (isset($lang) && is_array($lang)) {
			$str = 'var lng = new Array();'."\n";
			foreach ($lang as $k => $l) {
				$l = str_replace("'", "\\'", $l);
				$str .= "lng['{$k}'] = '{$l}';\n";
			}
			return $str;
		}
		else {
			return false;
		}
	}

	function return_array($group = '') {
		if (!empty($group)) {
			@require($this->get_path($group));
			if (isset($lang) && is_array($lang)) {
				return $lang;
			}
		}
		trigger_error('Array from language file can\'t be returned.', E_USER_NOTICE);
		return false;
	}

	function charset() {
		if (empty($this->lngarray['charset'])) {
			global $config;
			return $config['asia_charset'];
		}
		else {
			return $this->lngarray['charset'];
		}
	}

	function get_mail($file) {
		global $gpc;
		$this->benchmark['all']++;
		$this->file = $this->get_path(array('mails', $file));
		if (file_exists($this->file) == false) {
		    $this->benchmark['error']++;
			return false;
		}
        $this->benchmark['ok']++;
        $content = file_get_contents($this->file);
        preg_match("|<title>(.+?)</title>.*?<comment>(.+?)</comment>|is", $content, $matches);
		$matches[1] = $this->parse_pvar($matches[1]);
		$matches[2] = $this->parse_pvar($matches[2]);
        return array(
        	'title' => $gpc->plain_str($matches[1]),
        	'comment' => $gpc->plain_str($matches[2])
        );
	}

	function get_text($file) {
		$this->benchmark['all']++;
		$this->file = $this->get_path(array('texts', $file));
		if (file_exists($this->file) == false) {
		    $this->benchmark['error']++;
			return false;
		}
        $this->benchmark['ok']++;
        $content = file_get_contents($this->file);
		$content = $this->parse_pvar($content);
        return $content;
	}

	function get_words($file = 'search') {
		$this->file = $this->get_path(array('words', $file));
		if (file_exists($this->file) == false) {
			return array();
		}
        $arr = file($this->file);
		$arr = array_map('trim', $arr);
        return $arr;
	}

	function group($group) {
		$this->file = $this->get_path($group);
		if (file_exists($this->file) && !isset($this->cache[$this->file])) {
			@include($this->file);
			if (isset($lang) && is_array($lang)) {
				$this->lngarray += $lang;
				$this->cache[$group] = true;
			}
			else {
				echo "<!-- Could not parse language-file {$file} -->";
			}
		}
		else {
			echo "<!-- Could not load language-file {$file} -->";
		}
	}

	function phrase($phrase) {
		if (isset($this->lngarray[$phrase])) {
			$pphrase = $this->lngarray[$phrase];
			if (strpos($pphrase, '{') !== false) {
        		$pphrase = $this->parse_pvar($pphrase);
			}
			return $pphrase;
		}
		else {
			return $phrase;
		}
	}

	function assign($key, $val) {
		$this->assign[$key] = $val;
	}

	function parse_pvar($content) {
		return preg_replace('#\{(\$|\%|\@)(.+?)\}#ie', "\$this->parse_variable('\\2','\\1')", $content);
	}

	function parse_variable($key, $type) {
		if ($type == '%') {
			$keys = explode('->',$key);
			if (isset($this->assign[$keys[0]]->$keys[1])) {
				$var = $this->assign[$keys[0]]->$keys[1];
				return $var;
			}
			elseif(isset($GLOBALS[$keys[0]]->$keys[1])) {
				return $GLOBALS[$keys[0]]->$keys[1];
			}
		}
		elseif ($type == '@') {
			$keys = explode('->',$key);
			if (isset($keys[2])) {
				if (isset($this->assign[$keys[0]][$keys[1]][$keys[2]])) {
					$var = $this->assign[$keys[0]][$keys[1]][$keys[2]];
					return $var;
				}
				elseif(isset($GLOBALS[$keys[0]][$keys[1][$keys[2]]])) {
					return $GLOBALS[$keys[0]][$keys[1]][$keys[2]];
				}
			}
			else {
				if (isset($this->assign[$keys[0]][$keys[1]])) {
					$var = $this->assign[$keys[0]][$keys[1]];
					return $var;
				}
				elseif(isset($GLOBALS[$keys[0]][$keys[1]])) {
					return $GLOBALS[$keys[0]][$keys[1]];
				}
			}
		}
		else {
			if (isset($this->assign[$key])) {
				$var = $this->assign[$key];
				return $var;
			}
			elseif(isset($GLOBALS[$key])) {
				return $GLOBALS[$key];
			}
		}
		return "{{$type}{$key}}"; // Not found. Don't change anything!
	}

	function setdir($dirId) {
		global $config;
		if ($dirId < 1) {
			$dirId = $config['langdir'];
		}

		$dir = "language/{$dirId}";
		if (@is_dir($dir) == false) {
			$dir = "{$config['fpath']}/language/{$dirId}";
			if (@is_dir($dir) == false) {
				$dir = extract_dir(dirname(__FILE__));
				$dir = "{$dir}/language/{$dirId}";
			}
		}

		$dir = realpath($dir);

		if (file_exists($dir)) {
			$this->dirid = $dirId;
			$this->dir = $dir;
			return true;
		}
		else {
			return false;
		}
	}

	function getdir($id = false) {
		if ($id == true) {
			return $this->dirid;
		}
		else {
			return $this->dir;
		}
	}

	function get_path($name, $ext = 'php') {
		if (is_array($name)) {
			$name = implode(DIRECTORY_SEPARATOR, $name);
			$ext = 'php';
		}
		else {
			$ext = 'lng.php';
		}
		$this->file = $this->dir.DIRECTORY_SEPARATOR.$name.'.'.$ext;
		return $this->dir.DIRECTORY_SEPARATOR.$name.'.'.$ext;
	}

	function group_is_loaded($group) {
		if (isset($this->cache[$group]) && $this->cache[$group] == true) {
			return true;
		}
		else {
			return false;
		}
	}

}
?>
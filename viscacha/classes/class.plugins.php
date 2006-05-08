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

if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "class.plugins.php") die('Error: Hacking Attempt');

class PluginSystem {

	var $cache;
	var $pos;
	var $sqlcache;
	
	function PluginSystem() {
		$this->cache = array();
		$this->pos = array();
		$this->sqlcache = null;
	}

	function load($pos) {
		$group = $this->_group($pos);
		$this->_load_group($pos);
		if (isset($this->cache[$group][$pos])) {
			return $this->cache[$group][$pos];
		}
		else {
			return '';
		}
	}
	
	function navigation() {
		return '';
	}
	
	function _load_group($pos) {
		$group = $this->_group($pos);
		$file = 'cache/modules/'.$group.'.php';
		
		if (file_exists($file) == true) {
			$code = file_get_contents($file);
			$code = unserialize($code);
		}
		else {
			$code = $this->_build_code($pos);
		}
		$this->cache[$group] = $code;
	}
	
	function _build_code($pos) {
		global $myini, $db;
		$group = $this->_group($pos);
		$file = 'cache/modules/'.$group.'.php';

		if ($this->sqlcache == null) {
			$this->sqlcache = array();
			$this->sqlcache[$group] = array();
	        $result = $db->query("SELECT module, position FROM {$db->pre}plugins WHERE active = '1' ORDER BY ordering",__LINE__,__FILE__);
	        while ($row = $db->fetch_assoc($result)) {
	        	$row['group'] = $this->_group($row['position']);
	            $this->sqlcache[$row['group']][$row['position']][] = $row['module'];
	        }
	    }
	    if (!isset($this->sqlcache[$group])) {
	    	$this->sqlcache[$group] = array();
	    }

	    $cfgdata = array();
	    $code = array();
	    foreach ($this->sqlcache[$group] as $position => $mods) {
	    	$code[$position] = '';
	    	foreach ($mods as $plugin) {
	    		if (!isset($cfgdata[$plugin])) {
		    		$inifile = 'modules/'.$plugin.'/config.ini';
		    		$cfgdata[$plugin] = $myini->read($inifile);
	    		}
	    		if (isset($cfgdata[$plugin]['php'])) {
		    		foreach ($cfgdata[$plugin]['php'] as $phpposition => $phpfile) {
		    			if ($position == $phpposition) {
				    		$sourcefile = 'modules/'.$plugin.'/'.$phpfile;
				    		if (file_exists($sourcefile)) {
					    		$source = file_get_contents($sourcefile);
					    		$code[$position] .= '$pluginid = "'.$plugin.'";'."\r\n".$source."\r\n";
				    		}
			    		}
			    	}
	    		}
	    	}
	    }

		$save = serialize($code);		
		$save = file_put_contents($file, $save);
		
		return $code;
	}
	
	function _group($pos) {
		$offset = strpos ($pos, '_');
		if ($offset === false) {
			return $pos;
		}
		else {
			return substr($pos, 0, $offset);
		}
	}
	
	function _check_permissions($groups) {
	    global $slog;
	    if ($groups == 0 || count(array_intersect(explode(',', $groups), $slog->groups)) > 0) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}

}
?>
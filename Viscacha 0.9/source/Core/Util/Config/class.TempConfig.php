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
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadInterface('Core.Util.Config.ConfigHandler');
Core::loadClass('Core.Util.Config.PHPConfig');

/**
 * Implementation of a temporary config.
 *
 * This config is valid only during the page request and all data will be lost afterwards.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class TempConfig extends PHPConfig implements ConfigHandler {

	public function __construct() {
		$this->data = array();
	}

	public function create() {
		if (!is_array($this->data)) {
			$this->data = array();
		}
		return true;
	}

	public function load() {
		$this->data = array();
		return true;
	}

	public function save() {
		// Nothing to do for temporary data
		return true;
	}

}
?>
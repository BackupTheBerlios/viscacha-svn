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
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Token implementation for the BBCodeCompiler.
 *
 * @package		Core
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class BBCodeToken {

	private $tagObject;
	private $text;
	private $tagName;
	private $openingTag;
	private $attributes;
	private $childs;
	private $closingToken;
	
	public function __construct($text, $tagName, $isOpeningTag) {
		$this->tagObject = null;
		$this->text = $text;
		$this->tagName = strtolower($tagName);
		$this->openingTag = (boolean) $isOpeningTag;
		$this->attributes = array();
		$this->childs = array();
		$this->closingToken = null;
	}

	public function isValidChild($child) {
		$forbidden = $this->tagObject->getDisallowedChilds($this->tagName);
		if (count($forbidden) == 0) {
			// Nothing forbidden, everything is allowed
			return true;
		}

		// There are restrictions...
		$exceptions = $this->tagObject->getDisallowedChildsExceptions($this->tagName);

		// Check if text is allowed
		if (is_string($child) == true) {
			$isForbidden = in_array(BBCodeTag::DT_TEXT, $forbidden, true);
			$isAllForbidden = in_array(BBCodeTag::DT_ALL, $forbidden, true);
			$isException = in_array(BBCodeTag::DT_TEXT, $exceptions, true);

			return !($isForbidden || ($isAllForbidden && !$isException));
		}

		// Check if the given token is allowed
		if ($child instanceof BBCodeToken) {
			// Check if the display type is allowed or not
			$displayType = $child->getDisplayType();

			$isForbidden = in_array($displayType, $forbidden, true);
			$isAllForbidden = in_array(BBCodeTag::DT_ALL, $forbidden, true);
			$isException = in_array($displayType, $exceptions, true);

			if ($isForbidden || ($isAllForbidden && !$isException)) {
				return false;
			}

			// Check if the tag is allowed
			$tagName = $child->getTagName();

			$isForbidden = in_array($tagName, $forbidden, true);
			$isAllForbidden = in_array(BBCodeTag::DT_ALL, $forbidden, true);
			$isException = in_array($tagName, $exceptions, true);

			return !($isForbidden || ($isAllForbidden && !$isException));
		}

		// Default action: Reject other things
		return false;
	}

	public function setClosingToken(BBCodeToken $token) {
		$this->closingToken = $token;
	}

	public function getClosingToken() {
		return $this->closingToken;
	}

	public function getTagName() {
		return $this->tagName;
	}

	public function addChild($child) {
		$this->childs[] = $child;
	}

	public function getChilds() {
		return $this->childs;
	}

	public function toText($childsOnly = false) {
		$text = '';
		if ($childsOnly == false) {
			$text .= $this->getOriginalText();
		}
		foreach ($this->childs as $child) {
			if (is_string($child)) {
				$text .= $child;
			}
			elseif($child instanceof BBCodeToken) {
				$text .= $child->toText();
			}
		}
		$closingToken = $this->getClosingToken();
		if ($childsOnly == false && $closingToken !== null) {
			$text .= $closingToken->getOriginalText();
		}
		return $text;
	}

	public function getOriginalText() {
		return $this->text;
	}

	public function isOpeningTag() {
		return $this->openingTag;
	}
	
	public function setAttribute($key, $value) {
		$this->attributes[$key] = $value;
	}
	
	public function getAttribute($key) {
		if (isset($this->attributes[$key])) {
			return $this->attributes[$key];
		}
		else {
			return null;
		}
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function setTagObject(BBCodeTag $tag) {
		$this->tagObject = $tag;
	}
	
	public function getTagObject() {
		return $this->tagObject;
	}

	public function isStandalone() {
		return $this->tagObject->isStandalone($this->tagName);
	}

	public function getDisplayType() {
		return $this->tagObject->getDisplayType($this->tagName);

	}

}
?>
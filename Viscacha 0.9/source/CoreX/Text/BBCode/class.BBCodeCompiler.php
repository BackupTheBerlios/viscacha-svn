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
 * Extensible BB-Code-Parser (Compiler).
 *
 * You can have a maximum of 3 attributes: [tagname=x attrnametwo=y attrnamethree=z].
 * The attribute names and the tag name can contain alphabetic chars only.
 *
 * Valid Examples:
 * <code<
 * [note='Explain the CPU [\'xyz\']' abbr=CPU]Central Processing Unit[/note]
 * [example="" else='\\'][/example]
 * [b]Hi guys[/b]
 * </code>
 *
 * @package		Core
 * @subpackage	Text
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class BBCodeCompiler {

	private $tags;
	private $filter;

	public function  __construct() {
		$this->tags = array();
		$this->filter = array(
			BBCodeFilter::TEXT => array(),
			BBCodeFilter::PRE => array(),
			BBCodeFilter::POST => array()
		);
	}

	public function registerFilter(BBCodeFilter $filter, $type = null) {
		if ($type === null) {
			$type = $filter->getType();
		}
		$this->filter[$type][] = $filter;
	}

	public function registerTag(BBCodeTag $object, $tagName) {
		$tags = $object->getTagNames();
		$tagName = strtolower($tagName);
		if (in_array($tagName, $tags) == true) {
			$object->setCompiler($this);
			$this->tags[$tagName] = $object;
		}
	}

	public function registerTags(BBCodeTag $object) {
		$object->setCompiler($this);
		foreach ($object->getTagNames() as $tag) {
			$tag = strtolower($tag);
			$this->tags[$tag] = $object;
		}
	}

	public function convert($text) {
		// Execute prepending filters
		$text = $this->executeFilter($text, BBCodeFilter::PRE);
		// Convert the text to tokens (lexing)
		$tokens = $this->executeScanner($text);
		// Start parsing process using a root node to get a tree
		$tokens = $this->executeParser($tokens, new BBCodeToken('', 'viscacha_root_node', true));
		// Compile the tree to text
		$text = $this->compile($tokens, BBCodeTag::DT_ALL);
		// Execute appending filters
		return $this->executeFilter($text, BBCodeFilter::POST);
	}

	public function compile(BBCodeToken $token, $type) {
		return $this->executeCompiler($token, $type);
	}

	private function executeFilter($text, $type) {
		foreach ($this->filter[$type] as $filter) {
			$text = $filter->compile($text);
		}
		return $text;
	}

	/**
	 * Tokenize the text into peaces containing the tags (opening or closing) and the texts.
	 *
	 * This will also repair malformed bb-code strings.
	 *
	 * @param string Text to tokenize
	 * @return array Array containing the tokens
	**/
	private function executeScanner($text) {
		if (strpos($text, '[') === false) {
			// If no [ char then there is no bb code,. shorten the procedure / save cpu
			return array($text);
		}
		else {
			// Build regexp
			$attributeValue = '(\'.+?(?<!(?<!\\\\)\\\\)\'|".+?(?<!(?<!\\\\)\\\\)"|[^\]\s]+)';
			$attribute = '(?:\s+([a-z]+)='.$attributeValue.')?';
			$regexp = '~\[(?:([a-z]+)(?:='.$attributeValue.')?'.$attribute.$attribute.'|/[a-z]+)\]~i';
			// Parse the tags (matches only tags, not the texts)
			preg_match_all($regexp, $text, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);

			// Iterate through tags and add missing texts
			$tokens = array();
			$lastOffset = 0;
			// Stacks to repair the code
			$inlineStack = array();
			$blockStack = array();
			// We use our own index for the array to have an ability to remove stack elements by key
			$tokenIndex = 0;

			foreach ($matches as $match) {
				// Calculate the length of the missing text (difference of the both tag offsets)
				$length = $match[0][1] - $lastOffset;
				// Add missing text when there is something missing
				if ($length > 0) {
					$tokens[$tokenIndex++] = substr($text, $lastOffset, $length);
				}

				// We decide what type of token it is on the number of array elements / matches
				$type = count($match);
				// Add token with additional information from the scanning process
				if ($type == 1) { // Its a closing tag
					$tagName = strtolower(substr($match[0][0], 2, -1));
					if (isset($this->tags[$tagName])) {
						// Create new token, remove the [/ and ] for the tag name
						$token = new BBCodeToken($match[0][0], $tagName, false);
						$token->setTagObject($this->tags[$tagName]);
					}
					else {
						// Tag is not registered, handle it as text
						$token = $match[0][0];
					}
				}
				else {
					$tagName = strtolower($match[1][0]);
					if (isset($this->tags[$tagName])) {
						// Add new opening tag with attributes
						$token = new BBCodeToken($match[0][0], $tagName, true);
						$token->setTagObject($this->tags[$tagName]);
						switch ($type) {
							case 7:
								$token->setAttribute($match[5][0], $match[6][0]);
							case 5:
								$token->setAttribute($match[3][0], $match[4][0]);
							case 3:
								$token->setAttribute($match[1][0], $match[2][0]);
						}
					}
					else {
						// Tag is not registered, handle it as text
						$token = $match[0][0];
					}
				}

				// Start repairing malformed bb code
				if ($token instanceof BBCodeToken && $token->isStandalone($tagName) == false) {
					// Tag is not standalone, go through them, save standalone tags without change
					if ($token->getDisplayType() == BBCodeTag::DT_BLOCK) {
						// Close all unclosed tags before the closing block element
						$lastKey = count($inlineStack) -1;
						for ($i = $lastKey; $i >= 0; $i--) {
							$closingTagName = $inlineStack[$i]->getTagName();
							$element = new BBCodeToken(
								"[/{$closingTagName}]",
								$closingTagName,
								false
							);
							$element->setTagObject($this->tags[$closingTagName]);
							$tokens[$tokenIndex++] = $element;
						}
						// Reset stack as all tags are closed now
						$inlineStack = array();

						// Handle tags, add to stack, remove from stack etc.
						if ($token->isOpeningTag() == true) {
							// Add opening tag to block element stack with current index that is
							// used also when adding current token
							$blockStack[$tokenIndex] = $tagName;
						}
						else {
							// Check on top of stack for a matching opening tag
							if (end($blockStack) == $tagName) {
								// Opening tag found, remove it from the stack
								array_pop($blockStack);
							}
							else {
								// remove the token, token doesn't match stack content
								$token = null;
							}
						}
					}
					else {
						// Handle tags, add to stack, remove from stack etc.
						if ($token->isOpeningTag() == true) {
							// Add opening tag to stack
							$inlineStack[] = $token;
						}
						else {
							// Check whether there is a opening tag for the closing tag
							// Order can be swapped for inline tags, nestin will be repaired.
							// Example: [b]a[i]b[/b]c[/i] will be [b]a[i]b[/i][/b][i]c[/i]
							$lastKey = count($inlineStack) - 1;
							$key = null;
							for ($i = $lastKey; $i >= 0; $i--) {
								if ($inlineStack[$i]->getTagName() == $tagName) {
									$key = $i;
									break;
								}
							}
							if ($key === null) {
								// No opening tag found, remove the closing tag
								$token = null;
							}
							else {
								// Closes the opened tags until the key.
								for ($i = $lastKey; $i > $key; $i--) {
									// Close the tags
									$closingTagName = $inlineStack[$i]->getTagName();
									$element = new BBCodeToken(
										"[/{$closingTagName}]",
										$closingTagName,
										false
									);
									$element->setTagObject($this->tags[$tagName]);
									$tokens[$tokenIndex++] = $element;
								}
								// Add the current closing tag already after the closed elements and
								// set token to null so it is not added again at end of the loop...
								$tokens[$tokenIndex++] = $token;
								$token = null;
								// Open the tags again as we are notbehind the current closing tag
								for ($i = $lastKey; $i > $key; $i--) {
									// We need to clone it or the childs will be duplicated too
									$tokens[$tokenIndex++] = clone $inlineStack[$i];
								}
								// Opening tag found, remove it from the stack and repair the
								// nesting if needed.
								unset($inlineStack[$key]);
								// Reindex the key of the inline stack
								$inlineStack = array_merge($inlineStack);
							}
						}
					}
				}

				if ($token !== null) {
					// Append the token to the current index
					$tokens[$tokenIndex++] = $token;
				}

				// Change the lastOffset to the current offset plus the length of the tag
				$lastOffset = $match[0][1] + strlen($match[0][0]);
			}

			// Append the rest of the text after the last tag
			$tokens[$tokenIndex++] = substr($text, $lastOffset);

			// Close all open tag at the end of the text in order of their occurance.
			$inlineStack = array_reverse($inlineStack);
			foreach ($inlineStack as $element) {
				$closingTagName = $element->getTagName();
				$element = new BBCodeToken(
					"[/{$closingTagName}]",
					$closingTagName,
					false
				);
				$element->setTagObject($this->tags[$closingTagName]);
				$tokens[$tokenIndex++] = $element;
			}
			// Remove open but unclosed block elements from tree
			foreach ($blockStack as $key => $element) {
				unset($tokens[$key]);
			}

			return $tokens;
		}
	}

	/**
	 * Parses the tokens generated by executeScanner() and generates a tree
	 *
	 * @param array $tokens
	 * @param BBCodeToken $currentToken
	 * @return BBCodeToken Root node containing all nodes of the tree
	 */
	private function executeParser(array &$tokens, BBCodeToken $currentToken) {
		if ($currentToken->getTagName() === 'viscacha_root_node') {
			// This is the root node, reset the array pointer for $tokens
			reset($tokens);
		}

		// Use while as foreach will reset the array pointer on each call
		while (list(, $token) = each($tokens)) {
			if (is_string($token) == true) {
				// It' a string, add it to the current token as child
				$currentToken->addChild($token);
			}
			elseif ($token instanceof BBCodeToken) {
				// Get the tag object for this Token
				if ($token->getDisplayType() == BBCodeTag::DT_BLOCK) {
					if ($token->isStandalone() == true) {
						// Add standalone tags to the current token (can't have childs => no recursion)
						$currentToken->addChild($token);
					}
					elseif ($token->isOpeningTag() == true) {
						$currentToken->addChild($this->executeParser($tokens, $token));
					}
					else {
						$currentToken->setClosingToken($token);
						break;
					}
				}
				else { // Inline Tag
					if ($token->isStandalone() == true) {
						// Add standalone tags to the current token (can't have childs => no recursion)
						$currentToken->addChild($token);
					}
					elseif ($token->isOpeningTag() == true) {
						$currentToken->addChild($this->executeParser($tokens, $token));
					}
					else {
						$currentToken->setClosingToken($token);
						break;
					}
				}
			}
		}

		return $currentToken;
	}

	private function executeCompiler(BBCodeToken $parent) {
		$text = '';
		$childs = $parent->getChilds();
		foreach ($childs as $child) {
			if ($parent->getTagName() != 'viscacha_root_node' && $parent->isValidChild($child) == false) {
				if ($child instanceof BBCodeToken) {
					// Invalid child, add as text only
					$text .= $child->totext();
				}
				// Else: Invalid child, remove it (applies also on strings)
			}
			else {
				if (is_string($child)) {
					// Valid child, add the text
					$text .= $child;
				}
				elseif ($child instanceof BBCodeToken) {
					// Valid child, add the compiled token
					$text .= $child->getTagObject()->compile($child);
				}
			}
		}
		return $text;
	}

}
?>
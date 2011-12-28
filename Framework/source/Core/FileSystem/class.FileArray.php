<?php
/**
 * Array to File and reverse.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 * @static
 */
class FileArray {

	/**
	 * Object of tyoe File pointing to the file.
	 * @var File
	 */
	private $file;
	/**
	 * Name of the variable that will be used to get and save the file.
	 * @var string
	 */
	private $varname;

	/**
	 * Constructs a new FileArray object for the specified filename.
	 *
	 * The second parameter only allows characters that are alphabetic. All other characters will be removed.
	 *
	 * @param File
	 * @param string
	 */
	function __construct(File $file, $varname = '') {
		$varname = preg_replace('/[^a-zA-Z]+/i', '', $varname);
		if (empty($varname) == true) {
			$varname = 'config';
		}
		$this->varname = $varname;

		$this->file = $file;
	}

	/**
	 * Returns the content of the array in the file.
	 *
	 * If no array with the specified name exists, an empty array will be returned.
	 *
	 * @return array Array read from the file.
	 */
	function get() {
		if ($this->file->exists() == true) {
			@include($this->file->relPath());
		}
		if (isset(${$this->varname}) == false) {
			${$this->varname} = array();
		}
		return ${$this->varname};
	}

	/**
	 * Saves the array to a file.
	 *
	 * The file can be included. It is a valid php file containing one array with the specified name.
	 * The array can be multidimensional, but the array(s) can only contain strings and integer.
	 *
	 * @param array Array to save to file.
	 * @todo Use var_export()
	 */
	function save($array) {
		ksort($array); // Not needed, but looks better ;-)

		$lines = array();
		$lines[] = '<?php';
		$lines[] = '$'.$this->varname.' = array();';

		$stack = Core::constructObject('Core.Util.Structures.Stack');
		$this->addArrayElement($lines, $stack, $array);

		$lines[] = '?>';

		$this->file->writeArray($lines);
	}

	/**
	 * Recursive function that produes the lines that will define the array in the file.
	 *
	 * @param array Reference to the lines that should be written later.
	 * @param Stack Reference to a stack
	 * @param mixed Element(s) to add to the array
	 */
	private function addArrayElement(&$lines, &$stack, $element) {
		if (is_array($element) == true) {
			foreach ($element as $key => $value) {
				$stack->push($key);
				$this->addArrayElement($lines, $stack, $value);
				$stack->pop();
			}
		}
		else {
			$cats = implode("']['", $stack->getArray());
			$element = $this->prepareString($element);
			$lines[] = '$'.$this->varname."['{$cats}'] = {$element};";
		}
	}

	/**
	 * Method that acts as a callback for FileArray::prepareString().
	 *
	 * It escapes the new lines and carriage returns.
	 *
	 * @see FileArray::prepareString()
	 * @param string String to escape
	 * @return string Escaped string
	 */
	private function escapeNewline($nl) {
		$nl = str_replace(array("\r\n", "\n", "\r"), '\\r\\n', $nl[1]);
		$nl = str_replace("\t", '\\t', $nl);
		$str = "'.\"".$nl."\".'";
		return $str;
	}

	/**
	 * Escapes a string.
	 *
	 * After the string has been escaped it can be used as value for an array element.
	 * If the parameter is an integer it will be returned as is.
	 *
	 * Example:
	 * <code>
	 * $string = '<?php $variable = '.$this->parseString("Wie geht's euch?\nI'm fine!").'; ?>';
	 * file_put_contents('myConfigFile.txt', $string);
	 * // myConfigFile.txt would be:
	 * // <?php $variable = 'Wie geht\'s euch?'."\r\n".'I\'m fine!'; ?>
	 * </code>
	 *
	 * @param mixed Integer or string that should be escaped.
	 * @return mixed Escaped string
	 */
	private function prepareString($val2) {
		if (is_int($val2) == true) {
			return $val2;
		}
		else {
			$val2 = str_replace(array("\0", '\\', "'"), array('', '\\\\', "\\'"), $val2);
			$val2 = preg_replace_callback("/((\r\n|\n|\r|\t)+)/s", array(&$this, 'escapeNewline'), $val2);
			return "'{$val2}'";
		}
	}

}

?>

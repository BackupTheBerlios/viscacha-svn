<?php
/**
 * Abstract class for Exceptions that have additional information.
 *
 * @package		Core
 * @author		Matthias Mohr
 * @since 		1.0
 */
abstract class ExceptionData extends Exception {

	protected static $objInExc;

	/**
	 * Constructs the Exception.
	 *
	 * @param string Error message
	 * @param int Error number (default: 0)
	 */
	public function __construct($message, $code = 0) {
		 if (self::$objInExc instanceof self) {
	     	self::$objInExc->hardExit();
		 }
		 self::$objInExc = $this;
		 parent::__construct($message, $code);
	}

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * The array can contain as many details as you want.
	 * The keys of the elements are the labels/titles and the values is the variable data.
	 *
	 * @return array Data with keys as labels and values as data.
	 */
	public function getData() {
		return array();
	}

	protected function hardExit() {
		die('CRITICAL SERVER ERROR: '.$this->getMessage().' (File: '.$exception->getFile().', Line :'.$exception->getLine().')');
	}

}
?>

<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../../../../source/Core/Util/DataTypes/class.Strings.php';

class StringsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider providerTrimLineBreaks
	 */
	public function testTrimLineBreaks($param1, $param2, $expected) {
		$result = Strings::trimLineBreaks($param1, $param2);
		$function = ($param2 == true) ? 'rtrim' : 'trim';
		$this->assertEquals($expected, $result, "Failed to {$function}: ".var_export($param1, true));
	}

	public function providerTrimLineBreaks() {
		return array(
			array("Test", true, "Test"),
			array("\nTest\rTest\n\r\n", true, "\nTest\rTest"),
			array("\r\n\r", true, ""),
			array(" \r\n\t", true, " \r\n\t"),
			array("\n\n\n\n\n\nn", true, "\n\n\n\n\n\nn"),
			array('\r\nTest\r\n', true, '\r\nTest\r\n'),
			array("\\r\\n\r\n\\r\\n", true, "\\r\\n\r\n\\r\\n"),
			array("Test", false, "Test"),
			array("\nTest\rTest\n\r\n", false, "Test\rTest"),
			array("\r\n\r", false, ""),
			array(" \r\n\t", false, " \r\n\t"),
			array("\n\n\n\n\n\nn", false, "n"),
			array('\r\nTest\r\n', false, '\r\nTest\r\n'),
			array("\\r\\n", false, "\\r\\n"),
			array('', true, '')
		);
	}

	/**
	 * @dataProvider providerReplaceLineBreaks
	 */
	public function testReplaceLineBreaks($param_string, $param_replace, $expected) {
		$result = Strings::replaceLineBreaks($param_string, $param_replace);
		$this->assertEquals(
			$expected,
			$result,
			"Could not properly replace line breaks with ".var_export($param_replace, true).
				" in string ".var_export($param_string, true)
		);
	}

	public function providerReplaceLineBreaks() {
		return array(
			array("Test\rTest", "\r\n\r\n", "Test\r\n\r\nTest"),
			array('Test\r\nTest', "\t", 'Test\r\nTest'),
			array("Test\r\nTest\r\nTest", "\t", "Test\tTest\tTest"),
			array("\r \n \r\n \n\r", 0, "0 0 0 00"),
			array('', '', ''),
			array("\\r\\n", '-', "\\r\\n")
		);
	}

}
?>
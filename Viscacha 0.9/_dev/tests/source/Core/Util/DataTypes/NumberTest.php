<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../../../../source/Core/Util/DataTypes/class.Number.php';

class NumberTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider providerIsNatural
	 */
	public function testIsNatural($value, $expected) {
		$result = Number::isNatural($value);
		$this->assertEquals($expected, $result, "Given: {$value}; Result: ".var_export($result, true));
	}

	public function providerIsNatural() {
		return array(
			array(0, false),
			array("", false),
			array(false, false),
			array(true, false),
			array(2.5, false),
			array(-2, false),
			array(1, true),
			array(99, true),
			array("1", true),
			array("01", false),
			array(floatval(1.0), false),
			array("a1b", false),
			array(0x1a, true),
			array("0x12", false),
			array(null, false),
			array(0123, true),
			array(PHP_INT_MAX, true),
			// This should be always greater than PHP_INT_MAX
			// and we care also about differences with different int sizes on 64bit/32bit etc.
			array(str_repeat('9', strlen(PHP_INT_MAX)), true),
			array("+77", false),
			array("+0123.45e6", false),
			array("50e3", false),
			array("1.0", false)
		);
	}

	/**
	 * @dataProvider providerIsInteger
	 */
	public function testIsInteger($value, $expected) {
		$result = Number::isInteger($value);
		$this->assertEquals($expected, $result, "Given: {$value}; Result: ".var_export($result, true));
	}

	public function providerIsInteger() {
		return array(
			array(0, true),
			array("", false),
			array(false, false),
			array(true, false),
			array(2.5, false),
			array(-2, true),
			array(1, true),
			array(99, true),
			array("1", true),
			array("01", true),
			array(floatval(1.0), false),
			array("a1b", false),
			array(0x1a, true),
			array("0x12", false),
			array(null, false),
			array(0123, true),
			array(PHP_INT_MAX, true),
			array(str_repeat('9', strlen(PHP_INT_MAX)), true), // Always greater than PHP_INT_MAX
			array('-'.str_repeat('9', strlen(PHP_INT_MAX)), true), //Always smaller than PHP_INT_MAX
			array("+77", true),
			array("+0123.45e6", false),
			array("-0", true),
			array("1.0", false)
		);
	}

	/**
	 * @dataProvider providerLeadingZero
	 */
	public function testLeadingZero($value, $leading, $expected) {
		$this->assertEquals($expected, Number::leadingZero($value, $leading));
	}

	function providerLeadingZero() {
		return array(
			array(0, 2, "00"),
			array(1, 2, "01"),
			array(10, 2, "10"),
			array(100, 2, "100"),
			array(0, 0, "0"),
			array(0, 1, "0"),
			array(0, 2, "00"),
			array(10, 10, "0000000010"),
			array(77, -4, "0077"),
			array(2, -1, "2"),
			array(PHP_INT_MAX, strlen(PHP_INT_MAX)+1, "0".PHP_INT_MAX),
			array(1.1, 3, "1.1"),
			array("99", 4, "0099"),
			array("99", 2, "99"),
			array("a1c", 5, "a1c"),
			array(1.1, 1.1, 1.1),
			array("a", "b", "a"),
			array(-5, 3, "-005"),
			array("", 0, "")
		);
	}
}
?>

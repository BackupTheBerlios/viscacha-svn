<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../../../../../source/Core/Util/DataTypes/class.Arrays.php';

class ArraysTest extends PHPUnit_Framework_TestCase {

	private $xPathData = array(
		'red' => array(
			'max' => 100,
			'min' => 0
		),
		'blue' => 'water',
		'green' => 'flowers',
		'grey' => array(
			'light' => array('eeeeee', 'e7e7e7'),
			'dark' => array('cccccc')
		),
		7 => 'foobar'
	);

	/**
	 * @dataProvider providerXPath
	 */
	public function testXPath($path, $expected_return, $expected_result) {
		$return = Arrays::xPath($this->xPathData, $path, $result);
		$this->assertEquals(
			$expected_return,
			$return,
			"Returned result does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$result,
			"Returned data does not fit to test case."
		);
	}

	public function providerXPath() {
		return array(
			array('red/max', true, 100),
			array('foo/bar', false, null),
			array('grey/light/1', true, 'e7e7e7'),
			array('grey/dark', true, array('cccccc')),
			array('grey/dark/1', false, null),
			array('red/min', true, 0),
			array('red/min/1', false, null),
			array('abc/def/ghi', false, null),
			array('7', true, 'foobar'),
			array(
				'grey',
				true,
				array(
					'light' => array('eeeeee', 'e7e7e7'),
					'dark' => array('cccccc')
				)
			),
			array('', false, null),
			array('/', false, null)
		);
	}

	/**
	 * @dataProvider providerIsEmpty
	 */
	public function testIsEmpty($array, $result_first_level, $result_recursive) {
		$return = Arrays::isEmpty($array, false);
		$this->assertEquals(
			$result_first_level,
			$return,
			"First level check failed for ".var_export($array, true)
		);
		$return = Arrays::isEmpty($array, true);
		$this->assertEquals(
			$result_recursive,
			$return,
			"Recursive check failed for ".var_export($array, true)
		);
	}

	public function providerIsEmpty() {
		return array(
			array(
				array(),
				true, // Result for first level check
				true // result for recursive check
			),
			array(
				array("0", false, null),
				true,
				true
			),
			array(
				array(true),
				false,
				false
			),
			array(
				array(0,1,2),
				false,
				false
			),
			array(
				array(1 => 0, true => false, 2 => ''),
				true,
				true
			),
			array(
				array('Hallo Welt'),
				false,
				false
			),
			array(
				array(array(), array(array())),
				false,
				true
			),
			array(
				array(array(0)),
				false,
				true
			),
			array(
				array(array('Eins'), array(0), array('Zwei')),
				false,
				false
			),
			array(
				array(array(array('')), '0', array(array(), array(array(), array(false, 0, null)))),
				false,
				true
			),
			array(
				array(array(), array(array(), array(array(), array(false, '1', null)))),
				false,
				false
			),
		);
	}
}
?>

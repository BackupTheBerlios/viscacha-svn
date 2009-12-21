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
		$this->assertEquals($expected_return, $return);
		$this->assertEquals($expected_result, $result);
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
}
?>

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
		'black' => array(
			'pure' => array('000000')
		),
		'test1' => array(
			'test11' => array(
				'test1111' => array(
					'x' => 'x'
				),
				'test1112' => '1112'
			),
			'test12' => array(
				'test1121' => '1121'
			)
		),
		'test2' => array(
			'test11' => array(
				'test2111' => array(
					'x' => 'x'
				),
				'test2112' => '2112'
			),
			'test12' => array(
				'test2121' => '2121'
			)
		),
		7 => 'foobar'
	);

	/**
	 * @test
	 */
	public function xPathChangeArrayTest1() {
		$expected_result = $this->xPathData;
		$expected_result['red']['max'] = 200;
		$array = $this->xPathData;

		$return = Arrays::xPath($array, 'red/max', $var = 200);

		$this->assertEquals(
			true,
			$return,
			"Returned boolean does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$array,
			"Change array with xPath test 1 - Data wasn't modified correctly."
		);
	}

	/**
	 * @test
	 */
	public function xPathChangeArrayTest2() {
		$expected_result = $this->xPathData;
		$expected_result['test1']['test11'] = array();
		$expected_result['test2']['test11'] = array();
		$array = $this->xPathData;

		$return = Arrays::xPath($array, '*/test11', $var = array());

		$this->assertEquals(
			true,
			$return,
			"Returned boolean does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$array,
			"Change array with xPath test 2 - Data wasn't modified correctly."
		);
	}

	/**
	 * @test
	 */
	public function xPathChangeArrayTest3() {
		$expected_result = $this->xPathData;
		$array = $this->xPathData;

		$return = Arrays::xPath($array, 'test1/*/nothingToFind', $var = 0);

		$this->assertEquals(
			false,
			$return,
			"Returned boolean does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$array,
			"Change array with xPath test 3 - Data wasn't modified correctly."
		);
	}


	/**
	 * @test
	 */
	public function xPathChangeArrayTest4() {
		$newValue = '111111';
		$expected_result = $this->xPathData;
		$expected_result['black']['pure'][0] = $newValue;
		$array = $this->xPathData;

		$return = Arrays::xPath($array, 'black/*/0', $newValue);

		$this->assertEquals(
			true,
			$return,
			"Returned boolean does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$array,
			"Change array with xPath test 4 - Data wasn't modified correctly."
		);
	}

	/**
	 * @dataProvider providerXPath
	 */
	public function testXPath($path, $expected_return, $expected_result) {
		$return = Arrays::xPath($this->xPathData, $path, $result);
		$this->assertEquals(
			$expected_return,
			$return,
			"Returned boolean does not fit to test case."
		);
		$this->assertEquals(
			$expected_result,
			$result,
			"Result does not fit to test case."
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
			array('/', false, null),
			array(null, false, null),
			array('*/max', true, array('red' => 100)),
			array(
				'*/*/1',
				true,
				array(
					'grey' => array(
						'light' => 'e7e7e7'
					)
				)
			),
			array(
				'*/*/0',
				true,
				array(
					'grey' => array(
						'light' => 'eeeeee',
						'dark' => 'cccccc'
					),
					'black' => array(
						'pure' => '000000'
					)
				)
			),
			array(
				'grey/*/0',
				true,
				array(
					'light' => 'eeeeee',
					'dark' => 'cccccc'
				)
			),
			array('*', false, null),
			array(
				'*/test11/*/x',
				true,
				array(
					'test1' => array(
						'test1111' => 'x'
					),
					'test2' => array(
						'test2111' => 'x'
					)
				)
			)
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

<?php
// Call numbersTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'numbersTest::main');
}

require_once 'PHPUnit/Framework.php';

require_once 'numbers.php';

/**
 * Test class for numbers.
 */
class numbersTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var numbers
	 */
	protected $object;

	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite	= new PHPUnit_Framework_TestSuite('numbersTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}
	
	/**
	 * Loads the data from a the JSON file into a structure that can be read by
	 * PHPUnit.
	 *
	 * @return array
	 */
	public static function numbersProvider()
    {
    	$return = array();
		$tests = json_decode(file_get_contents('../tests/numbersTest'), true);
		foreach ($tests as $test => $all_expected)
		{
			foreach ($all_expected as $expected)
			{
				$return[] = array($test, $expected);
			}
		}
		return $return;
    }
 
    /**
     * @dataProvider numbersProvider
     */
	public function testNumbers($test_data, $expected)
	{
		static $methods = array('unsigned', 'signed', 'real_numbers', 'ratios', 'percentages_and_dimensions', 'lists');
		$method = current($methods);
		
		if (!next($methods))
		{
			reset($methods);
		}
		
		try
		{
			if (is_int($expected) || is_array($expected))
			{
				$this->assertEquals($expected, call_user_func(array('numbers', $method), $test_data));
			}
			else
			{
				$this->assertSame($expected, call_user_func(array('numbers', $method), $test_data));
			}
		}
		catch (Exception $e)
		{
			if ($e->getCode() === numbers::unspecified)
			{
				$this->markTestIncomplete($e->getMessage());
			}
			else
			{
				$this->fail($e->getMessage());
			}
		}
	}
	
	
}

// Call numbersTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD === 'numbersTest::main')
{
	numbersTest::main();
}

?>
<?php

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Util/Timer.php';

require_once 'numbers.php';

class numbersTest implements PHPUnit_Framework_Test
{
	private $tests = array();
	
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite;
		$suite->addTest(new numbersTest());
		return $suite;
	}
	
	public function __construct($data_file = '../../tests/numbersTest')
	{
		$this->tests = json_decode(file_get_contents($data_file), true);
	}
	
	public function count()
	{
		return 1;
	}
	
	public function getName()
	{
		return __CLASS__;
	}
	
	public function run(PHPUnit_Framework_TestResult $result = null)
	{
		if ($result === null)
		{
			$result = new PHPUnit_Framework_TestResult;
		}
		
		$methods = array('unsigned', 'signed', 'real_numbers', 'ratios', 'percentages_and_dimensions', 'lists');
		
		foreach ($this->tests as $test => $expectations)
		{
			reset($methods);
			foreach ($expectations as $expect)
			{
				$result->startTest($this);
				PHPUnit_Util_Timer::start();
				
				try
				{
					if (is_int($expect) || is_array($expect))
					{
						PHPUnit_Framework_Assert::assertEquals($expect, call_user_func(array('numbers', current($methods)), $test));
					}
					else
					{
						PHPUnit_Framework_Assert::assertSame($expect, call_user_func(array('numbers', current($methods)), $test));
					}
				}
				catch (PHPUnit_Framework_AssertionFailedError $e)
				{
					$result->addFailure($this, $e, PHPUnit_Util_Timer::stop());
				}
				catch (Exception $e)
				{
					if ($e->getCode() === numbers::unspecified)
					{
						try
						{
							throw new PHPUnit_Framework_IncompleteTestError($e->getMessage());
						}
						catch (Exception $e2)
						{
							$result->addError($this, $e2, PHPUnit_Util_Timer::stop());
						}
					}
					else
					{
						$result->addError($this, $e, PHPUnit_Util_Timer::stop());
					}
				}
				next($methods);
		
				$result->endTest($this, PHPUnit_Util_Timer::stop());
			}
		}

		return $result;
	}
}

?>
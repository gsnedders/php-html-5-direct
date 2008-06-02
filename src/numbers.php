<?php
/**
 * php-html-5-direct: Direct implementation of the HTML 5 algorithms
 *
 * Copyright (c) 2007, Geoffrey Sneddon.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the author nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package html-5
 * @subpackage semantics
 * @copyright 2007 Geoffrey Sneddon
 * @author Geoffrey Sneddon
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Include common parser idioms
 */
require_once 'common2.php';

/**
 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html#numbers Numbers}
 *
 * @package html-5
 * @subpackage semantics
 */
class numbers
{
	/**
	 * Marks an exception as being a real error
	 */
	const error = 1;
	
	/**
	 * Marks an exception as being unwritten as it is unspecified
	 */
	const unspecified = 2;
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#unsigned Unsigned integers}
	 *
	 * A string is a valid non-negative integer if it consists of one of more characters in the range U+0030 DIGIT ZERO (0) to U+0039 DIGIT NINE (9).
	 *
	 * The rules for parsing non-negative integers are as given in the following algorithm. When invoked, the steps must be followed in the order given, aborting at the first step that returns a value. This algorithm will either return zero, a positive integer, or an error. Leading spaces are ignored. Trailing spaces and indeed any trailing garbage characters are ignored.
	 *
	 * @static
	 * @access public
	 * @param string $input
	 * @return float|int|false This algorithm will either return zero, a positive integer, or an error.
	 */
	public static function unsigned($input)
	{
		// Step 1: Let input be the string being parsed.
		$input = (string) $input;
		
		// Step 2: Let position be a pointer into input, initially pointing at the start of the string.
		$position = 0;
		
		// Step 3: Let value have the value 0.
		$value = 0;
		
		// Step 4: Skip whitespace.
		common2::skip_whitespace($input, $position);
		
		// Step 5: If position is past the end of input, return an error.
		if ($position >= strlen($input))
		{
			return false;
		}
		
		// Step 6: If the next character is not one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9), then return an error.
		if (strcspn($input, '0123456789', $position, 1))
		{
			return false;
		}
		
		// Step 7: If the next character is one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9):
		while (strspn($input, '0123456789', $position, 1))
		{
			// Substep 1: Multiply value by ten.
			$value *= 10;
			
			// Substep 2: Add the value of the current character (0..9) to value.
			$value += $input[$position];
			
			// Substep 3: Advance position to the next character.
			$position++;
			
			// Substep 4: If position is not past the end of input, return to the top of step 7 in the overall algorithm (that's the step within which these substeps find themselves).
			if ($position >= strlen($input))
			{
				break;
			}
		}
		
		// Step 8: Return value.
		return $value;
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#signed Signed integers}
	 *
	 * A string is a valid integer if it consists of one of more characters in the range U+0030 DIGIT ZERO (0) to U+0039 DIGIT NINE (9), optionally prefixed with a U+002D HYPHEN-MINUS ("-") character.
	 *
	 * The rules for parsing integers are similar to the rules for non-negative integers, and are as given in the following algorithm. When invoked, the steps must be followed in the order given, aborting at the first step that returns a value. This algorithm will either return an integer or an error. Leading spaces are ignored. Trailing spaces and trailing garbage characters are ignored.
	 *
	 * @static
	 * @access public
	 * @param string $input
	 * @return float|int|false This algorithm will either return an integer (if the number is too big for PHP to cope, a float) or an error.
	 */
	public static function signed($input)
	{
		// Step 1: Let input be the string being parsed.
		$input = (string) $input;
		
		// Step 2: Let position be a pointer into input, initially pointing at the start of the string.
		$position = 0;
		
		// Step 3: Let value have the value 0.
		$value = 0;
		
		// Step 4: Let sign have the value "positive".
		$sign = 'positive';
		
		// Step 5: Skip whitespace.
		common2::skip_whitespace($input, $position);
		
		// Step 6: If position is past the end of input, return an error.
		if ($position >= strlen($input))
		{
			return false;
		}
		
		// Step 7: If the character indicated by position (the first character) is a U+002D HYPHEN-MINUS ("-") character:
		if ($input[$position] == '-')
		{
			// Substep 1: Let sign be "negative".
			$sign = 'negative';
			
			// Substep 2: Advance position to the next character.
			$position++;
			
			// Substep 3: If position is past the end of input, return an error.
			if ($position >= strlen($input))
			{
				return false;
			}
		}
		
		// Step 8: If the next character is not one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9), then return an error.
		if (strcspn($input, '0123456789', $position, 1))
		{
			return false;
		}
		
		// Step 9: If the next character is one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9):
		while (strspn($input, '0123456789', $position, 1))
		{
			// Substep 1: Multiply value by ten.
			$value *= 10;
			
			// Substep 2: Add the value of the current character (0..9) to value.
			$value += $input[$position];
			
			// Substep 3: Advance position to the next character.
			$position++;
			
			// Substep 4: If position is not past the end of input, return to the top of step 9 in the overall algorithm (that's the step within which these substeps find themselves).
			if ($position >= strlen($input))
			{
				break;
			}
		}
		
		// If sign is "positive", return value, otherwise return 0-value.
		if ($sign == 'positive')
		{
			return $value;
		}
		else
		{
			return 0 - $value;
		}
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#real-numbers Real numbers}
	 *
	 * A string is a valid floating point number if it consists of one of more characters in the range U+0030 DIGIT ZERO (0) to U+0039 DIGIT NINE (9), optionally with a single U+002E FULL STOP (".") character somewhere (either before these numbers, in between two numbers, or after the numbers), all optionally prefixed with a U+002D HYPHEN-MINUS ("-") character.
	 *
	 * The rules for parsing floating point number values are as given in the following algorithm. As with the previous algorithms, when this one is invoked, the steps must be followed in the order given, aborting at the first step that returns a value. This algorithm will either return a number or an error. Leading spaces are ignored. Trailing spaces and garbage characters are ignored.
	 *
	 * @static
	 * @access public
	 * @param string $input
	 * @return float|false This algorithm will either return a float or an error.
	 */
	public static function real_numbers($input)
	{
		// Step 1: Let input be the string being parsed.
		$input = (string) $input;
		
		// Step 2: Let position be a pointer into input, initially pointing at the start of the string.
		$position = 0;
		
		// Step 3: Let value have the value 0.
		$value = 0.0;
		
		// Step 4: Let sign have the value "positive".
		$sign = 'positive';
		
		// Step 5: Skip whitespace.
		common2::skip_whitespace($input, $position);
		
		// Step 6: If position is past the end of input, return an error.
		if ($position >= strlen($input))
		{
			return false;
		}
		
		// Step 7: If the character indicated by position (the first character) is a U+002D HYPHEN-MINUS ("-") character:
		if ($input[$position] == '-')
		{
			// Substep 1: Let sign be "negative".
			$sign = 'negative';
			
			// Substep 2: Advance position to the next character.
			$position++;
			
			// Substep 3: If position is past the end of input, return an error.
			if ($position >= strlen($input))
			{
				return false;
			}
		}
		
		// Step 8: If the next character is not one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9) or U+002E FULL STOP ("."), then return an error.
		if (strcspn($input, '0123456789.', $position, 1))
		{
			return false;
		}
		
		// Step 9: If the next character is U+002E FULL STOP ("."), but either that is the last character or the character after that one is not one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9), then return an error.
		if ($input[$position] == '.' && !strspn($input, '0123456789', $position + 1, 1))
		{
			return false;
		}
		
		// Step 10: If the next character is one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9):
		while (strspn($input, '0123456789', $position, 1))
		{
			// Substep 1: Multiply value by ten.
			$value *= 10;
			
			// Substep 2: Add the value of the current character (0..9) to value.
			$value += $input[$position];
			
			// Substep 3: Advance position to the next character.
			$position++;
			
			// Substep 4: If position is past the end of input, then if sign is "positive", return value, otherwise return 0-value.
			if ($position >= strlen($input))
			{
				if ($sign == 'positive')
				{
					return $value;
				}
				else
				{
					return 0 - $value;
				}
			}
			
			// Substep 5: Otherwise return to the top of step 10 in the overall algorithm (that's the step within which these substeps find themselves).
			// This is implied by the while loop.
		}
		
		// Step 11: Otherwise, if the next character is not a U+002E FULL STOP ("."), then if sign is "positive", return value, otherwise return 0-value.
		if ($input[$position] != '.')
		{
			if ($sign == 'positive')
			{
				return $value;
			}
			else
			{
				return 0 - $value;
			}
		}
		
		// Step 12: The next character is a U+002E FULL STOP ("."). Advance position to the character after that.
		$position++;
		
		// Step 13: Let divisor be 1.
		$divisor = 1;
		
		// Step 14: If the next character is one of U+0030 DIGIT ZERO (0) .. U+0039 DIGIT NINE (9):
		while (strspn($input, '0123456789', $position, 1))
		{
			// Substep 1: Multiply divisor by ten.
			$divisor *= 10;
			
			// Substep 2: Add the value of the current character (0..9) divided by divisor, to value.
			$value += $input[$position] / $divisor;
			
			// Substep 3: Advance position to the next character.
			$position++;
			
			// Substep 4: If position is past the end of input, then if sign is "positive", return value, otherwise return 0-value.
			if ($position >= strlen($input))
			{
				if ($sign == 'positive')
				{
					return $value;
				}
				else
				{
					return 0 - $value;
				}
			}
			
			// Substep 5: Otherwise return to the top of step 14 in the overall algorithm (that's the step within which these substeps find themselves).
			// This is implied by the while loop.
		}
		
		// Otherwise, if sign is "positive", return value, otherwise return 0-value.
		if ($sign == 'positive')
		{
			return $value;
		}
		else
		{
			return 0 - $value;
		}
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#ratios Ratios}
	 *
	 * @static
	 * @access public
	 * @param string $input
	 * @return array|null
	 */
	public static function ratios($input)
	{
		// (Un-spec'd): Let input be the string being parsed.
		$input = (string) $input;
		
		// Step 1: If the string is empty, then return nothing and abort these steps.
		if ($input === '')
		{
			return null;
		}
		
		// Step 2: Find a number in the string according to the algorithm below, starting at the start of the string.
		$position = 0;
		$number1 = self::find_a_number($input, $position);
		
		// Step 3: If the sub-algorithm in step 2 returned nothing or returned an error condition, return nothing and abort these steps.
		if ($number1 === null || $number1 === false)
		{
			return null;
		}
		
		// Step 4: Set number1 to the number returned by the sub-algorithm in step 2.
		// See step 2
		
		// Step 5: Starting with the character immediately after the last one examined by the sub-algorithm in step 2, skip any characters in the string that are in the Unicode character class Zs (this might match zero characters). [UNICODE]
		common2::skip_Zs_characters($input, $position);
		
		// Step 6: If there are still further characters in the string, and the next character in the string is a valid denominator punctuation character, set denominator to that character.
		if ($position < strlen($input) && $input[$position] === "\x25")
		{
			$denominator = $input[$position];
		}
		elseif ($position + 1 < strlen($input) && substr($input, $position, 2) === "\xD9\xAA")
		{
			$denominator = substr($input, $position, 2);
		}
		elseif ($position + 2 < strlen($input) && in_array(substr($input, $position, 3), array("\xEF\xB9\xAA", "\xEF\xBC\x85", "\xE2\x80\xB0", "\xE2\x80\xB1"), true))
		{
			$denominator = substr($input, $position, 3);
		}
		
		// Step 7: If the string contains any other characters in the range U+0030 DIGIT ZERO to U+0039 DIGIT NINE, but denominator was given a value in the step 6, return nothing and abort these steps.
		if (isset($denominator) && strcspn($input, '0123456789', $position) !== strlen($input) - $position)
		{
			return null;
		}
		
		// Step 8: Otherwise, if denominator was given a value in step 6, return number1 and denominator and abort these steps.
		if (isset($denominator))
		{
			return array($number1, $denominator);
		}
		
		// Step 9: Find a number in the string again, starting immediately after the last character that was examined by the sub-algorithm in step 2.
		$number2 = self::find_a_number($input, $position);
		
		// Step 10: If the sub-algorithm in step 9 returned nothing or an error condition, return nothing and abort these steps.
		if ($number2 === null || $number2 === false)
		{
			return null;
		}
		
		// Step 11: Set number2 to the number returned by the sub-algorithm in step 9.
		// See step 9
		
		// Step 12: If there are still further characters in the string, and the next character in the string is a valid denominator punctuation character, return nothing and abort these steps.
		if ($position < strlen($input) && $input[$position] === "\x25" || $position + 1 < strlen($input) && substr($input, $position, 2) === "\xD9\xAA" || $position + 2 < strlen($input) && in_array(substr($input, $position, 3), array("\xEF\xB9\xAA", "\xEF\xBC\x85", "\xE2\x80\xB0", "\xE2\x80\xB1"), true))
		{
			return null;
		}
		
		// Step 13: If the string contains any other characters in the range U+0030 DIGIT ZERO to U+0039 DIGIT NINE, return nothing and abort these steps.
		if ($position < strlen($input) && strcspn($input, '0123456789', $position) !== strlen($input) - $position)
		{
			return null;
		}
		
		// Step 14: Otherwise, return number1 and number2.
		return array($number1, $number2);
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#find-a Find a number}
	 *
	 * It is given a string and a starting position, and returns either nothing, a number, or an error condition.
	 *
	 * @static
	 * @access public
	 * @param string &$input
	 * @param string &$position
	 * @return float|null|false
	 */
	public static function find_a_number(&$input, &$position)
	{
		// Step 1: Starting at the given starting position, ignore all characters in the given string until the first character that is either a U+002E FULL STOP or one of the ten characters in the range U+0030 DIGIT ZERO to U+0039 DIGIT NINE.
		$position += strcspn($input, '.0123456789', $position);
		
		// Step 2: If there are no such characters, return nothing and abort these steps.
		if (!strspn($input, '.0123456789', $position, 1))
		{
			return null;
		}
		
		// Step 3: Starting with the character matched in step 1, collect all the consecutive characters that are either a U+002E FULL STOP or one of the ten characters in the range U+0030 DIGIT ZERO to U+0039 DIGIT NINE, and assign this string of one or more characters to string.
		$len = strspn($input, '.0123456789', $position);
		$string = substr($input, $position, $len);
		$position += $len;
		
		// Step 4: If string contains more than one U+002E FULL STOP character then return an error condition and abort these steps.
		if (substr_count($string, '.') > 1)
		{
			return false;
		}
		
		// Step 5: Parse string according to the rules for parsing floating point number values, to obtain number. This step cannot fail (string is guaranteed to be a valid floating point number).
		$number = self::real_numbers($string);
		
		// Step 6: Return number.
		return $number;
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#percentages-and-dimensions Percentages and dimensions}
	 *
	 * This is yet to be written in the specification, so this throws an exception.
	 *
	 * @static
	 * @access public
	 */
	public static function percentages_and_dimensions()
	{
		throw new Exception('This is yet to be written in the specification', self::unspecified);
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#lists Lists of integers}
	 *
	 * A valid list of integers is a number of valid integers separated by U+002C COMMA characters, with no other characters (e.g. no space characters). In addition, there might be restrictions on the number of integers that can be given, or on the range of values allowed.
	 *
	 * @static
	 * @access public
	 * @param string $input
	 * @return array This algorithm will either return n array.
	 */
	public static function lists($input)
	{
		// Step 1: Let input be the string being parsed.
		$input = (string) $input;
		
		// Step 2: Let position be a pointer into input, initially pointing at the start of the string.
		$position = 0;
		
		// Step 3: Let numbers be an initially empty list of integers. This list will be the result of this algorithm.
		$numbers = array();
		
		// (Un-spec'd): Start step 4 loop
		do
		{
			// Step 4: If there is a character in the string input at position position, and it is either a U+0020 SPACE, U+002C COMMA, or U+003B SEMICOLON character, then advance position to the next character in input, or to beyond the end of the string if there are no more characters.
			if ($position < strlen($input) && in_array($input[$position], array(' ', ',', ';')))
			{
				$position++;
			}
			
			// Step 5: If position points to beyond the end of input, return numbers and abort.
			if ($position >= strlen($input))
			{
				return $numbers;
			}
			
			// Step 6: If the character in the string input at position position is a U+0020 SPACE, U+002C COMMA, or U+003B SEMICOLON character, then return to step 4.
			if (in_array($input[$position], array(' ', ',', ';')))
			{
				continue;
			}
			
			// Step 7: Let negated be false.
			$negated = false;
			
			// Step 8: Let value be 0.
			$value = 0;
			
			// Step 9: Let started be false.
			$started = false;
			
			// Step 10: Let got number be false. This variable is set to true when the parser sees a number.
			$got_number = false;
			
			// Step 11: Let finished be false.
			$finished = false;
			
			// Step 12: Let bogus be false.
			$bogus = false;
			
			// (Un-spec'd): Start step 13 loop
			do
			{
				// Step 13: Parser: If the character in the string input at position position is:
				switch ($input[$position])
				{
					// A U+002D HYPHEN-MINUS character
					case '-':
						// Substep 1: If got number is true, let finished be true.
						if ($got_number)
						{
							$finished = true;
						}
						// Substep 2: If finished is true, skip to the next step in the overall set of steps.
						if ($finished)
						{
							break 3;
						}
						
						// Substep 3: If started is true, let negated be false.
						if ($started)
						{
							$negated = false;
						}
						
						// Substep 4: Otherwise, if started is false and if bogus is false, let negated be true.
						if (!$started && !$bogus)
						{
							$negated = true;
						}
						
						// Substep 5: Let started be true.
						$started = true;
					break;
					
					// A character in the range U+0030 DIGIT ZERO .. U+0039 DIGIT NINE
					case '0':
					case '1':
					case '2':
					case '3':
					case '4':
					case '5':
					case '6':
					case '7':
					case '8':
					case '9':
						// Substep 1: If finished is true, skip to the next step in the overall set of steps.
						if ($finished)
						{
							break 3;
						}
						
						// Substep 2: Multiply value by ten.
						$value *= 10;
						
						// Substep 3: Add the value of the digit, interpreted in base ten, to value.
						$value += $input[$position];
						
						// Substep 4: Let started be true.
						$started = true;
						
						// Substep 5: Let got number be true.
						$got_number = true;
					break;
					
					// A U+0020 SPACE character
					case ' ':
					// A U+002C COMMA character
					case ',':
					// A U+003B SEMICOLON character
					case ';':
						// Substep 1: If got number is false, return the numbers list and abort. This happens if an entry in the list has no digits, as in "1,2,x,4".
						if (!$got_number)
						{
							return $numbers;
						}
						
						// Substep 2: If negated is true, then negate value.
						if ($negated)
						{
							$value = 0 - $value;
						}
						
						// Substep 3: Append value to the numbers list.
						$numbers[] = $value;
						
						// Substep 4: Jump to step 4 in the overall set of steps.
						continue 3;
					
					// A U+002E FULL STOP character
					case '.':
						// Substep 1: If got number is true, let finished be true.
						if ($got_number)
						{
							$finished = true;
						}
						
						// Substep 2: If finished is true, skip to the next step in the overall set of steps.
						if ($finished)
						{
							break 3;
						}
						
						// Substep 3: Let negated be false.
						$negated = false;
					break;
					
					// Any other character
					default:
						// Substep 1: If finished is true, skip to the next step in the overall set of steps.
						if ($finished)
						{
							break 3;
						}
						
						// Substep 2: Let negated be false.
						$negated = false;
						
						// Substep 3: Let bogus be true.
						$bogus = true;
						
						// Substep 4: If started is true, then return the numbers list, and abort. (The value in value is not appended to the list first; it is dropped.)
						if ($started)
						{
							return $numbers;
						}
					break;
				}
				
				// Step 14: Advance position to the next character in input, or to beyond the end of the string if there are no more characters.
				$position++;
				
			// Step 15: If position points to a character (and not to beyond the end of input), jump to the big Parser step above.
			} while ($position < strlen($input));
			
			// (Un-spec'd): End step 4 loop
			break;
		}
		while (true);
		
		// Step 16: If negated is true, then negate value.
		if ($negated)
		{
			$value = 0 - $value;
		}
		
		// Step 17: If got number is true, then append value to the numbers list.
		if ($got_number)
		{
			$numbers[] = $value;
		}
		
		// Step 18: Return the numbers list and abort.
		return $numbers;
	}
}

?>
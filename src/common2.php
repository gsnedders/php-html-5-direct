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
 * @version $Id: common2.php 32 2007-07-12 15:58:53Z gsnedders $
 * @copyright 2007 Geoffrey Sneddon
 * @author Geoffrey Sneddon
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html#common2 Common parser idioms}
 *
 * @package html-5
 * @subpackage semantics
 */
class common2
{
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#collect Collect a sequence of characters}
	 *
	 * @static
	 * @access public
	 * @param string &$input Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 * @param string &$position Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 * @param array $characters The set of characters that can be collected.
	 * @return string String matching characters starting at position at input.
	 */
	public static function collect_characters(&$input, &$position, $characters)
	{
		// Step 2: Let result be the empty string.
		$result = '';
		
		// Step 3: While position doesn't point past the end of input and the character at position is one of the characters, append that character to the end of result and advance position to the next character in input.
		while ($position < strlen($input))
		{
			$match = false;
			foreach ($characters as $character)
			{
				if (substr($input, $position, strlen($character)) === $character)
				{
					$match = true;
					$result .= $character;
					$position += strlen($character);
				}
			}
			
			if (!$match)
			{
				break;
			}
		}
		
		// Step 4: Return result.
		return $result;
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#skip-whitespace Skip whitespace}
	 *
	 * @static
	 * @access public
	 * @param string &$input Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 * @param string &$position Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 */
	public static function skip_whitespace(&$input, &$position)
	{
		self::collect_characters($input, $position, array("\x20", "\x09", "\x0A", "\x0B", "\x0C", "\x0D"));
	}
	
	/**
	 * {@link http://dev.w3.org/cvsweb/~checkout~/html5/spec/Overview.html?rev=1.904#skip- skip Zs characters}
	 *
	 * Disclaimer: This assumes we are using UTF-8
	 *
	 * @static
	 * @access public
	 * @param string &$input Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 * @param string &$position Let input and position be the same variables as those of the same name in the algorithm that invoked these steps.
	 */
	public static function skip_Zs_characters(&$input, &$position)
	{
		self::collect_characters($input, $position, array("\x20", "\xC2\xA0", "\xE1\x9A\x80", "\xE1\xA0\x8E", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"));
	}
}
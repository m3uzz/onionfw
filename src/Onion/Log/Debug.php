<?php
/**
 * This file is part of Onion
 *
 * Copyright (c) 2014-2016, Humberto Lourenço <betto@m3uzz.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Humberto Lourenço nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    Onion
 * @author     Humberto Lourenço <betto@m3uzz.com>
 * @copyright  2014-2016 Humberto Lourenço <betto@m3uzz.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://github.com/m3uzz/onionfw
 */

namespace Onion\Log;
use Onion;
use Onion\Lib\Util;
use Onion\Config\Config;
use Onion\Json\Json;

class Debug
{

	/**
	 *
	 * @param int|string|array|object $pmVar
	 *        	- Valor a ser impresso
	 * @param boolean $pbForceDebug
	 *        	- Se true força a impressão mesmo que no config o debug esteja
	 *        	desabilitado
	 * @param string $psType
	 *        	- força o tipo de saída de impressão
	 */
	public static function debug ($pmVar, $pbForceDebug = false, $psType = null)
	{
		$laConfig = Config::getAppOptions();
		$laDebug = $laConfig['log']['debug']['PHP'];
		
		if (Util::toBoolean($laDebug['enable']) || $pbForceDebug)
		{
			$lsType = $laDebug['output'];
			
			if ($psType != null)
			{
				$lsType = $psType;
			}
			
			switch ($lsType)
			{
				case "DISPLAY":
					echo '<pre style="margin:50px;"><code><fieldset><legend>Onion Debug:</legend>';
					self::displayDebug($pmVar);
					echo '</fieldset></code></pre>';
					break;
				case "FIREBUG":
					$loLogger = new FirePHP();
					$loLogger->log($pmVar);
					break;
				case "COMMENT":
					echo "<!--";
					self::displayDebug($pmVar);
					echo "-->";
					break;
				case "BUFFER":
					$loBuffer = Session::getRegister("DEBUG");
					$loBuffer[] = Json::encode($pmVar);
					break;
			}
		}
	}

	/**
	 *
	 * @param int|string|array|object $pmVar        	
	 */
	public static function display ($pmVar)
	{
		self::debug($pmVar, true, "DISPLAY");
	}

	/**
	 *
	 * @param int|string|array|object $pmVar        	
	 */
	public static function displayD ($pmVar)
	{
		die(self::debug($pmVar, true, "DISPLAY"));
	}

	/**
	 */
	public static function showDebug ()
	{
		$laConfig = Config::getAppOptions();
		$laDebug = $laConfig['log']['debug']['PHP'];
		
		if (Util::toBoolean($laDebug['enable']) && $laDebug['output'] == "BUFFER")
		{
			$loBuffer = Session::getRegister("DEBUG");
			
			echo '<pre style="margin:50px;"><code><fieldset><legend>Onion Debug:</legend>';
			
			if (is_array($loBuffer))
			{
				foreach ($loBuffer as $lsItem)
				{
					self::displayDebug(Json::decode($lsItem));
					echo "<br/><hr/><br/>";
				}
			}
			
			echo '</fieldset></code></pre>';
		}
		
		Session::clearRegister("DEBUG");
	}

	/**
	 *
	 * @param int|string|array|object $pmVar        	
	 */
	public static function displayDebug ($pmVar)
	{
		if (is_object($pmVar))
		{
			var_dump($pmVar);
		}
		elseif (is_array($pmVar))
		{
			print_r($pmVar);
		}
		else
		{
			echo ($pmVar);
		}
	}
}
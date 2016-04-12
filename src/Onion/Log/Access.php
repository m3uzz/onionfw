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
use Onion\Db\Adapter\Adapter;

class Access
{

	/**
	 */
	public static function log ($poCredentials, $psMsg = "OK")
	{
		$laConfig = Config::getAppOptions();
		$laLog = $laConfig['log']['log']['access'];
		$laDb = $laConfig['db'][APP_ENV];
		
		$lnId = null;
		$lsUsername = null;
		$lnGroupId = null;
		
		if (is_object($poCredentials))
		{
			$lnId = $poCredentials->getId();
			$lsUsername = $poCredentials->getUsername();
			$lnGroupId = $poCredentials->getUserGroupId();
		}
		else
		{
			$lsUsername = $poCredentials;
		}
		
		$lsIp = $_SERVER['REMOTE_ADDR'];
		$lsSession = session_id();
		
		if (Util::toBoolean($laLog['enable']))
		{
			if ($laLog['output'] == "DB")
			{
				$loDb = new Adapter($laDb);
				
				$laColumnMap = array(
					'timestamp' => 'dtInsert',
					'priority' => 'stPriority',
					'message' => 'txtCredentials',
					'extra' => array(
						'User_id' => 'User_id',
						'stSession' => 'stSession',
						'stIP' => 'stIP',
						'txtServer' => 'txtServer'
					)
				);
				
				$laData = array(
					'User_id' => $lnId,
					'stSession' => $lsSession,
					'stIP' => $lsIp,
					'txtServer' => $psMsg
				);
				
				$laCredential = array(
					'user' => $lsUsername,
					'group' => $lnGroupId
				);
				
				$lsCredential = Json::encode($laCredential);
				
				$loWriter = new Writer\Db($loDb, $laLog['table'], $laColumnMap);
				
				$loLogger = new Logger();
				$loLogger->addWriter($loWriter);
				$loLogger->info($lsCredential, $laData);
			}
			else
			{
				$laMessage = array(
					'stIP' => $lsIp,
					'stSession' => $lsSession,
					'userId' => $lnId,
					'user' => $lsUsername,
					'group' => $lnGroupId
				);
				
				Event::log($laMessage, Event::INFO, $laLog['fileName'], "STREAM", true);
			}
		}
	}
}
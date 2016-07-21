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
use \Zend\Authentication\AuthenticationService AS Auth;

class Event
{
	const EMERG   = 0;  // Emergency: system is unusable
	const ALERT   = 1;  // Alert: action must be taken immediately
	const CRIT    = 2;  // Critical: critical conditions
	const ERR     = 3;  // Error: error conditions
	const WARN    = 4;  // Warning: warning conditions
	const NOTICE  = 5;  // Notice: normal but significant condition
	const INFO    = 6;  // Informational: informational messages
	const DEBUG   = 7;  // Debug: debug messages
	
	/**
	 *
	 * @param string $psId        	
	 * @return array NULL
	 */
	public static function getCredential (&$psId)
	{
		$loAuthService = new Auth();

		if ($loAuthService->hasIdentity())
		{
			$loCredentials = $loAuthService->getIdentity();;
			$psId = $loCredentials->get('id');
			return $loCredentials;
		}
		
		return null;
	}

	/**
	 * event: gera log de sistema, gravando em banco, arquivo, firebug ou exibindo na tela
	 *
	 * @param string|array $pmParam	- array __FILE__,__METHOD__,__LINE__ ...
	 * @param int $pnPriority - EMERG = 0; ALERT = 1; CRIT = 2; ERR = 3; WARN = 4; NOTICE =	5; INFO = 6; DEBUG = 7;
	 * @param string $psOutput - força o nome da tabela ou posfixo do arquivo a ser gravado
	 * @param string $psType - força o tipo de saída: DB ou STREAM
	 */
	public static function log ($pmParam, $pnPriority = 7, $psOutput = null, $psType = null, $pbSave = false)
	{
		$laConfig = Config::getAppOptions();
		$laLog = $laConfig['log']['log']['events'];
		$laDb = $laConfig['db'][APP_ENV];
		$lsLogDir = $laConfig['log']['log']['logDir'];
		
		$lsTable = $laLog['table'];
		$lsFilePosfix = "_" . $laLog['fileName'];
		$lsLine = "";
		$lsId = null;
		
		if (Util::toBoolean($laLog['enable']) || $pbSave)
		{
			$lsIP = $_SERVER['REMOTE_ADDR'];
			
			$lsType = $laLog['output'];
				
			if ($psType != null)
			{
				$lsType = $psType;
			}
			
			if (is_array($pmParam))
			{
				foreach ($pmParam as $lsKey => $lsItem)
				{
					if (strtolower($lsKey) == 'userid')
					{
						$lsId = $lsItem;
						continue;
					}
				}
			}
			
			$laData = array();
			
			switch ($lsType)
			{
				case "DB":
					if ($psOutput != null)
					{
						$lsTable = $psOutput;
					}
					
					$loDb = new Adapter($laDb);
					
					$lsLine = Json::encode($pmParam);
					$lsServer = Json::encode($_SERVER);
					
					$laExtra = array(
					   'stIP' => 'stIP',
					   'txtServer' => 'txtServer'
				    );	
				
					$laData = array(
						'stIP' => $lsIP,
						'txtServer' => $lsServer
					);
				
					if ($lsId !== null)
				    {
				        $laExtra['User_id'] = 'User_id';
				        $laData['User_id'] = $lsId;
				    }
				
					$laColumnMap = array(
						'timestamp' => 'dtInsert',
						'priority' => 'stPriority',
						'message' => 'stMsg',
						'extra' => $laExtra
					);
					
					$loWriter = new Writer\Db($loDb, $lsTable, $laColumnMap);
					
					break;
				case "STREAM":
					$lsLine .= "\t" . $lsIP;
						
					if (is_array($pmParam))
					{
						foreach ($pmParam as $lsKey => $lsItem)
						{
							$lsLine .= "\t" . $lsItem;
								
							if (strtolower($lsKey) == 'userid')
							{
								$lsId = $lsItem;
							}
						}
					}
					else
					{
						$lsLine .= "\t" . $pmParam;
					}
					
					if ($psOutput != null)
					{
						$lsFilePosfix = "_" . $psOutput;
					}
					
					if (file_exists($lsLogDir))
					{
						$loWriter = new Writer\Stream($lsLogDir . DS . date("Y-m-d") . $lsFilePosfix . ".log");
					}
					
					break;
			}
			
			$loLogger = new Logger();
			$loLogger->addWriter($loWriter);
			$loLogger->log($pnPriority, $lsLine, $laData);
		}
	}
	

	public static function getLog ($psSource)
	{
		//TODO: create a parse log;
	}
	
	public static function getLogStream ()
	{
		return self::getLog('STREAM');
	}
	
	public static function getLogDb ()
	{
		return self::getLog('DB');
	}	
}
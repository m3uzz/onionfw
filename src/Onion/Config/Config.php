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

namespace Onion\Config;
use Onion\Application\Application;
use Onion\Lib\Util;
use Onion\Log\Log;
use Onion\File;
use Onion\Json\Json;

class Config
{
	
	/**
	 * Load the Onion default configs and merge with client configs.
	 * 
	 * @return array
	 */
	public static function loadConfigs ()
	{
		$lsCacheConfDir = CLIENT_DIR . DS . 'data' . DS . 'cache' . DS . 'client_config.global.php';
		$lsOnionConfDir = 'config' . DS . "autoload" . DS . "onion.global.php";
		$lsClientConfDir = CLIENT_DIR . DS . "config" . DS . "client.global.php";
		
		if (file_exists($lsCacheConfDir))
		{
			return include $lsCacheConfDir;
		}
		else 
		{
			$laOnionConfig = array();
			$laClientConfig = array();
			
			if (file_exists($lsOnionConfDir))
			{
				$laOnionConfig = include $lsOnionConfDir;
			}
			
			if (file_exists($lsClientConfDir))
			{
				$laClientConfig = include $lsClientConfDir;
			}
			
			return self::merge($laOnionConfig, $laClientConfig);
		}
	}

	/**
	 * Merge options recursively
	 *
	 * @param array $paArray1        	
	 * @param mixed $paArray2        	
	 * @return array
	 */
	public static function merge (array $paArray1, $paArray2 = null)
	{
		if (is_array($paArray2))
		{
			foreach ($paArray2 as $lmKey => $lmVal)
			{
				if (is_array($paArray2[$lmKey]))
				{
					$paArray1[$lmKey] = (array_key_exists($lmKey, $paArray1) &&
							 is_array($paArray1[$lmKey])) ? self::merge(
									$paArray1[$lmKey], $paArray2[$lmKey]) : $paArray2[$lmKey];
				}
				else
				{
					$paArray1[$lmKey] = $lmVal;
				}
			}
		}
		
		return $paArray1;
	}

	/**
	 * 
	 * @param array $paConfigs
	 */
	public static function cacheConfig($paConfigs)
	{
		$lsCacheConfDir = CLIENT_DIR . DS . 'data' . DS . 'cache' . DS . 'client_config.global.php';
		
		if ($paConfigs['settings']['cacheConfig'] && !file_exists($lsCacheConfDir))
		{
			$lsConfig = File\System::arrayToFile($paConfigs);
			File\System::saveFile($lsCacheConfDir, $lsConfig);
		}
	}
	
	/**
	 * Load the application options
	 *
	 * @param string $psOption
	 * @return array
	 */
	public static function getAppOptions ($psOption = null)
	{
		$goOnionApp = Application::getInstance();
		
		if ($goOnionApp && $goOnionApp->get('active'))
		{
			$gaOnionConfig = $goOnionApp->get('_onionConfig');
		}
		else
		{
			$gaOnionConfig = self::loadConfigs();
		}
		
		if ($psOption !== null && isset($gaOnionConfig[$psOption]))
		{
			return $gaOnionConfig[$psOption];
		}
		else
		{
			return $gaOnionConfig;
		}
	}
	
	/**
	 * Load the application options
	 *
	 * @param string $psOption
	 * @return array
	 */
	public static function getZendOptions ($psOption = null)
	{
		$goOnionApp = Application::getInstance();
	
		if ($goOnionApp && $goOnionApp->get('active'))
		{
			$gaZendConfig = $goOnionApp->get('_generalConfig');
		}
	
		if ($psOption !== null && isset($gaZendConfig[$psOption]))
		{
			return $gaZendConfig[$psOption];
		}
		else
		{
			return $gaZendConfig;
		}
	}	

	/**
	 * 
	 * @param string $psOption
	 * @return string Json
	 */
	public static function getAppOptionsJson ($psOption = null)
	{
		return Json::endoce(self::getAppOptions($psOption));
	}

	/**
	 * 
	 * @param unknown $pmValue
	 * @param string $psVar
	 * @param string $psSection
	 */
	public static function saveAppOption ($pmValue, $psVar = null, $psSection = null)
	{
		// TODO: Implement method saveAppOption
	}
	
	public static function setModules(array $paSettings)
	{
		
	}
	
	public static function setModuleListenerOptions(array $paSettings)
	{
		
	}
	
	public static function setLayout(array $paSettings)
	{
		
	}
	
	public static function setTranslator(array $paSettings)
	{
		
	}

	public static function setDb(array $paSettings)
	{
	
	}
	
	
}
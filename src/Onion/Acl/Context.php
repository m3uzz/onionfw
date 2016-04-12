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

namespace Onion\Acl;
use Onion\Config\Config;
use Onion\Log\Debug;

class Context
{
	public static function hasContextAccess ($paUserContext = null)
	{
		$lsUserIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
		$lsAppClient = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$lsToken = isset($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN'] : "";
		$lbReturn = false;
	
		Debug::debug(array($lsUserIp, $lsAppClient, $lsToken));
	
		$laConfigAcl = Config::getAppOptions('acl');
		$laContext = (isset($laConfigAcl['acl']['context']) ? $laConfigAcl['acl']['context'] : null);
		
		if ($paUserContext !== null)
		{
			$laContext = Config::merge($paUserContext, $laContext);
		}
		
		Debug::debug($laContext);
	
		if (is_array($laContext))
		{
			foreach ($laContext as $lsIp => $laIpContext)
			{
				$lsIpPattern = preg_replace(array("/^!([\d\.?]*)$/", "/\./", "/\?/"), array("[^$1]", "\.", "[\d]*"), $lsIp);
				
				if (preg_match("/^$lsIpPattern$/", $lsUserIp))
				{
					Debug::debug("IP: $lsUserIp");

					if (isset($laIpContext['denied']) && $laIpContext['denied'] == true)
					{
						$lbReturn = false;
						Debug::debug("IP [$lsUserIp] DENIED FOR $lsIpPattern");
						break;
					}
					
					if (isset($laIpContext['user-agent'][$lsAppClient]))
					{
						Debug::debug("USER-AGENT: $lsAppClient");
							
						if ($laIpContext['user-agent'][$lsAppClient] == $lsToken)
						{
							Debug::debug("TOKEN: $lsToken");
							$lbReturn = true;
							break;
						}
					}
					elseif (isset($laIpContext['user-agent']['*']))
					{
						Debug::debug("USER-AGENT: *");
							
						if ($laIpContext['user-agent']['*'] == $lsToken)
						{
							Debug::debug("TOKEN: $lsToken");
							$lbReturn = true;
							break;
						}
					}
				}
				else 
				{
					Debug::debug("IP [$lsUserIp] DENIED FOR $lsIpPattern");
				}
			}
		}
		else
		{
			$lbReturn = true;
		}

		return $lbReturn;
	}	
}
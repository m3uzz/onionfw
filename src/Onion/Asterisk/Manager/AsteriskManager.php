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

namespace Onion\Asterisk\Manager;
use Onion\Log\Debug;
use Onion\Log\Event;
use Onion\I18n\Translator;
use Onion\Application\Application;
use Onion\Config\Config;
use Onion\Json\Json;
use Onion\Lib\String;

class AsteriskManager
{

	protected $_rSocket = null;

	protected $_nResponseTimeOut = 1;

	protected $_nResponseTimeOutMicro = 0;

	protected $_bProxyConnection = true;

	protected $_aResponse = array();

	protected $_aError;

	protected $_aService = array();

	protected $_aParams = array();

	protected $_sResoponseType = 'http'; // json, stream

	protected $_sActionId = "Onion";

	protected $_sAudioPath = "/data\/uploads\/audio/";

	protected $_sAudioPathReal = "datastorage/audios";

	/**
	 */
	public function __construct ()
	{
		if ($_SERVER['HTTP_ACCEPT'] == "text/event-stream")
		{
			$this->_sResoponseType = 'stream';
		}
		elseif ($_SERVER['HTTP_ACCEPT'] == "application/json")
		{
			$this->_sResoponseType = 'json';
		}
	}

	/**
	 *
	 * @param string $psVar
	 * @param mixed $pmValue
	 * @return \Onion\Lib\AsteriskManager
	 */
	public function set ($psVar, $pmValue)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			$this->$lsVar = $pmValue;
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psVar
	 */
	public function get ($psVar)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			return $this->$lsVar;
		}
	}

	/**
	 */
	public function getParams ()
	{
		if (isset($_SERVER['argv']) && is_array($_SERVER['argv']))
		{
			foreach ($_SERVER['argv'] as $lsArg)
			{
				$laArg = explode("=", $lsArg);
				
				if (isset($laArg[1]))
				{
					$this->_aParams[$laArg[0]] = $laArg[1];
				}
			}
		}
		elseif (isset($_GET) && is_array($_GET))
		{
			$this->_aParams = $_GET;
		}
	}

	/**
	 *
	 * @param array $paService
	 */
	public function setService ($paService)
	{
		$this->_aService = array(
			'host' => $paService['host'],
			'port' => $paService['port'],
			'user' => $paService['user'],
			'pass' => $paService['pass'],
			'context' => $paService['context']
		);
		
		$this->set('bProxyConnection', $paService['proxy']);
	}

	/**
	 *
	 * @param string $psVar
	 * @param string $psValue
	 */
	public function setParam ($psVar, $psValue)
	{
		$this->_aParams[$psVar] = $psValue;
	}

	/**
	 *
	 * @param boolean $pbJson
	 * @return string|array
	 */
	public function getLog ($pbJson = false)
	{
		if ($pbJson)
		{
			return Json::encode($this->_aResponse);
		}
		
		return $this->_aResponse;
	}

	/**
	 *
	 * @param string $psVar
	 * @param string $psDefault
	 * @return multitype
	 */
	public function request ($psVar, $psDefault = null)
	{
		if (isset($this->_aParams[$psVar]))
		{
			return $this->_aParams[$psVar];
		}
		else
		{
			return $psDefault;
		}
	}

	/**
	 *
	 * @return string
	 */
	public function response ()
	{
		if ($this->_sResoponseType == 'json')
		{
			header("Content-Type: application/json");
			return Json::encode($this->_aResponse);
		}
		elseif ($this->_sResoponseType == 'stream')
		{
			header("Content-type: text/event-stream");
			$lsResponse = Json::encode($this->_aResponse);
			return "data: " . $lsResponse . "\n\n";
		}
		else
		{
			Debug::display($this->_aResponse);
		}
	}

	/**
	 *
	 * @param string $psAct
	 * @return boolean
	 */
	public function manager ($psAct = '')
	{
		$lsAct = $this->request('act', $psAct);
		$lsNextAction = $this->request('nextaction', '');
		$lnId = $this->request('id', '');
		$lnExtension = $this->request('extension', '');
		$lnPhone = $this->request('phone', '');
		$lsSip = $this->request('sip', '');
		$lsAudioChannel = $this->request('audiochannel', '');
		$lsAudioPath = $this->request('audio', '');
		
		$lsAudio = preg_replace($this->_sAudioPath, $this->_sAudioPathReal, $lsAudioPath);
		$lnExtension = String::formatPhone($lnExtension);
		$lnPhone = String::formatPhone($lnPhone);
		
		$this->set('sActionId', $this->get('sActionId') . "-{$lnId}");
		
		$this->_aResponse[0]['Act'] = $lsAct;
		$this->_aResponse[0]['NextAction'] = $lsNextAction;
		$this->_aResponse[0]['Id'] = $lnId;
		$this->_aResponse[0]['Extension'] = $lnExtension;
		$this->_aResponse[0]['Phone'] = $lnPhone;
		$this->_aResponse[0]['Audio'] = $lsAudio;
		$this->_aResponse[0]['Sip'] = $lsSip;
		$this->_aResponse[0]['AudioChannel'] = $lsAudioChannel;
		$this->_aResponse[0]['Play'] = false;
		
		switch ($lsAct)
		{
			case "call":
				$lbReturn = $this->call($lnExtension, $lnPhone);
			break;
			case "hangup":
				$lbReturn = $this->hangup($lsSip);
			break;
			case "play":
				$lbReturn = $this->playAudio($lsSip, $lsAudio);
			break;
			case "stop":
				$lbReturn = $this->stopAudio($lsAudioChannel);
			break;
			case "queue":
				$lbReturn = $this->queue($lnExtension);
			break;
			case "status":
				$this->_nResponseTimeOut = 1;
				$lbReturn = $this->status($lnId, $lnExtension, $lnPhone, $lsAudioChannel);
			break;
		}
		
		if (! isset($this->_aResponse[0]['Response']))
		{
			$this->_aResponse[0]['Response'] = 'Error';
		}
		
		$this->_aResponse[0]['Return'] = $lbReturn;
		$this->_aResponse[0]['ErrorMessage'] = $this->_aError;
		
		return $lbReturn;
	}

	/**
	 *
	 * @param string $psCommandLine
	 * @param boolean $pbClose
	 */
	public function sendCommand ($psCommandLine, $pbClose = false)
	{
		$lsEndLine = "\r\n";
		
		if ($pbClose)
		{
			$lsEndLine .= "\r\n";
		}
		
		fputs($this->_rSocket, "{$psCommandLine}{$lsEndLine}");
	}

	/**
	 *
	 * @return boolean
	 */
	public function getResponse ()
	{
		stream_set_timeout($this->_rSocket, $this->_nResponseTimeOut, $this->_nResponseTimeOutMicro);
		$lsLine = true;
		$lnCount = 0;
		
		$this->_aResponse[0]['Event'] = 'Head';
		
		while (! feof($this->_rSocket) && $lsLine !== false)
		{
			$lsLine = stream_get_line($this->_rSocket, 1024, "\n");
			
			if ($lsLine !== false)
			{
				$laLine = explode(":", trim($lsLine));
				
				if (isset($laLine[1]))
				{
					if ($laLine[0] == 'Event')
					{
						$lnCount ++;
					}
					
					$this->_aResponse[$lnCount][$laLine[0]] = trim($laLine[1]);
				}
			}
		}
		
		if (isset($this->_aResponse[0]['Response']) && $this->_aResponse[0]['Response'] == 'Success')
		{
			return true;
		}
		
		return false;
	}

	/**
	 *
	 * @return boolean
	 */
	public function connect ($psEvent = 'off')
	{
		$this->_rSocket = @fsockopen($this->_aService['host'], $this->_aService['port'], $lnErrno, $lsErrstr, 10);
		
		if ($this->_rSocket)
		{
			if ($this->_bProxyConnection)
			{
				return true;
			}
			else
			{
				$this->sendCommand("Action: Login");
				$this->sendCommand("UserName: {$this->_aService['user']}");
				$this->sendCommand("Secret: {$this->_aService['pass']}");
				$this->sendCommand("Events: {$psEvent}", true);
				
				if ($this->getResponse())
				{
					return true;
				}
				else
				{
					$this->_aError[] = "Asterisk Manager auth error!";
				}
			}
		}
		else
		{
			$this->_aError[] = "Asterisk Manager connect error!";
		}
		
		return false;
	}

	/**
	 *
	 * @param int $pnExtension
	 * @param int $pnPhone
	 * @return boolean
	 */
	public function call ($pnExtension, $pnPhone)
	{
		// Verifica se já existe uma chamada em damento para o ramal informado
		// Se sim ele retorna true e recupera o controle da chamada em curso.
		// Senão tenta iniciar uma nova chamada.
		if ($this->queue($pnExtension))
		{
			return true;
		}
		elseif (! empty($pnExtension) && ! empty($pnPhone))
		{
			if ($this->connect('call'))
			{
				$lsChannel = "Sip/{$pnExtension}_{$this->_aService['context']}";
				
				$this->sendCommand("Action: Originate");
				$this->sendCommand("Channel:{$lsChannel}");
				$this->sendCommand("Exten: {$pnPhone}");
				$this->sendCommand("Context: {$this->_aService['context']}-from-internal");
				$this->sendCommand("Priority: 1");
				$this->sendCommand("ActionId: {$this->_sActionId}-Call");
				$this->sendCommand("Async: yes", true);
				
				if ($this->getResponse())
				{
					$lbReturn = false;
					
					foreach ($this->_aResponse as $laEvent)
					{
						if ($laEvent['Event'] == 'OriginateResponse' && $laEvent['Channel'] == $lsChannel && $laEvent['Exten'] == $pnPhone && $laEvent['Response'] == 'Failure')
						{
							$this->_aResponse[0]['Response'] = $laEvent['Response'];
							
							switch ($laEvent['Reason'])
							{
								case 0:
									$this->_aError[] = "Extension {$pnExtension} not registred!";
								break;
								case 1:
									$this->_aError[] = "Local extension {$pnExtension} no answer!";
								break;
								case 2:
									$this->_aError[] = "Ringing local extension {$pnExtension}!";
								break;
								case 3:
									$this->_aError[] = "Ringing remote phone {$pnPhone}!";
								break;
								case 4:
									$this->_aError[] = "Remote phone {$pnPhone} answered!";
								break;
								case 5:
									$this->_aError[] = "Remote phone {$pnPhone} busy!";
								break;
								case 6:
									$this->_aError[] = "Make it go off hook!";
								break;
								case 7:
									$this->_aError[] = "Line is off hook!";
								break;
								case 8:
									$this->_aError[] = "Congestion!";
								break;
							}
							
							$lbReturn = false;
						}
						elseif ($laEvent['Event'] == 'Newchannel' && $laEvent['CallerIDNum'] == $pnExtension)
						{
							$this->_aResponse[0]['Sip'] = $laEvent['Channel'];
							
							$lbReturn = true;
						}
					}
					
					return $lbReturn;
				}
			}
		}
		else
		{
			$this->_aError[] = "Extension or phone undefined!";
		}
		
		return false;
	}

	/**
	 *
	 * @return boolean
	 */
	public function queue ($pnExtension)
	{
		if (! empty($pnExtension))
		{
			if ($this->connect('call'))
			{
				$this->sendCommand("Action: CoreShowChannels");
				$this->sendCommand("ActionId: {$this->_sActionId}-Call", true);
				
				if ($this->getResponse())
				{
					$lsChannel = "/^SIP\/{$pnExtension}_{$this->_aService['context']}-.*$/";
					$lbReturn = false;
					
					foreach ($this->_aResponse as $laEvent)
					{
						if ($laEvent['Event'] == 'CoreShowChannel' && preg_match($lsChannel, $laEvent['Channel']))
						{
							if (preg_match('/^SIP\/.*-peer\/(.*?),[0-9]{3}/', $laEvent['ApplicationData'], $laPhone))
							{
								$this->_aResponse[0]['Phone'] = $laPhone[1];
							}
							
							$this->_aResponse[0]['Sip'] = $laEvent['Channel'];
						
							$lbReturn = true;
						}
					}
					
					if (!$lbReturn)
					{
						$this->_aResponse[0]['Response'] = "Error";
						$this->_aError[] = "There is no channel for this extension!";
					}
					
					return $lbReturn;
				}
			}
		}
		else
		{
			$this->_aError[] = "Extension undefined!";
		}
		
		return false;
	}

	/**
	 *
	 * @param int $pnId
	 * @param int $pnExtension
	 * @param int $pnPhone
	 * @return boolean
	 */
	public function status ($pnId, $pnExtension, $pnPhone, $psAudioChannel)
	{
		if (! empty($pnId) && ! empty($pnExtension) && ! empty($pnPhone))
		{
			if ($this->connect('call'))
			{
				$this->sendCommand("Action: CoreShowChannels");
				$this->sendCommand("ActionId: {$this->_sActionId}-Status", true);
				
				if ($this->getResponse())
				{
					$lbReturn = false;
					
					foreach ($this->_aResponse as $laEvent)
					{
						if ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['CallerIDnum'] == $pnExtension && $laEvent['ChannelState'] == '5')
						{
							$this->_aResponse[0]['Sip'] = $laEvent['Channel'];
							$this->_aResponse[0]['Act'] = 'ringingExtension';
							$lbReturn = true;
						}
						elseif ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['CallerIDnum'] == $pnExtension && $laEvent['ChannelState'] == '0')
						{
							$this->_aResponse[0]['Sip'] = $laEvent['Channel'];
							$this->_aResponse[0]['Act'] = 'extensionAnswer';
							$lbReturn = true;
						}
						elseif ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['CallerIDnum'] == $pnPhone && $laEvent['ChannelState'] == '5')
						{
							$this->_aResponse[0]['Act'] = 'ringingPhone';
							$lbReturn = true;
						}
						elseif ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['CallerIDnum'] == $pnPhone && $laEvent['ChannelState'] == '0')
						{
							$this->_aResponse[0]['Act'] = 'bridge';
							$lbReturn = true;
						}
						elseif ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['CallerIDnum'] == $pnPhone && $laEvent['ChannelState'] == '6')
						{
							$this->_aResponse[0]['Act'] = 'phoneAnswer';
							$lbReturn = true;
						}
						elseif ($laEvent['Event'] == 'CoreShowChannel' && $laEvent['Application'] == 'MP3Player' && $laEvent['ChannelState'] == '6')
						{
							$this->_aResponse[0]['Play'] = true;
							$lbReturn = true;
						}
					}
					
					if (! $lbReturn)
					{
						$this->_aResponse[0]['Act'] = 'hangup';
					}
					
					return $lbReturn;
				}
			}
		}
		else
		{
			$this->_aError[] = "Id, extension or phone undefined!";
		}
		
		return false;
	}

	/**
	 *
	 * @param string $psSip
	 * @return boolean
	 */
	public function hangup ($psSip)
	{
		if (! empty($psSip))
		{
			if ($this->connect())
			{
				$this->sendCommand("Action: Hangup");
				$this->sendCommand("ActionId: {$this->_sActionId}-HangUp");
				$this->sendCommand("Channel: {$psSip}", true);
				
				if ($this->getResponse())
				{
					return true;
				}
				elseif ($this->_aResponse[0]['Response'] == "Error" && $this->_aResponse[0]['Message'] == "No such channel")
				{
					return true;
				}
			}
		}
		else
		{
			$this->_aError[] = "Undefined Sip channel to hangup!";
		}
		
		return false;
	}

	/**
	 *
	 * @param string $psSip
	 * @param string $psAudio
	 * @return boolean
	 */
	public function playAudio ($psSip, $psAudio)
	{
		if (! empty($psSip) && ! empty($psAudio))
		{
			if ($this->connect('call'))
			{
				$this->sendCommand("Action: Originate");
				$this->sendCommand("ActionId: {$this->_sActionId}-PlayAudio");
				$this->sendCommand("Channel: Local/123@{$this->_aService['context']}-telemessage");
				$this->sendCommand("Exten: s");
				$this->sendCommand("Context: {$this->_aService['context']}-telemessage");
				$this->sendCommand("Priority: 1");
				$this->sendCommand("Variable: chanvar={$psSip}");
				$this->sendCommand("Variable: audiovar={$psAudio}");
				$this->sendCommand("Async: yes", true);
				
				if ($this->getResponse())
				{
					$lbReturn = false;
					
					foreach ($this->_aResponse as $laEvent)
					{
						if ($laEvent['Event'] == 'LocalBridge')
						{
							$this->_aResponse[0]['AudioChannel'] = $laEvent['Channel1'];
							$this->_aResponse[0]['Play'] = true;
							
							$lbReturn = true;
						}
					}
					
					if (! $lbReturn)
					{
						$this->_aResponse[0]['Act'] = 'stop';
						$this->_aResponse[0]['Play'] = false;
					}
					
					return $lbReturn;
				}
			}
		}
		elseif (empty($psSip))
		{
			$this->_aError[] = "Undefined Sip channel to play!";
		}
		elseif (empty($psAudio))
		{
			$this->_aError[] = "Undefined audio path to play!";
		}
		
		return false;
	}

	/**
	 *
	 * @param string $psAudioChannel
	 * @return boolean
	 */
	public function stopAudio ($psAudioChannel)
	{
		if (! empty($psAudioChannel))
		{
			if ($this->connect())
			{
				$this->sendCommand("Action: Hangup");
				$this->sendCommand("ActionId: {$this->_sActionId}-StopAudio");
				$this->sendCommand("Channel: {$psAudioChannel}", true);
				
				if ($this->getResponse())
				{
					$this->_aResponse[0]['Play'] = false;
					$this->_aResponse[0]['AudioChannel'] = '';
					
					return true;
				}
			}
		}
		else
		{
			$this->_aError[] = "Undefined audio channel to stop play!";
		}
		
		return false;
	}
}
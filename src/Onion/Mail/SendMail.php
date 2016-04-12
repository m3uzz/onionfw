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

namespace Onion\Mail;
use Onion\Lib\Util;
use Onion\Log;
use Onion\Config\Config;
use Onion\Mime;

class SendMail
{

	/**
	 * opções de configuração de envio
	 *
	 * @var array $_aOptions
	 */
	private $_aOptions = null;

	/**
	 * array(0=>array(
	 * 'tmp_name'=>'/tmp/upload/xwt4di.jpg',
	 * 'type'=>'image/jpeg',
	 * 'filename'=>'foto.jpg'
	 * ))
	 *
	 * @var array $_aFiles
	 */
	private $_aFiles = null;

	/**
	 *
	 * @param array $paOptions        	
	 * @return \Onion\SendMail
	 */
	public function __construct (array $paOptions = null)
	{
		if ($paOptions !== null)
		{
			$this->_aOptions = $paOptions;
		}
		elseif ($laOptions = Config::getAppOptions())
		{
			$this->_aOptions = $laOptions['mail'];
		}
		else
		{
			$this->_aOptions['transporter'] = "sendmail";
			$this->_aOptions['fromEmail'] = "webmaster@localhost";
			$this->_aOptions['fromName'] = "";
			$this->_aOptions['replayToEmail'] = "";
			$this->_aOptions['replayToName'] = "";
			$this->_aOptions['charset'] = "UTF-8";
			$this->_aOptions['html'] = false;
			$this->_aOptions['smtp']['server'] = "";
			$this->_aOptions['smtp']['security'] = "ssl";
			$this->_aOptions['smtp']['port'] = "465";
			$this->_aOptions['smtp']['login'] = "";
			$this->_aOptions['smtp']['password'] = "";
		}
		
		return $this;
	}

	/**
	 * setAttachement:
	 *
	 * @param string $psPath        	
	 * @param string $psMimeType        	
	 * @param string $psFileName        	
	 * @return \OnionLib\SendMail
	 */
	public function setAttachment ($psPath, $psMimeType, $psFileName)
	{
		if (file_exists($psPath))
		{
			$this->oaFiles = array(
				'tmp_name' => $psPath,
				'type' => $psMimeType,
				'filename' => $psFileName
			);
		}
		
		return $this;
	}

	/**
	 * send:
	 *
	 * @param array|string $pmTo
	 *        	- Who will receve the message, array('email', 'name') or
	 *        	'email'
	 * @param string $psSubject
	 *        	messagem subject
	 * @param string $psBodyText
	 *        	- message in plan text format
	 * @param string $psBodyHtml
	 *        	- message in html format
	 * @param array|string $paFrom
	 *        	- Who send the message, array('email', 'name') or 'email'
	 *        	
	 * @return Zend_Mail
	 */
	public function send ($pmFrom, $psSubject, $pmBody, $pmTo = null)
	{
		$lsFromEmail = null;
		$lsFromName = null;
		$lsToEmail = null;
		$lsToName = null;
		
		$loMail = new Message();
		$loMail->setEncoding($this->_aOptions['charset']);
		
		$loMail->setSender($this->_aOptions["fromEmail"], $this->_aOptions["fromName"]);
		$loMail->addReplyTo($this->_aOptions["replayToEmail"], $this->_aOptions["replayToName"]);
		
		$loMail->setSubject($psSubject);
		
		$loMail->setBody($this->setContainer($pmBody));
		
		if (is_array($pmTo))
		{
			if (isset($pmTo[0]))
			{
				$lsToEmail = $pmTo[0];
			}
			
			if (isset($pmTo[1]))
			{
				$lsToName = $pmTo[1];
			}
		}
		elseif (is_string($pmTo))
		{
			$lsToEmail = $pmTo;
		}
		else 
		{
			$lsToEmail = $this->_aOptions["fromEmail"];
			$lsToName = $this->_aOptions["fromName"];
		}
		
		$loMail->addTo($lsToEmail, $lsToName);
		
		if (is_array($pmFrom))
		{
			if (isset($pmFrom[0]))
			{
				$lsFromEmail = $pmFrom[0];
			}
			
			if (isset($pmFrom[1]))
			{
				$lsFromName = $pmFrom[1];
			}
		}
		elseif (is_string($pmFrom))
		{
			$lsFromEmail = $pmFrom;
		}
		else
		{
			$lsFromEmail = $this->_aOptions["fromEmail"];
			$lsFromName = $this->_aOptions["fromName"];
		}
		
		$loMail->setFrom($lsFromEmail, $lsFromName);
		
		if ($this->_aOptions["transporter"] == "SMTP")
		{
			$loOptionsSMTP = new Transport\SmtpOptions($this->getSMTPOptions());
			$loTransport = new Transport\Smtp($loOptionsSMTP);
			$loTransport->send($loMail);
		}
		elseif ($this->_aOptions["transporter"] == "sendmail")
		{
			$loTransport = new Transport\Sendmail();
			$loTransport->send($loMail);
		}
	}

	public function setContainer ($pmBody)
	{
		$lsBodyText = '';
		$lsBodyHtml = '';
		
		if (is_array($pmBody))
		{
			if (isset($pmBody['text']))
			{
				$lsBodyText = $pmBody['text'];
			}
			
			if (isset($pmBody['html']))
			{
				$lsBodyHtml = $pmBody['html'];
			}	
		}
		else
		{
			$lsBodyText = $pmBody;
		}
		
		$loText = new Mime\Part($lsBodyText);
		$loText->type = "text/plain";
		$laBody[] = $loText;
		
		if (Util::toBoolean($this->_aOptions["html"]))
		{
			$loHtml = new Mime\Part($lsBodyHtml);
			$loHtml->type = "text/html";
			$laBody[] = $loHtml;
		}
		
		if (is_array($this->_aFiles))
		{
			foreach ($this->_aFiles as $laFile)
			{
				$loAttachment = new Mime\Part(file_get_contents($laFile['tmp_name']));
				$loAttachment->type = $laFile['type'];
				$loAttachment->disposition = 'attachment';
			}
			
			$laBody[] = $loAttachment;
		}
		
		$loBody = new Mime\Message();
		$loBody->setParts($laBody);
		
		return $loBody;
	}

	public function getSMTPOptions ()
	{
		if (! empty($this->_aOptions["smtp"]["security"]))
		{
			return array(
				'name' => $this->_aOptions["smtp"]["server"],
				'host' => $this->_aOptions["smtp"]["server"],
				'port' => $this->_aOptions["smtp"]["port"],
				'connection_class' => 'login',
				'connection_config' => array(
					'username' => $this->_aOptions["smtp"]["login"],
					'password' => $this->_aOptions["smtp"]["password"],
					'ssl' => $this->_aOptions["smtp"]["security"]
				)
			);
		}
	}
}
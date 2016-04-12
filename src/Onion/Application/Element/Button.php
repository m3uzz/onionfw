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

namespace Onion\Application\Element;
use Onion\Application\AbstractApp;
use Onion\Application\InterfaceApp;
use Onion\Log\Debug;
use Zend\Db\Sql\Ddl\Column\Boolean;
use Onion\Application\Action;

class Button extends AbstractApp
{

	/**
	 * Request type: ajax, http
	 * 
	 * @var string
	 */
	protected $_request = 'ajax';
	
	/**
	 * Response type: html, json, text
	 * 
	 * @var string
	 */
	protected $_response = 'json';
	
	/**
	 * Open target: body, popup, modal, window, hidden 
	 *  
	 * @var string
	 */
	protected $_target = 'body';
	
	/**
	 * Url to the action
	 * 
	 * @var string
	 */
	protected $_href = "#";
	
	/**
	 * If the link is an external link
	 * 
	 * @var Boolean
	 */
	protected $_external = false;
	
	/**
	 * Object action
	 * 
	 * @var object Onion\Application\Action
	 */
	protected $_action = null;

	/**
	 * Params to pass for action
	 * 
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * @var string
	 */
	protected $_class = '';

			
	// Settings
	
	/**
	 * Construct an object setting the id, name and resource properties
	 * if the id is not given the construct will return an exception
	 *
	 * @param string $psId
	 *        	- Instance identifier.
	 * @param string $psResource
	 * @throws Exception
	 */	
	public function __construct ($psId, $psResource = null)
	{
		return parent::__construct($psId, $psResource);
	}
	
	/**
	 * Request type: ajax, http
	 * 
	 * @param string $psRequest
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setRequest ($psRequest)
	{
		if(strtolower($psRequest) === 'ajax' || strtolower($psRequest) === 'html')
		{
			$this->_request = $psRequest;
		}
		else
		{
			throw new Exception ('The request value should be ajax or html!'); 	
		}
		
		return $this;
	}
	
	/**
	 * Response type: html, json, text
	 *
	 * @param string $psResponse
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setResponse ($psResponse)
	{
		switch(strtolower($psResponse))
		{
			case 'json':
			case 'html':
			case 'text':
				$this->_response = $psResponse;
			break;
			
			default:
				throw new Exception ('The response value should be html, json or text!');
		}
	
		return $this;
	}	
	
	/**
	 * Open target: body, popup, window, hidden 
	 * 
	 * @param string $psTarget
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setTarget ($psTarget)
	{
		switch(strtolower($psTarget))
		{
			case 'body':
			case 'popup':
			case 'modal':
			case 'window':
			case 'hidden':											
				$this->_target = $psTarget;
			break;
			
			default:
				throw new Exception ('The target value should be body, popup, modal, window or hidden!');
		}
		
		return $this;
	}
	
	/**
	 * If the link is an external link
	 * 
	 * @param boolean $pbExternal
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setExternal ($pbExternal)
	{
		if (is_bool($pbExternal))
		{
			$this->_external = $pbExternal;
		}
		else
		{
			throw new Exception('The value of "external" property need to be a boolean!');
		}
				
		return $this;
	}

	/**
	 * Action object
	 * 
	 * @param object $poAction
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setAction ($poAction)
	{
		if ($poAction instanceof Action)
		{
			$this->_action = $poAction;
		}
		else 
		{
			throw new Exception('The action value should be an Onion\Application\Action object!');	
		}
		
		return $this;
	}

	/**
	 * Params to pass for action
	 * 
	 * @param array $paParams
	 * @throws Exception
	 * @return Onion\Application\Element\Button
	 */
	public function setParams ($paParams)
	{
		if (is_array($paParams))
		{
			$this->_params = $paParams;
		}
		else 
		{
			throw new Exception('The params value should be an array!');
		}	
			
		return $this;
	}
	
	/**
	 *
	 * @param string $psHref        	
	 * @return Onion\Application\Element\Button
	 */
	public function setHref ($psHref)
	{
		if (!empty($psHref))
		{
			$this->_href = $psHref;
			
			$laResources = explode(":", $this->_resource);
			
			if (count($laResources) == 2 || empty($this->_resource))
			{
				$lsResource = str_replace("/", ":", $psHref);
				
				if (substr($lsResource, 0, 1) == ":")
				{
					$lsResource = substr($lsResource, 1);
				}
				
				$this->setResource($lsResource);
			}
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $psClass
	 * @return \Onion\Application\Element\Button
	 */
	public function setClass ($psClass)
	{
		if (!empty($psClass))
		{
			$this->_class = $psClass;
		}
	
		return $this;
	}	
	
	public function render ()
	{
		$laParams = $this->get('params');
		$lsParams = '';
		
		if (is_array($laParams))
		{
			foreach ($laParams as $lsName => $lsValue)
			{
				$lsParams .= "{$lsName}=\"{$lsValue}\" ";		
			}
		}
		
		$lsButton = '
			<a 
				id="onion-button-' . $this->get('id') . '"
				class="' . $this->get('class') . '" 
				href="' . $this->get('href') . '"
				title="' . $this->get('description') . '"
				data-target="' . $this->get('target') . '"
				' . $lsParams . '
			>
					<i class="glyphicon glyphicon-' . $this->get('icon') . '"></i> ' . $this->get('title') . '
			</a>';
		
		return $lsButton;
	}
}
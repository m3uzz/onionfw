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

class Filter extends AbstractApp
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
	 * Open target: body, popup, window, hidden
	 *
	 * @var string
	 */
	protected $_target = 'body';
	
	/**
	 * Filter type: select, multiselect, input, tree
	 *
	 * @var string
	 */
	protected $_type = "select";
	
	/**
	 * Filter Options
	 *
	 * @var array
	 */
	protected $_options = array();
	
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
	 * Url to the action
	 *
	 * @var string
	 */
	protected $_href = "";	
	
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
	 * @return Onion\Application\Element\Filter
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
	 * @return Onion\Application\Element\Filter
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
	 * @return Onion\Application\Element\Filter
	 */
	public function setTarget ($psTarget)
	{
		switch(strtolower($psTarget))
		{
			case 'body':
			case 'popup':
			case 'window':
			case 'hidden':
				$this->_target = $psTarget;
				break;
					
			default:
				throw new Exception ('The target value should be body, popup, window or hidden!');
		}
	
		return $this;
	}
	
	/**
	 * Filter Type: select, multiselect, input, tree
	 *
	 * @param string $psType
	 * @throws Exception
	 * @return Onion\Application\Element\Filter
	 */
	public function setType ($psType)
	{
		switch(strtolower($psType))
		{
			case 'select':
			case 'multiselect':
			case 'input':
			case 'tree':
				$this->_type = $psType;
				break;
					
			default:
				throw new Exception ('The type value should be select, multiselect, input, tree!');
		}
	
		return $this;
	}
		
	/**
	 * Action object
	 *
	 * @param object $poAction
	 * @throws Exception
	 * @return Onion\Application\Element\Filter
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
	 * Filter options
	 *
	 * @param array $paOptions
	 * @throws Exception
	 * @return Onion\Application\Element\Filter
	 */
	public function setOptions ($paOptions)
	{
		if (is_array($paOptions))
		{
			$this->_options = $paOptions;
		}
		else
		{
			throw new Exception('The options value should be an array!');
		}
			
		return $this;
	}
	
	/**
	 *
	 * @param string $psHref
	 * @return Onion\Application\Element\Filter
	 */
	public function setHref ($psHref)
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
	
		return $this;
	}
}
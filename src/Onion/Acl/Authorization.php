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
use \Zend\Mvc\Controller\Plugin\AbstractPlugin;
use \Zend\Authentication\AuthenticationService;
use \Zend\Authentication\Adapter\DbTable as AuthAdapter;

class Authorization extends AbstractPlugin
{

	/**
	 *
	 * @var AuthAdapter
	 */
	protected $_authAdapter = null;

	/**
	 *
	 * @var AuthenticationService
	 */
	protected $_authService = null;

	/**
	 * Check if Identity is present
	 *
	 * @return bool
	 */
	public function hasIdentity ()
	{
		return $this->getAuthService()->hasIdentity();
	}

	/**
	 * Return current Identity
	 *
	 * @return mixed null
	 */
	public function getIdentity ()
	{
		return $this->getAuthService()->getIdentity();
	}

	/**
	 * Sets Auth Adapter
	 *
	 * @param \Zend\Authentication\Adapter\DbTable $authAdapter        	
	 * @return UserAuthentication
	 */
	public function setAuthAdapter (AuthAdapter $authAdapter)
	{
		$this->_authAdapter = $authAdapter;
		
		return $this;
	}

	/**
	 * Returns Auth Adapter
	 *
	 * @return \Zend\Authentication\Adapter\DbTable
	 */
	public function getAuthAdapter ()
	{
		if ($this->_authAdapter === null)
		{
			$this->setAuthAdapter(new AuthAdapter());
		}
		
		return $this->_authAdapter;
	}

	/**
	 * Sets Auth Service
	 *
	 * @param \Zend\Authentication\AuthenticationService $authService        	
	 * @return UserAuthentication
	 */
	public function setAuthService (AuthenticationService $authService)
	{
		$this->_authService = $authService;
		
		return $this;
	}

	/**
	 * Gets Auth Service
	 *
	 * @return \Zend\Authentication\AuthenticationService
	 */
	public function getAuthService ()
	{
		if ($this->_authService === null)
		{
			$this->setAuthService(new AuthenticationService());
		}
		
		return $this->_authService;
	}
}
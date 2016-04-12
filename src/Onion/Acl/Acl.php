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
use \Zend\Permissions\Acl\Acl as ZendAcl;
use \Zend\Permissions\Acl\Role\GenericRole as Role;
use \Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends ZendAcl
{

	/**
	 * Default Role
	 */
	const DEFAULT_ROLE = 'guest';

	/**
	 * Constructor
	 *
	 * @param array $config        	
	 * @return void
	 * @throws \Exception
	 */
	public function __construct ($paConfig)
	{
		if (!isset($paConfig['acl']['roles']) || !isset($paConfig['acl']['resources']))
		{
			throw new \Exception('Invalid ACL Config found');
		}
		
		$laRoles = $paConfig['acl']['roles'];
		
		if (! isset($laRoles[self::DEFAULT_ROLE]))
		{
			$laRoles[self::DEFAULT_ROLE] = '';
		}
		
		$this->_addRoles($laRoles);
		$this->_addResources($paConfig['acl']['resources']);
	}

	/**
	 * Adds Roles to ACL
	 *
	 * @param array $roles        	
	 * @return User\Acl
	 */
	protected function _addRoles (array $paRoles)
	{
		foreach ($paRoles as $lsName => $lsParent)
		{
			if (!$this->hasRole($lsName))
			{
				if (empty($lsParent))
				{
					$lsParent = array();
				}
				else
				{
					$lsParent = explode(',', $lsParent);
				}
				
				$this->addRole(new Role($lsName), $lsParent);
			}
		}
		
		return $this;
	}

	/**
	 * Adds Resources to ACL
	 *
	 * @param
	 *        	$resources
	 * @return User\Acl
	 * @throws \Exception
	 */
	protected function _addResources (array $paResources)
	{
		foreach ($paResources as $lsPermission => $laControllers)
		{
			if (is_array($laControllers))
			{
				foreach ($laControllers as $lsController => $laActions)
				{
					if ($lsController == 'all')
					{
						$lsController = null;
					}
					else
					{
						if (!$this->hasResource($lsController))
						{
							$this->addResource(new Resource($lsController));
						}
					}
					
					foreach ($laActions as $lsAction => $lsRole)
					{
						if ($lsAction == 'all')
						{
							$lsAction = null;
						}
						
						if ($lsPermission == 'allow')
						{
							$this->allow($lsRole, $lsController, $lsAction);
						}
						elseif ($lsPermission == 'deny')
						{
							$this->deny($lsRole, $lsController, $lsAction);
						}
						else
						{
							throw new \Exception('No valid permission defined: ' . $lsPermission);
						}
					}
				}
			}
		}
		
		return $this;
	}
}
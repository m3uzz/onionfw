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

namespace Onion\Entity;
use Onion\Config\Config;
use Onion\Log\Debug;
use Onion\Lib\String;

abstract class Entity
{
	protected $_oInputFilter;	
	
	/**
	 * Magic setter to save protected properties.
	 *
	 * @param string $psProperty
	 * @param mixed $pmValue
	 */
	public function __set ($psProperty, $pmValue)
	{
		$this->set($psProperty, $pmValue);
	}

	public function set ($psProperty, $pmValue)
	{
		if (property_exists($this, $psProperty))
		{
			$lsMethod = 'set'.$psProperty;
			
			if (method_exists($this, $lsMethod))
			{
				$this->$lsMethod($pmValue);
			}
			else 
			{
				$this->$psProperty = $pmValue;
			}
		}
		
		return $this;
	}
	
	/**
	 * Magic getter to expose protected properties.
	 *
	 * @param string $psProperty
	 * @return mixed
	 */
	public function __get ($psProperty)
	{
		return $this->get($psProperty);
	}
	
	public function get ($psProperty)
	{
		if (property_exists($this, $psProperty))
		{
			return $this->$psProperty;
		}
	}
	
	/**
	 * Convert the object to an array.
	 *
	 * @return array
	 */
	public function getArrayCopy ()
	{
		$laProperties = get_object_vars($this);
		
		if (is_array($laProperties))
		{
			foreach ($laProperties as $lsKey => $lmValue)
			{
				if (substr($lsKey, 0, 1) === '_')
				{
					unset($laProperties[$lsKey]);
				}
			}
		}
		
		return $laProperties;
	}
	
	public function getEntityId ()
	{
		return (int)$this->id;	
	}
	
	/**
	 * Populate from an array.
	 *
	 * @param array $paData
	 */
	public function populate ($paData = array())
	{
		if (is_array($paData))
		{
			foreach ($paData as $lsProperty => $lmValue)
			{
				if (property_exists($this, $lsProperty))
				{
					$lsMethod = 'set' . ucfirst($lsProperty);
					
					if (method_exists($this, $lsMethod ))
					{
						$this->$lsMethod($lmValue);	
					}
					else 
					{
						$this->$lsProperty = String::escapeString($lmValue);
					}
				}
			}
		}	
	}
	
	public function setDefault ($psTable)
	{
		$laConfigTable = Config::getAppOptions('table');

		if (isset($laConfigTable[$psTable]) && is_array($laConfigTable[$psTable]))
		{
			foreach ($laConfigTable[$psTable] as $lsField => $laValues)
			{
				if (property_exists($this, $lsField))
				{
					$this->$lsField = $laValues['default'];
				}
			}
		}
		
		$this->dtInsert = date('Y-m-d H:i:s');
	}
	
	public function isSystem ()
	{
		if (property_exists($this, 'isSystem') && $this->isSystem == '1')
		{
			return true;
		}
			
		return false;
	}
	
	public function moveTo ($pnNumStatus = null, $pbIsActive = null)
	{
		if ($pnNumStatus === null && $pbIsActive === null)
		{
			$this->isActive = ($this->isActive ? 0 : 1);
			$this->numStatus = ($this->numStatus ? 0 : 1);
		}
		else
		{
			$this->isActive = $pbIsActive;
			$this->numStatus = $pnNumStatus;
		}		
	}
	
	public function getObject ()
	{
		return $this;
	}
	
	public function getFormatedData ()
	{
		return $this->getArrayCopy();
		//Implementar na Entity, declarado aqui só pra garantir que não haverá erro;
	}

	public function addValidate ()
	{
		//Implementar na Entity, declarado aqui só pra garantir que não haverá erro;
	}
	
	public function editValidate ()
	{
		//Implementar na Entity, declarado aqui só pra garantir que não haverá erro;
	}
}
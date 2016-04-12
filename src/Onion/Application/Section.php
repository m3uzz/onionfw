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

namespace Onion\Application;
use Onion\Log\Debug;

class Section extends AbstractApp implements InterfaceApp
{
	
	/**
	 * Module register
	 * 
	 * @var array object
	 */
	protected $_modules = array();
	
	/**
	* Used to mount the menu
	 * 
	 * @var array object Onion\Application\Element\Options
	 */
	protected $_options = null;
	
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
	
	// Action methods
	
	/**
	 * Create a new Module object into the array object
	 * and setting its id and name
	 *
	 * @param string $psModuleId        	
	 * @return Onion\Application\Module
	 */
	public function createModule ($psModuleId)
	{
		return parent::create('_modules', $psModuleId);
	}

	/**
	 * Add an existent Module object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Module $poModule        	
	 * @param string $psIndex        	
	 * @param string $pnPosition        	
	 * @throws Exception
	 * @return Onion\Application\Section
	 */
	public function addModule ($poModule, $psIndex = null, $pnPosition = null)
	{
		if ($poModule instanceof Module)
		{
			return parent::add('_modules', $poModule, $psIndex, $pnPosition);
		}
		else
		{
			throw new Exception('$poModule should be a instance of Onion\Application\Module!');
		}
	}

	/**
	 * Remove a Module from the array object
	 *
	 * @param string $psModuleId        	
	 * @return Onion\Application\Section
	 */
	public function removeModule ($psModuleId)
	{
		return parent::remove('_modules', $psModuleId);
	}

	/**
	 * Load the Module object from array object
	 * or the entire array if $psModuleId = null
	 *
	 * @param string $psModuleId        	
	 * @param boolean $pbValid        	
	 * @throws Exception
	 * @return Onion\Application\Module array null
	 */
	public function getModule ($psModuleId = null, $pbValid = true)
	{
		return parent::getElement('_modules', $psModuleId, $pbValid);
	}

	/**
	 * Create a new Module object into the array object
	 * and setting its id and name
	 *
	 * @param string $psModuleId        	
	 * @return Onion\Application\Module
	 */
	public function createOption ($psModuleId)
	{
		return parent::create('_options', $psModuleId);
	}

	/**
	 * Add an existent Module object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Module $poModule
	 * @param string $psIndex
	 * @param string $pnPosition
	 * @throws Exception
	 * @return Onion\Application\Section
	 */
	public function addOption ($poModule, $psIndex = null, $pnPosition = null)
	{
		if ($poModule instanceof Module)
		{
			return parent::add('_options', $poModule, $psIndex, $pnPosition);
		}
		else
		{
			throw new Exception('$poModule should be a instance of Onion\Application\Module!');
		}
	}
	
	/**
	 * Remove a Module from the array object
	 *
	 * @param string $psModuleId
	 * @return Onion\Application\Section
	 */
	public function removeOption ($psModuleId)
	{
		return parent::remove('_options', $psModuleId);
	}
	
	/**
	 * Load the Module object from array object
	 * or the entire array if $psModuleId = null
	 *
	 * @param string $psModuleId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Module array null
	 */
	public function getOption ($psModuleId = null, $pbValid = true)
	{
		return parent::getElement('_options', $psModuleId, $pbValid);
	}
	
	/**
	 * Change the Section value from the internal object elements
	 *
	 * @param string $psSectionName        	
	 * @param string $psParentId        	
	 * @return Onion\Application\Section
	 */
	public function replaceSection ($psSectionName = "", $psParentId = "")
	{
		return parent::_replaceSection('_modules', $this->_id, $this->_id);
	}

	/**
	 * Return an array lagacy to the old version of the object
	 *
	 * @param $psParams
	 * @return array|null
	 */	
	public function legacy($psParams = "")
	{
		if ($this->_enable)
		{
			$laReturn['id'] = $this->_id;
			$laReturn['nome'] = $this->_name;
			$laReturn['icone'] = $this->_icon;
			
			if (is_array($this->_modules))
			{
				foreach ($this->_modules as $lsModulo => $loObj)
				{
					$laArray = is_object($loObj) ? $loObj->legacy() : $loObj;
					
					if ($laArray !== null)
					{
						$laReturn['modulos'][$lsModulo] = $laArray;
					}
				}
			}
			
			return $laReturn;
		}
		
		return null;
	}
}
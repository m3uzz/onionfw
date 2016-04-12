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
use Onion\Application\Element\Options;

class Module extends AbstractApp
{
		
	/**
	 * Controller register
	 * 
	 * @var array object Onion\Application\Controller
	 */
	protected $_controllers = array();
	
	/**
	 * 
	 * @var array object Onion\Application\Element\Options
	 */
	protected $_options = array();
	                                  
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
	
	// Action methods
	
	/**
	 * Create a new Controller object into the array object
	 * and setting its id and name
	 *
	 * @param string $psControllerId        	
	 * @return Onion\Application\Controller
	 */
	public function createController ($psControllerId)
	{
		return parent::create('_controllers', $psControllerId);
	}

	/**
	 * Add an existent Controller object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Controller $poController        	
	 * @param string $psIndex        	
	 * @param string $pnPosition        	
	 * @throws Exception
	 * @return Onion\Application\Module
	 */
	public function addController ($poController, $psIndex = null, $pnPosition = null)
	{
		if ($poController instanceof Controller)
		{
			return parent::add('_controllers', $poController, $psIndex, $pnPosition);
		}
		else
		{
			throw new Exception('$poController should be a instance of Onion\Application\Controller!');
		}
	}

	/**
	 * Remove a Controller from the array object
	 *
	 * @param string $psControllerId        	
	 * @return Onion\Application\Module
	 */
	public function removeController ($psControllerId)
	{
		return parent::remove('_controllers', $psControllerId);
	}

	/**
	 * Load the Controller object from array object
	 * or the entire array if $psControllerId = null
	 *
	 * @param string $psControllerId        	
	 * @param boolean $pbValid        	
	 * @throws Exception
	 * @return Onion\Application\Controller array null
	 */
	public function getController ($psControllerId = null, $pbValid = true)
	{
		return parent::getElement('_controllers', $psControllerId, $pbValid);
	}

	/**
	 * Create a new Options object into the array object
	 * and setting its id and name
	 *
	 * @param string $psOptionsId
	 * @return Onion\Application\Element\Options
	 */
	public function createOptions ($psOptionsId)
	{
		return $this->_options[] = new Options($psOptionsId, $this->_resource);
	}
	
	/**
	 * Add an existent Options object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Options $poOptions
	 * @param string $psIndex
	 * @param string $pnPosition
	 * @throws Exception
	 * @return Onion\Application\Module
	 */
	public function addOptions ($poOptions, $psIndex = null, $pnPosition = null)
	{
		if ($poOptions instanceof Options)
		{
			return parent::add('_options', $poOptions, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poOptions should be a instance of Onion\Application\Element\Options!');
		}
	}
	
	/**
	 * Remove a Options from the array object
	 *
	 * @param string $pmOptionsId
	 * @return Onion\Application\Module
	 */
	public function removeOptions ($pmOptionsId)
	{
		return parent::remove('_options', $pmOptionsId);
	}
	
	/**
	 * Load the Options object from array object
	 * or the entire array if $pmOptionsId = null
	 *
	 * @param string $pmOptionsId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Element\Options array null
	 */
	public function getOptions ($pmOptionsId = null, $pbValid = true)
	{
		return parent::getElement('_options', $pmOptionsId, $pbValid);
	}	
	
	/**
	 * Change the Section value from the internal object elements
	 *
	 * @param string $psSectionName        	
	 * @param string $psParentId        	
	 * @return Onion\Application\Module
	 */
	public function replaceSection ($psSectionName = "", $psParentId = "")
	{
		return parent::_replaceSection('_controllers', $psSectionName, $this->_id);
	}
}
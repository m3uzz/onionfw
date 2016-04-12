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
use Onion\Application\Element\Grid;
use Onion\Application\Element\Filter;

class Action extends AbstractApp
{

	/**
	 * 
	 * @var array object
	 */
	protected $_options = array();
	
	/**
	 * 
	 * @var array object
	 */
	protected $_grid = array();
	
	/**
	 * 
	 * @var array object
	 */
	protected $_filter = array();

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
	 * @return Onion\Application\Action
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
	 * @return Onion\Application\Action
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
	 * Create a new Grid object into the array object
	 * and setting its id and name
	 *
	 * @param string $psGridId        	
	 * @return Onion\Application\Element\Grid
	 */
	public function createGrid ($psGridId)
	{
		return $this->_grid[] = new Grid($psGridId, $this->getResourceBase());
	}

	/**
	 * Add an existent Grid object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	*
	 * @param Onion\Application\Element\Grid $poGrid        	
	 * @param string $psIndex        	
	 * @param int $pnPosition        	
	 * @return Onion\Application\Action        	
	 */
	public function addGrid ($poGrid, $psIndex = null, $pnPosition = null)
	{
		if ($poGrid instanceof Grid)
		{
			return parent::add('_grid', $poGrid, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poGrid should be a instance of Onion\Application\Element\Grid!');
		}
	}

	/**
	 * Remove a Grid from the array object
	 *
	 * @param int|string $pmGridId        	
	 * @return Onion\Application\Action
	 */
	public function removeGrid ($pmGridId)
	{
		return parent::remove('_grid', $pmGridId);
	}

	/**
	 * Load the Grid object from array object
	 * or the entire array if $pmGridId = null
	 *
	 * @param int|string $pmGridId        	
	 * @param boolean $pbValid        	
	 * @throws Exception
	 * @return Onion\Application\Element\Grid|array|null
	 */
	public function getGrid ($pmGridId = null, $pbValid = true)
	{
		return parent::getElement('_grid', $pmGridId, $pbValid);
	}

	/**
	 * Create a new Filter object into the array object
	 * and setting its id and name
	 *
	 * @param string $psFilterId
	 * @return Onion\Application\Element\Filter
	 */
	public function createFilter ($psFilterId)
	{
		return $this->_filter[] = new Filter($psFilterId, $this->_resource);
	}
	
	/**
	 * Add an existent Filter object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Filter $poFilter
	 * @param string $psIndex
	 * @param string $pnPosition
	 * @throws Exception
	 * @return Onion\Application\Action
	 */
	public function addFilter ($poFilter, $psIndex = null, $pnPosition = null)
	{
		if ($poFilter instanceof Filter)
		{
			return parent::add('_filter', $poFilter, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poFilter should be a instance of Onion\Application\Element\Filter!');
		}
	}
	
	/**
	 * Remove a Filter from the array object
	 *
	 * @param string $pmFilterId
	 * @return Onion\Application\Action
	 */
	public function removeFilter ($pmFilterId)
	{
		return parent::remove('_filter', $pmFilterId);
	}
	
	/**
	 * Load the Filter object from array object
	 * or the entire array if $pmFilterId = null
	 *
	 * @param string $pmFilterId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Element\Filter array null
	 */
	public function getFilter ($pmFilterId = null, $pbValid = true)
	{
		return parent::getElement('_filter', $pmFilterId, $pbValid);
	}
	
	/**
	 * Change the Section value from the internal object elements
	 *
	 * @param string $psSectionName
	 * @param string $psParentId
	 * @return Onion\Application\Action
	 */	
	public function replaceSection ($psSectionName = "", $psParentId = "")
	{
		parent::_replaceSection('_options', $psSectionName, $psParentId);
		
		parent::_replaceSection('_grid', $psSectionName, $psParentId);
		
		parent::_replaceSection('_filter', $psSectionName, $psParentId);
		
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function getResourceBase ()
	{
		$lsResource = null;
		
		if (! empty($this->_resource))
		{
			$laResource = explode(":", $this->_resource);
			
			$lsResource = isset($laResource[0]) ? $laResource[0] : "";
			$lsResource .= isset($laResource[1]) ? ":" . $laResource[1] : "";
		}
		
		return $lsResource;
	}
}
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

class Options extends AbstractApp
{

	/**
	 * Active Iten
	 *
	 * @var string
	 */
	protected $_active = "";
	
	/**
	 * List type: horizontal, vertical, submenu, tab
	 * 
	 * @var string
	 */
	protected $_type = "horizontal";
	
	/**
	 * Menu position: top, botton, left, right;
	 *
	 * @var string
	 */
	protected $_position = 'top';
	
	/**
	 * Presentation mode: closed, opened;
	 * @var string
	 */
	protected $_presentation = '';
	
	/**
	 * Iten exibition type: icon, title, both
	 *
	 * @var string
	 */
	protected $_exibition = 'both';
	
	/**
	 * Menu order: alphabetic, insertion
	 *
	 * @var string
	 */
	protected $_order = 'alphabetic';	

	/**
	 * Action itens
	 * 
	 * @var array object Onion\Application\Element\Button
	 */
	protected $_itens = array();
	
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
	public function __construct ($psId, $psResource = null, $poParent = null)
	{
		return parent::__construct($psId, $psResource, $poParent);
	}
	
	/**
	 * Active Iten
	 *  
	 * @param string $psActive
	 * @return Onion\Application\Element\Menu
	 */
	public function setActive ($psActive)
	{
		$this->_active = $psActive;
		
		return $this;
	}
	
	/**
	 * List type: horizontal, vertical, submenu, tab
	 *  
	 * @param string $psType
	 * @return Onion\Application\Element\Menu
	 */
	public function setType ($psType)
	{
		$laOptions = array(
			'horizontal' => 1,
			'vertical' => 1,
			'submenu' => 1,
			'tab' => 1,
		);
		
		if(isset($laOptions[$psType]))
		{
			$this->_active = $psType;
		}
		else
		{
			throw new Exception ('Menu type option error, try: horizontal, vertical, submenu, tab');
		}
		
		return $this;
	}
	
	/**
	 * Menu position: top, botton, left, right;
	 * 
	 * @param string $psPosition
	 * @return Onion\Application\Element\Menu
	 */
	public function setPosition ($psPosition)
	{
		$laOptions = array(
			'top' => 1,
			'botton' => 1,
			'left' => 1,
			'right' => 1,
		);
		
		if(isset($laOptions[$psPosition]))
		{
			$this->_position = $psPosition;
		}
		else
		{
			throw new Exception ('Menu position option error, try: top, botton, left, right');
		}
		
		return $this;
	}
	
	/**
	 * Presentation mode: closed, opened;
	 * 
	 * @param string $psPresentation
	 * @return Onion\Application\Element\Menu
	 */
	public function setPresentation ($psPresentation)
	{
		$laOptions = array(
			'closed' => 1,
			'opened' => 1,
		);
		
		if(isset($laOptions[$psPresentation]))
		{
			$this->_presentation = $psPresentation;
		}
		else
		{
			throw new Exception ('Menu presentatio option error, try: closed, opened;');
		}
		
		return $this;
	}
	
	/**
	 * Iten exibition type: icon, title, both
	 * 
	 * @param string $psExibition
	 * @return Onion\Application\Element\Menu
	 */
	public function setExibition ($psExibition)
	{
		$laOptions = array(
			'icon' => 1,
			'title' => 1,
			'both' => 1,
		);
		
		if(isset($laOptions[$psExibition]))
		{
			$this->_exibition = $psExibition;
		}
		else
		{
			throw new Exception ('Menu exibition option error, try: icon, title, both');
		}
		
		return $this;
	}
	
	/**
	 * Menu order: alphabetic, insertion
	 * 
	 * @param string $psOrder
	 * @return Onion\Application\Element\Menu
	 */
	public function setOrder ($psOrder)
	{
		$laOptions = array(
			'alphabetic' => 1,
			'insertion' => 1,
		);
		
		if(isset($laOptions[$psOrder]))
		{
			$this->_order = $psOrder;
		}
		else
		{
			throw new Exception ('Menu order option error, try: alphabetic, insertion');
		}
		
		return $this;
	}
	
	// Action methods
	
	/**
	 * Create a new Button object into the array object
	 * and setting its id and name
	 *
	 * @param string $psButtonId        	
	 * @return Onion\Application\Element\Button
	 */
	public function createIten ($psButtonId)
	{
		return $this->_itens[] = new Button($psButtonId, $this->getResourceBase());
	}

	/**
	 * Add an existent Button object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Button $poButton        	
	 * @param string $psIndex        	
	 * @param string $pnPosition        	
	 * @throws Exception
	 * @return Onion\Application\Element\Options
	 */
	public function addIten ($poButton, $psIndex = null, $pnPosition = null)
	{
		if ($poButton instanceof Button)
		{
			return parent::add('_itens', $poButton, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poButton should be a instance of Onion\Application\Element\Button!');
		}
	}

	/**
	 * Remove a Button from the array object
	 *
	 * @param string $pmButtonId
	 * @return Onion\Application\Element\Options
	 */
	public function removeIten ($pmButtonId)
	{
		return parent::remove('_itens', $pmButtonId);
	}

	/**
	 * Load the Button object from array object
	 * or the entire array if $pmButtonId = null
	 *
	 * @param string $pmButtonId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Element\Button|array|null
	 */
	public function getIten ($pmButtonId = null, $pbValid = true)
	{
		return parent::getElement('_toolbar', $pmButtonId, $pbValid);
	}	
		
	/**
	 * Create a new Options object into the array object
	 * and setting its id and name
	 *
	 * @param string $psOptionId
	 * @return Onion\Application\Element\Options
	 */
	public function createOptions ($psOptionId)
	{
		return $this->_itens[] = new Options($psOptionId, $this->getResourceBase());
	}
	
	/**
	 * Add an existent Options object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Options $poOption
	 * @param string $psIndex
	 * @param string $pnPosition
	 * @throws Exception
	 * @return Onion\Application\Element\Options
	 */
	public function addOptions ($poOption, $psIndex = null, $pnPosition = null)
	{
		if ($poOption instanceof Options)
		{
			return parent::add('_itens', $poOption, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poOption should be a instance of Onion\Application\Element\Options!');
		}
	}
	
	/**
	 * Remove an Options from the array object
	 *
	 * @param string $pmOptionId
	 * @return Onion\Application\Element\Options
	 */
	public function removeOptions ($pmOptionId)
	{
		return parent::remove('_itens', $pmOptionId);
	}
	
	/**
	 * Load the Options object from array object
	 * or the entire array if $pmOptionId = null
	 *
	 * @param string $pmOptionId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Element\Options|array|null
	 */
	public function getOptions ($pmOptionId = null, $pbValid = true)
	{
		return parent::getElement('_toolbar', $pmOptionId, $pbValid);
	}
	
	/**
	 * Add a separate to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param string $pnIndex
	 * @param string $pnPosition
	 * @return Onion\Application\Element\Options
	 */	
	public function addSeparate ($pnIndex = null, $pnPosition = null)
	{
		return parent::add('_itens', null, $pnIndex, $pnPosition, true);	
	}
	
	/**
	 * Remove a separate from the array object
	 *
	 * @param string $pnIndex
	 * @return Onion\Application\Element\Options
	 */
	public function removeSeparate ($pnIndex)
	{
		return parent::remove('_itens', $pnIndex);
	}
	
	
	public function render($a, $b, $c)
	{
	    
	}
}
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

abstract class AbstractApp
{

	/**
	 * Define if the object is enable
	 * @default false
	 *
	 * @var boolean
	 */
	protected $_enable = true;

	/**
	 * Define if the object instance is a clone of the original object
	 * In this case every change will not affect the original object
	 * @default false
	 *
	 * @var boolean
	 */
	protected $_clone = false;

	/**
	 * The parent object
	 *  
	 * @var object
	 */
	protected $_parent = '';
	
	/**
	 * Define the client folder
	 *
	 * @example onion.com
	 * @var string
	 */
	protected $_resource = '';

	/**
	 * The object id
	 *
	 * @var string
	 */
	protected $_id = '';

	/**
	 * The project client name
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * The project title
	 *
	 * @var string
	 */
	protected $_title = '';

	/**
	 * The project description
	 *
	 * @var string
	 */
	protected $_description = '';

	/**
	 * An url to the application help
	 *
	 * @var string
	 */
	protected $_help = '';

	/**
	 * The application default icon
	 * @default icon-th
	 *
	 * @var string
	 */
	protected $_icon = '';

	/**
	 *
	 * @var array object
	 */
	protected $_hooks = array();
	
	/**
	 *
	 * @var array object
	 */
	protected $_plugins = array();
			
	// methods
	
	/**
	 *
	 * @param string $psVarName        	
	 * @param string $psVarValue        	
	 * @throws Exception
	 */
	public function __set ($psVarName, $psVarValue)
	{
		throw new Exception("Unable to set vars dynamically");
	}

	/**
	 * Construct an object setting the id, name and resource properties
	 * if the id is not given the construct will return an exception
	 *
	 * @param string $psId
	 *        	- Instance identifier.
	 * @param string $psResource        	
	 * @throws Exception
	 */
	protected function __construct ($psId, $psResource = null, $poParent = null)
	{
		if (! empty($psId))
		{
			$this->setId($psId);
			
			if (empty($this->_name))
			{
				$this->setName(ucfirst($psId));
			}
			
			if (empty($this->_resource) && $psResource != null)
			{
				$this->setResource($psResource);
			}
			
			if ($poParent !== null)
			{
				$this->setParent($poParent);
			}
		}
		else
		{
			throw new Exception('The object ID need to be informed!');
		}
		
		return $this;
	}

	/**
	 * Check if a property exist into the object
	 *
	 * @param string $psVar        	
	 * @return boolean
	 */
	public function isVar ($psVar)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Set the object properties.
	 *
	 * The method "set" can be used as:
	 * set(array('property_name'=>'value' ...)) - An array with the exists
	 * properties and its values;
	 *
	 * set('property_name', 'value');
	 *
	 * setPropertyName('value') - An specific method, where the exist
	 * property is the name postfix, using camelCase style.
	 *
	 * If the property doesn't exist, the return will be a exception
	 *
	 * @param string|array $pmVar        	
	 * @param string $psValue        	
	 * @return object
	 */
	public function set ($pmVar, $psValue = null)
	{
		if (is_array($pmVar))
		{
			foreach ($pmVar as $lsVar => $lsValue)
			{
				$lsMethod = "set" . ucfirst($lsVar);
				
				if (method_exists($this, $lsMethod))
				{
					$this->$lsMethod($lsValue);
				}
				else
				{
					throw new Exception('The property "' . $lsVar . '" do not exist into the object!');
				}
			}
		}
		else
		{
			$lsMethod = "set" . ucfirst($pmVar);
			
			if (method_exists($this, $lsMethod))
			{
				$this->$lsMethod($psValue);
			}
			else
			{
				throw new Exception('The property "' . $pmVar . '" do not exist into the object!');
			}
		}
		
		return $this;
	}

	/**
	 * Define if the application is enable to run
	 *
	 * @param boolean $pbEnable        	
	 * @throws Exception
	 * @return object
	 */
	public function setEnable ($pbEnable)
	{
		if (is_bool($pbEnable))
		{
			$this->_enable = $pbEnable;
		}
		else
		{
			throw new Exception('The value of "enable" property need to be a boolean!');
		}
		
		return $this;
	}

	/**
	 * 
	 * @param object $poParent
	 * @return object
	 */
	public function setParent ($poParent)
	{
		$this->_parent = $poParent;

		return $this;
	}
	
	/**
	 *
	 * @param string $psResource        	
	 * @return object
	 */
	public function setResource ($psResource)
	{
		if (! empty($psResource))
		{
			$this->_resource = $psResource;
		}
		else
		{
			throw new Exception('The value of "resource" property should be a non empty string!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psId        	
	 * @return object
	 */
	public function setId ($psId)
	{
		if (! empty($psId))
		{
			$this->_id = $psId;
			
			if (empty($this->_name))
			{
				$this->setName($psId);
			}
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psName        	
	 * @return object
	 */
	public function setName ($psName)
	{
		if (! empty($psName))
		{
			$this->_name = $psName;
			
			if (empty($this->_id))
			{
				$this->setId($psName);
			}
			
			if (empty($this->_title))
			{
				$this->setTitle($psName);
			}
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psTitle        	
	 * @return object
	 */
	public function setTitle ($psTitle)
	{
		if (! empty($psTitle))
		{
			$this->_title = $psTitle;
			
			if (empty($this->_description))
			{
				$this->setDescription($psTitle);
			}
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psDescription        	
	 * @return object
	 */
	public function setDescription ($psDescription)
	{
		if (! empty($psDescription))
		{
			$this->_description = $psDescription;
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psHelp        	
	 * @return object
	 */
	public function setHelp ($psHelp)
	{
		$this->_help = $psHelp;
		
		return $this;
	}

	/**
	 *
	 * @param string $psIcon        	
	 * @return object
	 */
	public function setIcon ($psIcon)
	{
		$this->_icon = $psIcon;
		
		return $this;
	}

	/**
	 *
	 * @param string $psVar        	
	 * @throws Exception
	 * @return string int array object
	 */
	public function getProperties ($psVar)
	{
		if (substr($psVar, 0, 1) !== '_')
		{
			$psVar = "_" . $psVar;
		}
		
		if (property_exists($this, $psVar))
		{
			return $this->$psVar;
		}
		else
		{
			throw new Exception('The property "' . $psVar . '" do not exist into the object!');
		}
	}

	/**
	 *
	 * @param array $paVar        	
	 * @return object
	 */
	public function getResource ($paVar)
	{
		switch (get_class($this))
		{
			case 'Section':
				if (isset($paVar['module']))
				{
					$loResource = $this->getModule($paVar['module']);
					unset($paVar['module']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				break;
			case 'Module':
				if (isset($paVar['controller']))
				{
					$loResource = $this->getController($paVar['controller']);
					unset($paVar['controller']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				break;
			case 'Controller':
				if (isset($paVar['action']))
				{
					$loResource = $this->getAction($paVar['action']);
					unset($paVar['action']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				elseif (isset($paVar['button']))
				{
					$loResource = $this->getButton($paVar['button']);
					unset($paVar['button']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				break;
			case 'Action':
				if (isset($paVar['toolbar']))
				{
					$loResource = $this->getToolbar($paVar['toolbar']);
					unset($paVar['toolbar']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				elseif (isset($paVar['column']))
				{
					$loResource = $this->getColumn($paVar['column']);
					unset($paVar['column']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				elseif (isset($paVar['colAction']))
				{
					$loResource = $this->getColAction($paVar['colAction']);
					unset($paVar['colAction']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				break;
			case 'ColAction':
				if (isset($paVar['colActionBtn']))
				{
					$loResource = $this->getColActionBtn($paVar['colActionBtn']);
					unset($paVar['colActionBtn']);
					
					if (count($paVar) > 0)
					{
						return $loResource->get($paVar);
					}
					else
					{
						return $loResource;
					}
				}
				break;
		}
		
		return $loResource;
	}

	/**
	 * Return the whole object and its children as an array
	 * 
	 * @return array
	 */
	public function toArray ()
	{
		$laProperties = get_object_vars($this);
		
		if (is_array($laProperties))
		{
			foreach ($laProperties as $lsVar => $lmValue)
			{
				$lsKey = substr($lsVar, 1);
				
				if (is_array($lmValue) && count($lmValue) != 0)
				{
					foreach ($lmValue as $lsId => $lmObj)
					{
						if (is_object($lmObj) && method_exists($lmObj, 'get'))
						{
							$laReturn[$lsKey][$lsId] = $lmObj->get();
						}
						else
						{
							$laReturn[$lsKey][$lsId] = $lmObj;
						}
					}
				}
				else
				{
					$laReturn[$lsKey] = $lmValue;
				}
			}
		}
		
		return $laReturn;
	}

	/**
	 * Return the value of the object and its children
	 *
	 * The method "get" could be used as:
	 * get() - without pass any specific var, it will return the whole object;
	 *
	 * get('property_name') - return just the object property required;
	 *
	 * get(array_path_to_the_property) - An array whith the path to the deeped
	 * property.
	 * Ex.: array('module'=>'value1','controller'=>value2,'button'=>'value3');
	 *
	 * In this cace it will return the button element identified by id = value3
	 *
	 * @param string|array|NULL $pmVar        	
	 * @return string array object
	 */
	public function get ($pmVar = null)
	{
		if (is_string($pmVar) && $pmVar !== null)
		{
			return $this->getProperties ($pmVar);
		}
		elseif (is_array($pmVar))
		{
			// Debug::display(get_class($this));
			// Debug::display($pmVar);
			
			return $this->getResource($pmVar);
		}
		elseif ($pmVar === null)
		{
			return $this->toArray();
		}
	}

	/**
	 * Return a clone of the object.
	 * The change maked in this object clone will not affect the original one.
	 * 
	 * @return object
	 */
	public function copy ()
	{
		$loClone = clone $this;
		$loClone->_clone = true;
		
		if (is_object($loClone))
		{
			foreach ($loClone as $lsVar => $lmValue)
			{
				if (is_array($lmValue) && count($lmValue) != 0)
				{
					foreach ($lmValue as $lsId => $lmObj)
					{
						if (is_object($lmObj) && method_exists($lmObj, 'copy'))
						{
							$loClone->{$lsVar}[$lsId] = $lmObj->copy();
						}
					}
				}
			}
		}
		
		return $loClone;
	}

	/**
	 *
	 * @param string $psItem        	
	 * @param array $paOrder        	
	 * @param object
	 */
	public function order ($psItem, array $paOrder)
	{
		$laProperty = $this->get($psItem);
		
		if (is_array($laProperty) && is_array($paOrder))
		{
			foreach ($paOrder as $lsItem)
			{
				foreach ($laProperty as $lsKey => $lmItem)
				{
					if (is_object($lmItem))
					{
						if ($lmItem->get('id') == $lsItem)
						{
							$laNew[$lsKey] = $lmItem;
							continue;
						}
					}
					else
					{
						if ($lmItem == $lsItem)
						{
							$laNew[$lsKey] = $lmItem;
							continue;
						}
					}
				}
			}
			
			$psItem = "_" . $psItem;
			$this->$psItem = $laNew;
		}
		
		return $this;
	}

	/**
	 * Insere um item de array na posição desejada, deslocando os demais itens.
	 *
	 * @param array $paList
	 *        	- Array original onde será inserido o novo elemento.
	 * @param object $poValue
	 *        	- Elemento a ser inserido no array original.
	 * @param string $psIndex
	 *        	- Identificador do indice do elemento, podendo ser null quando
	 *        	for igual ao id do elemento.
	 * @param int $pnPosition
	 *        	- Posição em que o elemento $poValue será inserido na nova
	 *        	lista.
	 * @param boolean $pbNumericIndex
	 *        	- Informa quando o indice do array for numérico.
	 * @return array - Nova lista com o elemento inserido na posição desejada e
	 *         com os demais elementos deslocados.
	 */
	public function position (array $paList, $poValue, $psIndex = null, $pnPosition = null, $pbNumericIndex = false)
	{
		// inicializa o contador de posição do array
		$lnPos = 0;
		
		// percorrendo lista original
		foreach ($paList as $lsKey => $loValue)
		{
			// se a posição atual é a posição desejada
			if ($lnPos == $pnPosition)
			{
				// verifica se o indice não é numérico
				if ($pbNumericIndex === false)
				{
					// verifica se não foi passado um indice específico
					if ($psIndex === null)
					{
						// carrega o id do elemento para servir de indice
						$lsIndex = $poValue->get('id');
					}
					else
					{
						// utiliza o indice passado no parametro
						$lsIndex = $psIndex;
					}
				}
				else
				{
					// utiliza a posição numérica ordenada
					$lsIndex = $lnPos;
				}
				
				// insere o elemento na posição desejada
				$laNewList[$lsIndex] = $poValue;
				// incrementa a posição a ser verificada
				$lnPos ++;
			}
			
			// independente se o elemento achou sua posição ou não todos os
			// elementos devem ser transportados para o novo array. Sendo assim,
			// o array original deve ser percorrido até o fim.
			
			// verifica se o indice não é numérico
			if ($pbNumericIndex === false)
			{
				// utiliza o index já atual do elemanto
				$lsIndex = $lsKey;
			}
			else
			{
				// utiliza a posição numérica ordenada
				$lsIndex = $lnPos;
			}
			
			// mantem o elemento existente na sua posição ou desloca para baixo
			$laNewList[$lsKey] = $loValue;
			// incrementa a posição a ser verificada
			$lnPos ++;
		}
		
		// retornando a nova lista
		return $laNewList;
	}

	/**
	 *
	 * @param array $paRoles        	
	 * @return object
	 */
	public function visible ($paRoles)
	{
		if (is_object($this))
		{
			foreach ($this as $lsVar => $lmValue)
			{
				if (is_array($lmValue) && count($lmValue) != 0)
				{
					foreach ($lmValue as $lsId => $lmObj)
					{
						// verificando se o elemento é um array e se tem o
						// metodo visible
						if (is_object($lmObj) && method_exists($lmObj, 'visible'))
						{
							$lmObj->visible($paRoles);
						}
					}
				}
			}
			
			$this->checkEnable($paRoles);
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paRoles        	
	 * @return object
	 */
	public function checkEnable ($paRoles)
	{
		switch (get_class($this))
		{
			case 'Button':
				foreach ($paRoles as $lsRole => $lbDisable)
				{
					if ($lsRole == $this->_resource)
					{
						$this->_enable = false;
						continue;
					}
				}
				
				break;
			case 'Column':
				break;
			case 'ColActionBtn':
				foreach ($paRoles as $lsRole => $lbDisable)
				{
					if ($lsRole == $this->_resource)
					{
						$this->_enable = false;
						continue;
					}
				}
				
				break;
			case 'ColAction':
				$lnColActionBtnDisable = 0;
				
				foreach ($this->_colActionBtn as $loColActionBtn)
				{
					if (! $loColActionBtn->_enable)
					{
						$lnColActionBtnDisable ++;
					}
				}
				
				if (count($this->_colActionBtn) == $lnColActionBtnDisable)
				{
					$this->_enable = false;
				}
				
				break;
			case 'Action':
				foreach ($paRoles as $lsRole => $lbDisable)
				{
					if ($lsRole == $this->_resource)
					{
						$this->_enable = false;
						continue;
					}
				}
				
				break;
			case 'Controller':
				$lnActionDisable = 0;
				
				foreach ($this->_action as $loAction)
				{
					if (! $loAction->_enable)
					{
						$lnActionDisable ++;
					}
				}
				
				if (count($this->_action) == $lnActionDisable)
				{
					$this->_enable = false;
				}
				
				break;
			case 'Module':
				$lnControllerDisable = 0;
				
				foreach ($this->_controller as $loController)
				{
					if (! $loController->_enable)
					{
						$lnControllerDisable ++;
					}
				}
				
				if (count($this->_controller) == $lnControllerDisable)
				{
					$this->_enable = false;
				}
				
				break;
			case 'Section':
				$lnModuleDisable = 0;
				
				foreach ($this->_module as $loModule)
				{
					if (! $loModule->_enable)
					{
						$lnModuleDisable ++;
					}
				}
				
				if (count($this->_module) == $lnModuleDisable)
				{
					$this->_enable = false;
				}
				break;
		}
		
		return $this;
	}

	/**
	 * Create a new object into the array object
	 * and setting its id and name
	 *
	 * @param string $psArrayObj        	
	 * @param string $psElementId        	
	 * @return object
	 */
	public function create ($psArrayObj, $psElementId)
	{
		if (! isset($this->$psArrayObj[$psElementId]))
		{
			return $this->$psArrayObj[$psElementId] = new Module($psElementId, $this->_resource);
		}
		else
		{
			return $this->$psArrayObj[$psElementId];
		}
	}

	/**
	 * Add an existent object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param string $psArrayObj        	
	 * @param object $poElement        	
	 * @param string $psIndex        	
	 * @param string $pnPosition        	
	 * @param boolean $pbNumericIndex        	
	 * @return object
	 */
	public function add ($psArrayObj, $poElement, $psIndex = null, $pnPosition = null, $pbNumericIndex = false)
	{
		if (is_int($pnPosition))
		{
			$this->$psArrayObj = $this->position($this->$psArrayObj, $poElement, $psIndex, $pnPosition, $pbNumericIndex);
		}
		else
		{
			if ($psIndex === null)
			{
				$lsIndex = $poElement->get('id');
			}
			else
			{
				$lsIndex = $psIndex;
			}
			
			$this->$psArrayObj[$lsIndex] = $poElement;
		}
		
		return $this;
	}

	/**
	 * Remove the element from the array object
	 *
	 * @param string $psArrayObj        	
	 * @param string $pmElementId        	
	 * @return Object
	 */
	public function remove ($psArrayObj, $pmElementId)
	{
		if (isset($this->$psArrayObj[$pmElementId]))
		{
			unset($this->$psArrayObj[$pmElementId]);
		}
		else
		{
			if (is_array($this->$psArrayObj))
			{
				foreach ($this->$psArrayObj as $lnKey => $loButton)
				{
					if ($loButton->get('id') == $pmElementId)
					{
						unset($this->$psArrayObj[$lnKey]);
					}
				}
			}
		}
		
		return $this;
	}

	/**
	 * Load the element from array object
	 * or the entire array if $psElementId = null
	 *
	 * @param string $psArrayObj        	
	 * @param string $psElementId        	
	 * @param boolean $pbValid        	
	 * @throws Exception
	 * @return object array null
	 */
	public function getElement ($psArrayObj, $psElementId = null, $pbValid = true)
	{
		if ($psElementId !== null && in_array($psElementId, $this->$psArrayObj))
		{
			return $this->$psArrayObj[$psElementId];
		}
		elseif ($psElementId !== null && is_string($psElementId))
		{
			if (is_array($this->$psArrayObj))
			{
				foreach ($this->$psArrayObj as $lnKey => $loObj)
				{
					if (is_object($loObj) && $loObj->_id == $psElementId)
					{
						return $loObj;
					}
				}
			}
			
			if ($pbValid)
			{
				throw new Exception('The element "' . $psElementId . '" do not exist!');
			}
		}
		elseif ($pbValid && $psElementId !== null && is_string($psElementId))
		{
			throw new Exception('The element "' . $psElementId . '" do not exist!');
		}
		else
		{
			return $this->$psArrayObj;
		}
		
		return null;
	}

	/**
	 * Change the Section value from the internal object elements
	 *
	 * @param string $psArrayObj        	
	 * @param string $psSectionValue        	
	 * @param string $psParentId        	
	 * @return object
	 */
	public function _replaceSection ($psArrayObj, $psSectionValue = "", $psParentId = "")
	{
		if (is_array($this->$psArrayObj))
		{
			foreach ($this->$psArrayObj as $lsElementId => $loObj)
			{
				if (is_object($loObj))
				{
					$loObj->replaceSection($psSectionValue, $this->_id);
				}
			}
		}
		
		return $this;
	}

	/**
	 * Return an array lagacy to the old version of the object
	 *
	 * @param string $psParam	
	 * @return array|null
	 */
	public function legacy ($psParam = "")
	{
		if ($this->_enable)
		{
			$laProperties = get_object_vars($this);
			
			if (is_array($laProperties))
			{
				foreach ($laProperties as $lsVar => $lmValue)
				{
					$lsKey = substr($lsVar, 1);
					
					if (is_array($lmValue) && count($lmValue) != 0)
					{
						foreach ($lmValue as $lsId => $lmObj)
						{
							if (is_object($lmObj) && method_exists($lmObj, 'get'))
							{
								$laReturn[$lsKey][$lsId] = $lmObj->legacy($psParam);
							}
							else
							{
								$laReturn[$lsKey][$lsId] = $lmObj;
							}
						}
					}
					else
					{
						$laReturn[$lsKey] = $lmValue;
					}
				}
			}
			
			return $laReturn;
		}
		
		return null;
	}
}
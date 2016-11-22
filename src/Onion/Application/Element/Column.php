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

class Column extends AbstractApp
{

	/**
	 *
	 * @var string
	 */
	protected $_ordering = "";
	
	/**
	 *
	 * @var boolean
	 */
	protected $_sortable = false;

	/**
	 *
	 * @var boolean
	 */
	protected $_searchable = false;
	
	/**
	 *
	 * @var boolean
	 */
	protected $_visible = true;

	/**
	 *
	 * @var boolean
	 */
	protected $_resizable = true;
	
	/**
	 *
	 * @var boolean
	 */
	protected $_editable = false;
		
	/**
	 *
	 * @var string
	 */
	protected $_class = "";
	
	/**
	 *
	 * @var string
	 */
	protected $_width = "";
	
	/**
	 *
	 * @var string
	 */
	protected $_align = "left";
	
	/**
	 *
	 * @var string
	 */
	protected $_color = "";

	/**
	 *
	 * @var string
	 */
	protected $_background = "";
	
	/**
	 *
	 * @var string
	 */
	protected $_format = "";
	
	/**
	 * 
	 * @var object Onion\Application\Element\Button
	 */
	protected $_button = null;
	
	
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
	 *
	 * @param string $psOrdering
	 * @return \Onion\Application\Element\Column
	 */
	public function setOrdering ($psOrdering)
	{
		if (!empty($psOrdering))
		{
			$this->_ordering = $psOrdering;
		}
	
		return $this;
	}
		
	
	/**
	 *
	 * @param boolean $pbSortable        	
	 * @throws Exception
	 * @return \Onion\Application\Element\Column
	 */
	public function setSortable ($pbSortable = false)
	{
		if (is_bool($pbSortable))
		{
			$this->_sortable = $pbSortable;
		}
		else
		{
			throw new Exception('The sortable value should be a boolean!');
		}
		
		return $this;
	}

	
	/**
	 *
	 * @param boolean $pbSearchable
	 * @throws Exception
	 * @return \Onion\Application\Element\Column
	 */
	public function setSearchable ($pbSearchable = false)
	{
		if (is_bool($pbSearchable))
		{
			$this->_searchable = $pbSearchable;
		}
		else
		{
			throw new Exception('The searchable value should be a boolean!');
		}
	
		return $this;
	}
		
	
	/**
	 *
	 * @param boolean $pbVisible        	
	 * @throws Exception
	 * @return \Onion\Application\Element\Column
	 */
	public function setVisible ($pbVisible = true)
	{
		if (is_bool($pbVisible))
		{
			$this->_visible = $pbVisible;
		}
		else
		{
			throw new Exception('The visible value should be a boolean!');
		}
		
		return $this;
	}

	
	/**
	 *
	 * @param boolean $pbResizable
	 * @throws Exception
	 * @return \Onion\Application\Element\Column
	 */
	public function setResizable ($pbResizable = false)
	{
		if (is_bool($pbResizable))
		{
			$this->_resizable = $pbResizable;
		}
		else
		{
			throw new Exception('The resizable value should be a boolean!');
		}
	
		return $this;
	}
	
	
	/**
	 *
	 * @param boolean $pbEditable
	 * @throws Exception
	 * @return \Onion\Application\Element\Column
	 */
	public function setEditable ($pbEditable = false)
	{
		if (is_bool($pbEditable))
		{
			$this->_editable = $pbEditable;
		}
		else
		{
			throw new Exception('The editable value should be a boolean!');
		}
	
		return $this;
	}	
	
	
	/**
	 *
	 * @param string $psClass        	
	 * @return \Onion\Application\Element\Column
	 */
	public function setClass ($psClass)
	{
		if (!empty($psClass))
		{
			$this->_class = $psClass;
		}
		
		return $this;
	}

	
	/**
	 *
	 * @param string $psWidth        	
	 * @return \Onion\Application\Element\Column
	 */
	public function setWidth ($psWidth)
	{
		$this->_width = $psWidth;
		
		return $this;
	}

	
	/**
	 *
	 * @param string $psAlign
	 * @return \Onion\Application\Element\Column
	 */
	public function setAlign ($psAlign = 'left')
	{
		$this->_align = $psAlign;
	
		return $this;
	}
	
	
	/**
	 *
	 * @param string $psColor
	 * @return \Onion\Application\Element\Column
	 */
	public function setColor ($psColor)
	{
		$this->_color = $psColor;
	
		return $this;
	}

	
	/**
	 *
	 * @param string $psBackground
	 * @return \Onion\Application\Element\Column
	 */
	public function setBackground ($psBackground)
	{
		$this->_background = $psBackground;
	
		return $this;
	}	
	
	
	/**
	 *
	 * @param string $psFormat
	 * @return \Onion\Application\Element\Column
	 */
	public function setFormat ($psFormat)
	{
		$this->_format = $psFormat;
	
		return $this;
	}
		
	
	// Action methods
	
	
	/**
	 * Create a new Button object
	 * and setting its id and name
	 *
	 * @param string $psButtonId        	
	 * @return Onion\Application\Element\Button
	 */
	public function createButton ($psButtonId)
	{
		return $this->_button = new Button($psButtonId, $this->get('resource'), $this);
	}

	
	/**
	 * Remove a Button
	 *
	 * @return null
	 */
	public function removeButton ()
	{
		return $this->_button = null;
	}

	
	/**
	 * Load the Button object from array object
	 * or the entire array if $pmButtonId = null
	 *
	 * @return Onion\Application\Element\Button|null
	 */
	public function getButton ()
	{
		return $this->_button;
	}	
	
	
	public function renderHeader ($psOrderCol, $psOrder)
	{
		$lsCol = '';
		
		if ($this->get('enable') && $this->get('visible'))
		{
			$lsResizable = ($this->get('resizable') ? 'onion-gride-resizable' : '');
			
			$lsCol = '<th class="onion-grid-th ' . $lsResizable . '">';
			
			$lsIcon = (!empty($this->get('icon')) ? '<i class="glyphicon glyphicon-' . $this->get('icon') . '"></i> ' : '');
			
			if ($this->get('sortable'))
			{
				$lsOrdering = (!empty($this->get('ordering')) ? $this->get('ordering') : $this->get('id'));
				
				if ($psOrderCol == $lsOrdering)
				{
					if ($psOrder == 'ASC')
					{
						$lsCol .= '<a class="colOrder" href="#" data-act="?" data-col="' . $lsOrdering . '" data-ord="DESC" title="' . $this->get('description') . '">' . $lsIcon . $this->get('title') . '<b class="caret"></b></a>';
					}
					else
					{
						$lsCol .= '<a class="colOrder dropup" href="#" data-act="?" data-col="' . $lsOrdering . '" data-ord="ASC" title="' . $this->get('description') . '">' . $lsIcon . $this->get('title') . '<b class="caret"></b></a>';
					}
				}
				else
				{
					$lsCol .= '<a class="colOrder" href="#" data-act="?" data-col="' . $lsOrdering . '" data-ord="ASC" title="' . $this->get('description') . '">' . $lsIcon . $this->get('title') . '</a>';
				}
			}
			else
			{
				$lsCol .= '<a class="onion-grid-nosortable text-default" href="#" title="' . $this->get('description') . '">' . $lsIcon . $this->get('title') . '</a>';
			}
			
			$lsCol .= '</th>';
		}
				
		return $lsCol;
	}
	
	
	/**
	 * 
	 * @param string $psValue
	 * @param string $psGridId
	 * @return string
	 */
	public function renderColumn ($paRecord, $psGridId = null)
	{		
		$lsCol = '';
		
		if ($this->get('enable') && $this->get('visible'))
		{
			$lsAlign = (!empty($this->get('align')) ? "text-align:{$this->get('align')};" : '');
			$lsWidth = (!empty($this->get('width')) ? "width:{$this->get('width')};" : '');
			$lsColor = (!empty($this->get('color')) ? "color:{$this->get('color')};" : '');
			$lsBackground = (!empty($this->get('background')) ? "background:{$this->get('background')};" : '');
			$lsStyle = "{$lsAlign} {$lsWidth} {$lsColor} {$lsBackground}";
			$lsStyle = (!empty(trim($lsStyle)) ? 'style="' . $lsStyle . '"' : '');
			
			$lsButton = '';
				
			if ($this->getButton('button') !== null)
			{
				$lsButton = $this->getButton('button')->render();
			}
				
			if (!empty($this->get('format')) || !empty($lsButton))
			{
				if (is_array($paRecord))
				{
					$lsPattern = array();
					
					foreach ($paRecord as $lsKey => $lsData)
					{
						$lsPattern[] = "/#%{$lsKey}%#/";
					}
					
					$lsValue = preg_replace($lsPattern, $paRecord, $this->get('format'));
					$lsButton = preg_replace($lsPattern, $paRecord, $lsButton);
				}
			}
			
			if(empty($lsValue)) 
			{
				$lsValue = isset($paRecord[$this->get('id')]) ? $paRecord[$this->get('id')] : '';
			}
						
			$lsCol = '
				<td id="onion-grid-td-' . $psGridId . '-' . $this->get('id') . '" class="onion-grid-td ' . $this->get('class') . '" ' . $lsStyle . '>
					' . $lsButton . ' ' . $lsValue . '
				</td>';
		}
				
		return $lsCol;
	}
}
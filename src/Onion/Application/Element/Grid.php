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
use Onion\Application\Exception;
use Onion\Application\AbstractApp;
use Onion\Application\InterfaceApp;
use Onion\Application\Element\Column;
use Onion\Application\Element\Options;
use Onion\Application\Element\Filter;
use Onion\Application\Element\Button;
use Onion\Log\Debug;
use Onion\I18n\Translator;
use Onion\Paginator\Pagination;

class Grid extends AbstractApp
{

	/**
	 *
	 * @var string
	 */
	protected $_backTo = '';
	
	/**
	 * 
	 * @var boolean
	 */
	protected $_showSearch = false;

	/**
	 * 
	 * @var string
	 */
	protected $_searchFieldDefault = '';

	/**
	 *
	 * @var string
	 */
	protected $_searchQuery = '';
	
	/**
	 * Can be: checkbox, radio, null
	 * @var string
	 */
	protected $_checkType = 'checkbox';

	/**
	 * 
	 * @var string
	 */
	protected $_orderCol = '';
	
	/**
	 * Can be: ASC, DESC, RAND
	 * @var string
	 */
	protected $_order = 'ASC';

	/**
	 *
	 * @var boolean
	 */
	protected $_showColOptions = true;
	
	/**
	 *
	 * @var boolean
	 */
	protected $_showPaginationNumRows = true;
	
	/**
	 *
	 * @var boolean
	 */
	protected $_showPaginationInfo = true;
	
	/**
	 *
	 * @var boolean
	 */
	protected $_showPaginationNav = true;

	/**
	 *
	 * @var int
	 */
	protected $_paginationNumRows = array('6', '12', '25', '50', '100');
	
	/**
	 * Rows to show in the grid
	 * 
	 * @var int
	 */
	protected $_numRows = 25;
	
	/**
	 * Grid columns array
	 * 
	 * @var array object Onion\Application\Element\Column
	 */
	protected $_colums = array();
	
	/**
	 * Row action
	 * 
	 * @var array object Onion\Application\Element\Options
	 */
	protected $_colOptions = array();

	/**
	 * Results to be showed into the grid
	 * 
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Total of results for the query
	 *
	 * @var int
	 */
	protected $_totalResults = 0;
	
	/**
	 * Total of results for the query
	 *
	 * @var int
	 */
	protected $_currentPage = 0;
		
	/**
	 * Pagination object
	 * 
	 * @var object
	 */
	protected $_pagination = null;
	
	/**
	 * System messages
	 *  
	 * @var array
	 */
	protected $_messages = array();
	
	
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
	 *
	 * @param string $psBackTo
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setBackTo ($psBackTo)
	{
		if (!empty($psBackTo))
		{
			$this->_backTo = $psBackTo;
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param boolean $pbShowSearch
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setShowSearch ($pbShowSearch)
	{
		if (is_bool($pbShowSearch))
		{
			$this->_showSearch = $pbShowSearch;
		}
		else
		{
			throw new Exception('The value of "showSearch" property need to be a boolean!');
		}
	
		return $this;
	}	

	/**
	 *
	 * @param string $psSearchFieldDefault
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setSearchFieldDefault ($psSearchFieldDefault)
	{
		if (!empty($psSearchFieldDefault))
		{
			$this->_searchFieldDefault = $psSearchFieldDefault;
		}
	
		return $this;
	}

	/**
	 *
	 * @param string $psSearchQuery
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setSearchQuery ($psSearchQuery)
	{
		if (!empty($psSearchQuery))
		{
			$this->_searchQuery = $psSearchQuery;
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param string $psCheckType
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setCheckType ($psCheckType)
	{
		$this->_checkType = null;
		
		if ($psCheckType == 'checkbox' || $psCheckType == 'radio')
		{
			$this->_checkType = $psCheckType;
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param string $psOrderCol
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setOrderCol ($psOrderCol)
	{
		if (!empty($psOrderCol))
		{
			$this->_orderCol = $psOrderCol;
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param string $psOrder
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setOrder ($psOrder)
	{
		$this->_order = 'ASC';
		
		if ($psOrder == 'ASC' || $psOrder == 'DESC' || $psOrder == 'RAND')
		{
			$this->_order = $psOrder;
		}
	
		return $this;
	}

	/**
	 *
	 * @param boolean $pbShowColOptions
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setShowColOptions ($pbShowColOptions)
	{
		if (is_bool($pbShowColOptions))
		{
			$this->_showColOptions = $pbShowColOptions;
		}
		else
		{
			throw new Exception('The value of "showColOptions" property need to be a boolean!');
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param boolean $pbShowPaginationNumRows        	
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setShowPaginationNumRows ($pbShowPaginationNumRows)
	{
		if (is_bool($pbShowPaginationNumRows))
		{
			$this->_showPaginationNumRows = $pbShowPaginationNumRows;
		}
		else
		{
			throw new Exception('The value of "showPaginationNumRows" property need to be a boolean!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param boolean $pbShowPaginationInfo        	
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setShowPaginationInfo ($pbShowPaginationInfo)
	{
		if (is_bool($pbShowPaginationInfo))
		{
			$this->_showPaginationInfo = $pbShowPaginationInfo;
		}
		else
		{
			throw new Exception('The value of "showPaginationInfo" property need to be a boolean!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param boolean $pbShowPaginationNav
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setShowPaginationNav ($pbShowPaginationNav)
	{
		if (is_bool($pbShowPaginationNav))
		{
			$this->_showPaginationNav = $pbShowPaginationNav;
		}
		else
		{
			throw new Exception('The value of "showPaginationNav" property need to be a boolean!');
		}
	
		return $this;
	}
	
	/**
	 *
	 * @param array $paPaginationNumRows
	 * @return \Onion\Application\Element\Grid
	 */
	public function setPaginationNumRows ($paPaginationNumRows)
	{
		if (is_array($paPaginationNumRows))
		{
			$this->_paginationNumRows = $paPaginationNumRows;
		}
		else
		{
			throw new Exception('The value of "_paginationNumRows" property need to be an array!');
		}
		
		return $this;
	}	

	/**
	 *
	 * @param int $pnNumRows
	 * @return \Onion\Application\Element\Grid
	 */
	public function setNumRows ($pnNumRows)
	{
		if (is_int((int)$pnNumRows))
		{
			$this->_numRows = $pnNumRows;
		}
		else
		{
			throw new Exception('The value of "numRows" property need to be an int!');
		}
		
		return $this;
	}
	
	/**
	 *
	 * @param array $paData
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setData ($paData)
	{
		$this->_data = $paData;
	
		return $this;
	}	

	/**
	 *
	 * @param int $pnTotalResults
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setTotalResults ($pnTotalResults)
	{
		if (is_int((int)$pnTotalResults))
		{
			$this->_totalResults = $pnTotalResults;
		}
		else
		{
			throw new Exception('The value of "totalResults" property need to be an int!');
		}
	
		return $this;
	}

	/**
	 *
	 * @param int $pnCurrentPage
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setCurrentPage ($pnCurrentPage)
	{
		if (is_int((int)$pnCurrentPage))
		{
			$this->_currentPage = $pnCurrentPage;
		}
		else
		{
			throw new Exception('The value of "currentPage" property need to be an int!');
		}
	
		return $this;
	}
		
	/**
	 *
	 * @param object $poPagination Onion\Paginator\Pagination
	 * @throws Exception
	 * @return Onion\Paginator\Pagination
	 */
	public function setPagination ($poPagination = null)
	{
		if ($poPagination instanceof Pagination)
		{
			$this->_pagination = $poPagination;
		}
		elseif ($poPagination !== null)
		{
			throw new Exception('The value of "pagination" property need to be an instance of Onion\Paginator\Pagination!');		
		}
		else
		{
			$loPagination = new Pagination();
			$loPagination->set('nResPerPage', $this->get('numRows'));
			$loPagination->setPaginator(
					$this->get('totalResults'),
					$this->get('currentPage')
			);
			
			$this->_pagination = $loPagination;
		}
	
		return $this->_pagination;
	}
	
	/**
	 *
	 * @param array $paMessages
	 * @throws Exception
	 * @return Onion\Application\Element\Grid
	 */
	public function setMessages ($paMessages)
	{
		if (is_array($paMessages))
		{
			$this->_messages = $paMessages;
		}
		else
		{
			throw new Exception('The value of "messages" property need to be an array!');
		}
	
		return $this;
	}
	
	// Action methods
	
	/**
	 * Create a new Column object into the array object
	 * and setting its id and name
	 *
	 * @param string $psColumnId        	
	 * @return Onion\Application\Element\Column
	 */
	public function createColumn ($psColumnId)
	{
		return $this->_colums[] = new Column($psColumnId, $this->get('resource'), $this);
	}

	/**
	 * Add an existent Column object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Column $poColumn        	
	 * @param string $psIndex        	
	 * @param int $pnPosition        	
	 * @return Onion\Application\Grid
	 */
	public function addColumn ($poColumn, $psIndex = null, $pnPosition = null)
	{
		if ($poColumn instanceof Column)
		{
			return parent::add('_colums', $poColumn, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poColumn should be a instance of Onion\Application\Element\Column!');
		}
	}

	/**
	 * Remove a Column from the array object
	 *
	 * @param int|string $pmColumnId        	
	 * @return Onion\Application\Grid
	 */
	public function removeColumn ($pmColumnId)
	{
		return parent::remove('_colums', $pmColumnId);
	}

	/**
	 * Load the Column object from array object
	 * or the entire array if $pmColumnId = null
	 *
	 * @param int|string $pmColumnId        	
	 * @param boolean $pbValid        	
	 * @throws Exception
	 * @return Onion\Application\Element\Column array null
	 */
	public function getColumn ($pmColumnId = null, $pbValid = true)
	{
		return parent::getElement('_colums', $pmColumnId, $pbValid);
	}
	
	/**
	 * Create a new Options object into the array object
	 * and setting its id and name
	 *
	 * @param string $psColOptionsId
	 * @return Onion\Application\Element\Options
	 */
	public function createColOptions ($psColOptionsId)
	{
		return $this->_colOptions[] = new Options($psColOptionsId, $this->get('resource'), $this);
	}
	
	/**
	 * Add an existent Options object to the array object.
	 * If $pnPosition is int value, the object will be inserted in this array
	 * positon.
	 * Else, if $psIndex is given, it will be used to set the array key.
	 * Or by default the array key will be the object id property.
	 *
	 * @param Onion\Application\Element\Options $poColOptions
	 * @param string $psIndex
	 * @param int $pnPosition
	 * @return Onion\Application\Grid
	 */
	public function addColOptions ($poColOptions, $psIndex = null, $pnPosition = null)
	{
		if ($poColumn instanceof ColOptions)
		{
			return parent::add('_colOptions', $poColOptions, $psIndex, $pnPosition, true);
		}
		else
		{
			throw new Exception('$poColOptions should be a instance of Onion\Application\Element\Options!');
		}
	}
	
	/**
	 * Remove a Options from the array object
	 *
	 * @param int|string $pmColOptionsId
	 * @return Onion\Application\Grid
	 */
	public function removeColOptions ($pmColOptionsId)
	{
		return parent::remove('_colOptions', $pmColOptionsId);
	}
	
	/**
	 * Load the Options object from array object
	 * or the entire array if $pmColumnId = null
	 *
	 * @param int|string $pmColOptionsId
	 * @param boolean $pbValid
	 * @throws Exception
	 * @return Onion\Application\Element\Options|array|null
	 */
	public function getColOptions ($pmColOptionsId = null, $pbValid = true)
	{
		return parent::getElement('_colOptions', $pmColOptionsId, $pbValid);
	}

	public function isSearchField($psField)
	{
		if (!empty($psField))
		{
			$loColumn = $this->getColumn($psField, false);
			
			if ($loColumn != null)
			{
				return $loColumn->get('searchable');
			}
		}
		
		return false;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function render ($psRoute, $psFolder = null)
	{
		$this->setPagination();
		
		$lsGridHeader = $this->renderGridHeader($psFolder);

		$lsMessages = $this->renderMessages();
		
		$lsForm = $this->renderForm();
		
		$lsFilters = $this->renderFilters($psRoute);
		
		$lsTableHeader = $this->renderTableHeader();
		
		$lsRows = $this->renderRows();
		
		$lsNavBar = $this->renderNavPagination();
			
		$lsAlert = $this->renderAlert();
		
		$lsModal = $this->renderModal();
		
		$lsGrid = '
		<link href="/vendor/m3uzz/onionjs-0.16.4/dist/css/grid.css" media="all" rel="stylesheet" type="text/css">
		<div id="onion-grid-' . $this->get('id') . '" class="onion-grid">
			' . $lsGridHeader . '
			' . $lsMessages . '	
			' . $lsForm . '		
			' . $lsFilters . '
			<div class="table-responsive">
				<table id="onion-grid-table-' . $this->get('id') . '" class="onion-grid-table table table-hover table-condensed">
					' . $lsTableHeader . '
					' . $lsRows . '
				</table>
			</div>
			' . $lsNavBar . '
			' . $lsAlert . '
			' . $lsModal . '
		</div>
		<script type="text/javascript" src="/vendor/m3uzz/onionjs-0.16.4/dist/js/grid.js"></script>';
	
		return $lsGrid;
	}	

	public function renderGridHeader ($psFolder = null)
	{
		$lsIcon = '';
		$lsDescrtiption = '';
		$lsFolder = '';
		
		if ($psFolder !== null)
		{
			$lsFolder = '
				<span class="onion-grid-folder">
					<i class="glyphicon glyphicon-folder-open"></i> ' . $psFolder . '
				</span>';
		}
		
		if (!empty($this->get('icon')))
		{
			$lsIcon = '<i class="glyphicon glyphicon-' . $this->get('icon') . '"></i> ';
		}

		if (!empty($this->get('description')))
		{
			$lsDescrtiption = '<p class="onion-grid-desc">' . $this->get('description') . '</p> ';
		}
		
		$lsGridHeader = '
			<h1 class="onion-grid-h1">
				' . $lsIcon . '
				' . $this->get('title') . '
				' . $lsFolder . '
			</h1>
			' . $lsDescrtiption . '
		';
		
		return $lsGridHeader;	
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderForm ()
	{
		$loPagination = $this->get('pagination');
		
		$laData = $loPagination->get('aData');
		
		$lnCurrent = ($laData['total'] == 0 ? '0' : $laData['current']);
		
		$lsForm = '
		 	<form id="actForm" action="#" method="POST" data-request="HTTP">
				<input type="hidden" name="back" value="' . $this->get('backTo') . '" />
				<input type="hidden" name="id" />
		 		<input type="hidden" name="ckd" />
				<input type="hidden" name="col" value="' . $this->get('orderCol') . '" />
				<input type="hidden" name="ord" value="' . $this->get('order') . '" />
				<input type="hidden" name="rows" value="' . $this->get('numRows') . '" />
				<input type="hidden" name="p" value="' . $lnCurrent . '" />
				<input type="hidden" name="q" value="' . $this->get('searchQuery') . '" />
				<input type="hidden" name="f" value="' . $this->get('searchFieldDefault') . '" />
			</form>';
		
		return $lsForm;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderMessages ()
	{
		$laMessages = $this->get('messages');
		$lsMessage = '';
		
		if (is_array($laMessages))
		{
			foreach ($laMessages as $lnKey => $laMessage)
			{
				$lsMessage .= '<div class="alert alert-' . $laMessage['type'] . ' alert-dismissable">';
				$lsMessage .= '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
				$lsMessage .= $laMessage['msg'];
				$lsMessage .= '</div>';
			}
		}
		
		return $lsMessage;
	}
	
	public function renderFilters ($psRoute = '', $psTitle = '')
	{
		//TODO: automatizar toolbar

		$lsGrid = '
		<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
		<ul class="nav navbar-nav">
		<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Ações em massa') . '<b class="caret"></b></a>
		<ul class="dropdown-menu" role="menu">';
			
		if ($this->get('backTo') == 'trash')
		{
		$lsGrid .= '
		<li>
		<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja mover os registros selecionados para a lixeira?') . '" data-act="/' . $psRoute . '/move-list">
		<i class="glyphicon glyphicon-transfer"></i> ' . Translator::i18n('Restaurar registros') . '
		</a>
		</li>
		<li class="divider"></li>
		<li>
		<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja apagar permanentemente os registros selecionados?') . '" data-act="/' . $psRoute . '/delete-list">
		<i class="glyphicon glyphicon-remove"></i> ' . Translator::i18n('Apagar permanentemente') . '
		</a>
		</li>';
		}
		else
		{
		$lsGrid .= '
		<li>
		<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja mover os registros selecionados para a lixeira?') . '" data-act="/' . $psRoute . '/move-list">
		<i class="glyphicon glyphicon-trash"></i> ' . Translator::i18n('Mover para lixeira') . '
		</a>
		</li>';
		}
		
		$lsGrid .= '
		</ul>
		</li>';
		
		if ($this->get('backTo') == 'trash')
		{
		$lsGrid .= '
		<li>
		<a href="/' . $psRoute . '" title="' . Translator::i18n('Voltar para a lista') . '">
		<i class="glyphicon glyphicon-list"></i> ' . Translator::i18n('Lista') . '
		</a>
		</li>';
		}
		else
		{
		$lsGrid .= '
		<li>
		<a href="/' . $psRoute . '/add" title=" ' . Translator::i18n('Adicionar novo') . '">
		<i class="glyphicon glyphicon-plus-sign"></i> ' . Translator::i18n('Adicionar') . '
		</a>
		</li>';
		
		$lsGrid .= '
		<li>
		<a href="/' . $psRoute . '/trash" title="' . Translator::i18n('Ir para lixeira') . '">
		<i class="glyphicon glyphicon-trash"></i> ' . Translator::i18n('Lixeira') . '
		</a>
		</li>';
		}
		
		$lsGrid .= '</ul>';
		
		$laSearchableFields = $this->getSearchableFields();
		
		if ($this->get('showSearch') && $laSearchableFields != null)
		{
		$lsGrid .= '
		<div class="navbar-form navbar-right" role="search">
		<div class="input-group" style="margin:0; width:250px;">
		<input id="searchQuery" name="q" type="text" data-act="?" class="form-control" placeholder="' . Translator::i18n('Buscar por') . '" value="' . $this->get('searchQuery') .'" />
		<span id="searchClear" class="input-group-addon"><i class="glyphicon glyphicon-remove-circle"></i></span>
		</div>
		</div>';
		
		$lsGrid .= '
		<ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Buscar por:') . '<b class="caret"></b></a>
		<ul class="dropdown-menu" role="menu">';
		
		foreach ($laSearchableFields as $lsField => $lsLabel)
		{
		$lsGrid .= '
		<li>
		<a class="searchField" href="#" data-field="' . $lsField . '">
		' . Translator::i18n($lsLabel) . '
		</a>
		</li>';
		}
		
		$lsGrid .= '
		</ul>
		</li>
		</ul>';
		}
		
		$lsGrid .= '
		</div>
		</nav>
		</p>';

		return $lsGrid;
	}

	/**
	 *
	 * @return int
	 */
	public function getSearchableFields ()
	{
		$laSearchable = null;
		$paColums = $this->getColumn();
	
		if (is_array($paColums))
		{
			foreach ($paColums as $loColumn)
			{
				if ($loColumn->get('enable') && $loColumn->get('visible') && $loColumn->get('searchable'))
				{
					$laSearchable[$loColumn->get('id')] = $loColumn->get('title');
				}
			}
		}
	
		return $laSearchable;
	}
	
	/**
	 *
	 * @return string
	 */
	public function renderTableHeader ()
	{
		$lsSelect = $this->renderHeaderCheck();
	
		$laColums = $this->getColumn();
		$lsTitles = '';
	
		if (is_array($laColums))
		{
			foreach($laColums as $loColumn)
			{
				$lsTitles .= $loColumn->renderHeader($this->get('orderCol'), $this->get('order'));
			}
		}
	
		$lsAction = ($this->get('showColOptions') ? '<th></th>' : '');
	
		$lsTableHeader = '
					<tr class="onion-grid-tr-header active">
						' . $lsSelect . '
						' . $lsTitles . '
						' . $lsAction . '
					</tr>';
	
		return $lsTableHeader;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderHeaderCheck ()
	{
		if ($this->get('checkType') != null)
		{
			return '
				<th class="onion-grid-header-check">
					<a id="changeRowCheck" href="#" title="' . Translator::i18n('Marcar todos') . '" data-inv="' . Translator::i18n('Desmarcar todos') . '">
						<i class="glyphicon glyphicon-unchecked"></i>
					</a>
				</th>';		
		}		
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderRows ()
	{
		$lsRows = '';
		
		$laResults = $this->get('data');
		
		if (is_array($laResults) && count($laResults) > 0)
		{
			foreach ($laResults as $laRes)
			{
				$lsSelect = $this->renderRowCheck($laRes['id']);
				$lsColums = '';
		
				$laColums = $this->getColumn();
		
				if (is_array($laColums))
				{
					foreach($laColums as $loColumn)
					{
						$lsColums .= $loColumn->renderColumn($laRes);
					}
				}
		
				//TODO:
				$lsAction = $this->renderColOptions();
		
				$lsRows .= '
					<tr id="onion-grid-tr-' . $this->get('id')  . '-' . $laRes['id'] . '" class="onion-grid-tr">
						' . $lsSelect . '
						' . $lsColums . '
						' . $lsAction . '
					</tr>';
			}
		}
		else
		{
			$lsRows = $this->renderNotFound();
		}
		
		return $lsRows;
	}
	
	/**
	 * 
	 * @param int $pnId
	 * @return string
	 */
	public function renderRowCheck ($pnId)
	{
		if ($this->get('checkType') != null)
		{
			return '
				<td class="onion-grid-col-check">
					<input type="' . $this->get('checkType') . '" name="ck[]" value="' . $pnId . '" />
				</td>';
		}
		
		return '';
	}
	
	public function renderColOptions ()
	{
		$lsColOptions = '';
		
		if ($this->get('showColOptions'))
		{
			$lsOptions = '';
			$laColOptions = $this->getColOptions();
			
			if (is_array($laColOptions))
			{
				foreach($laColOptions as $loOption)
				{
					$lsOptions .= $loOption->render($this->get('id'), $this->get('orderCol'), $this->get('order'));
				}
			}

			$lsColOptions = '
				<td class="onion-grid-col-actions">
					' . $lsOptions . '
				</td>';
		}
				
		return $lsColOptions;
/*		
		$lsGrid .= '
											<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/view" data-value="' . $laRes['id'] . '">
												<i class="glyphicon glyphicon-eye-open btn-xs" title="' . Translator::i18n('Visualizar') . '"></i>
											</a>&nbsp;
											<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/edit" data-value="' . $laRes['id'] . '">
												<i class="glyphicon glyphicon-edit btn-xs" title="' . Translator::i18n('Editar') . '"></i>
											</a>&nbsp;';
			
		if ($lsBack == 'trash')
		{
			$lsGrid .= '
											<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/move" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja restaurar este registro da lixeira?') . '">
												<i class="glyphicon glyphicon-transfer btn-xs" title="' . Translator::i18n('Restaurar registro') . '"></i>
											</a>&nbsp;
											<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/delete" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja apagar permanentemente este registro?') . '">
												<i class="glyphicon glyphicon-remove btn-xs" title="' . Translator::i18n('Apagar permanentemente') . '"></i>
											</a>';
		}
		else
		{
			$lsGrid .= '
											<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/move" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja mover este registro para a lixeira?') . '">
												<i class="glyphicon glyphicon-trash btn-xs" title="' . Translator::i18n('Mover para lixeira') . '"></i>
											</a>';
		}
*/				
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderNotFound ()
	{
		$lnCols = $this->countColums();
		$lnCols = ($this->get('showColOptions') ? $lnCols + 1 : $lnCols);
		$lnCols = ($this->get('checkType') != null ? $lnCols + 1 : $lnCols);
		
		return '
			<tr>
				<td class="onion-grid-td-not-found" colspan="' . $lnCols . '">
					<p>
						' . Translator::i18n('Nenhum registro encontrado!') . '
					</p>
				</td>
			</tr>';
	}
	
	/**
	 *
	 * @return int
	 */
	public function countColums ()
	{
		$lnCount = 0;
		$paColums = $this->getColumn();
	
		if (is_array($paColums))
		{
			foreach ($paColums as $loColumn)
			{
				if ($loColumn->get('enable') && $loColumn->get('visible'))
				{
					$lnCount ++;
				}
			}
		}
	
		return $lnCount;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderNavPagination ()
	{
		$lsPaginationNav = '';
		
		if ($this->get('showPaginationNumRows') || $this->get('showPaginationInfo') || $this->get('showPaginationNav'))
		{
			$lsNumRowsCol = '';
			$lsInfoCol = '';
			$lsPaginationCol = '';
			
			$laPaginationNumRow = $this->get('paginationNumRows');
			
			if ($this->get('showPaginationNumRows') && is_array($laPaginationNumRow) && count($laPaginationNumRow) > 0)
			{
				$lsOptions = '';
				
				foreach ($laPaginationNumRow as $lnValue)
				{
					$lsOptions .= '
							<li>
								<a class="selectNumRows" href="#" data-act="?" data-rows="' . $lnValue . '">' . $lnValue . '</a>
							</li>';
				}
				
				$lsNumRowsCol = '				
								<div class="col-md-2">
					   				<div class="btn-group onion-grid-pagination-numrows">
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											' . Translator::i18n('Registros') .' <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											' . $lsOptions . '
							  		    </ul>
									</div>
								</div>';
			}

			$loPagination = $this->get('pagination');
				
			if ($this->get('showPaginationInfo'))
			{
				$laData = $loPagination->get('aData');
				$lnCurrent = ($laData['total'] == 0 ? '0' : $laData['current']);
				
				$lsInfoCol = '
								<div class="col-md-2">
									<div class="onion-grid-pagination-info">
										&nbsp;&nbsp;&nbsp;' . $lnCurrent . ' - ' . $laData['until'] . ' (' . $laData['total'] . ')
									</div>
								</div>';
			}
			
			if ($this->get('showPaginationNav'))
			{
				$lsPaginationCol = '
								<div class="col-md-8">
									' . $loPagination->renderPagination() . '
								</div>';
			}
	
			$lsPaginationNav = '
					<nav class="onion-grid-pagination-nav navbar navbar-default">
						<div class="container">
							<div class="row">
								' . $lsNumRowsCol . '
								' . $lsInfoCol . '
								' . $lsPaginationCol . '
							</div>
						</div>
					</nav>';
		}
		
		return $lsPaginationNav;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function renderAlert ()
	{
		$lsAlertModal = '
			<div id="onionGridAlert" class="onion-grid-alert modal fade bs-example-modal-lg hidden-print" tabindex="-1" role="dialog" aria-labelledby="onionGridAlertLabel" aria-hidden="true">
				<div class="modal-dialog  modal-lg">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="onionGridAlertLabel">' . Translator::i18n('Alerta') . '</h4>
			      		</div>
			      		<div class="modal-body">
			      		</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">' . Translator::i18n('Fechar') . '</button>
			      		</div>
			    	</div>
			  	</div>
			</div>';
		
		return $lsAlertModal;
	}

	/**
	 * 
	 * @return string
	 */
	public function renderModal ()
	{
		$lsModal = '
			<div id="onionGridModalConfirmation" class="onion-grid-modal modal fade bs-example-modal-lg hidden-print" tabindex="-1" role="dialog" aria-labelledby="onionGridModalConfirmationLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="onionGridModalConfirmationLabel">' . Translator::i18n('Confirmação') . '</h4>
			      		</div>
			      		<div class="modal-body">
			      		</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">' . Translator::i18n('Fechar') . '</button>
			        		<button id="onionModalConfirmBtn" type="button" class="btn btn-primary" data-dismiss="modal">' . Translator::i18n('Confirmar') . '</button>
			        	</div>
			    	</div>
			  	</div>
			</div>';

		return $lsModal;
	}
}
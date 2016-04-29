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

namespace Onion\Mvc\Controller;
use Onion\Mvc\Controller\ControllerActionBase;
use Onion\View\Model\ViewModel;
use Onion\Paginator\Pagination;
use Onion\Application\Application;
use Onion\I18n\Translator;
use Onion\Lib\Search;
use Onion\Log\Debug;
use Onion\Log\Event;
use Onion\Lib\String;
use Onion\Json\Json;

abstract class ControllerAction extends ControllerActionBase
{

	/**
	 * 
	 * @return \Onion\View\Model\ViewModel
	 */
	public function indexAction ()
	{
		$lsGrid = $this->grid();
		
		$loView = new ViewModel(
				array(
					'lsGrid' => $lsGrid,
					'lsFolder' => '',
					'lsTitleP' => $this->_sTitleP,
				)
		);
		
		$lsGrid = '<h1>' . $this->_sTitleP . '</h1>' . $lsGrid;
		
		return $this->setResponseType($loView, $lsGrid);
	}
	
	
	/**
	 * 
	 * @return \Onion\View\Model\ViewModel
	 */
	public function trashAction ()
	{
		$lsGrid = $this->grid(1, 0, "trash");
		
		$loView = new ViewModel(
				array(
					'lsGrid' => $lsGrid,
					'lsFolder' => Translator::i18n(' - Lixeira'),
					'lsTitleP' => $this->_sTitleP,
				)
		);
		
		$lsGrid = '<h1>' . $this->_sTitleP . Translator::i18n(' - Lixeira') . '</h1>' . $lsGrid;
		
		return $this->setResponseType($loView, $lsGrid);
	}
	
	
	/**
	 * 
	 * @return \Onion\View\Model\ViewModel
	 */
	public function messageAction ()
	{
		$this->_sWindowType = $this->request('w', 'default');
		
		$loForm = Application::factory($this->_sForm);
		
		$loView = new viewModel(array(
			'loForm' => $loForm,
			'lsTitleS' => $this->_sTitleS,
			'lsTitleP' => $this->_sTitleP,
			'lsRoute' => $this->_sRoute
		));
		
		return $this->setResponseType($loView);
	}
	
	
	/**
	 * 
	 * @param number $pnStatus
	 * @param number $pbActive
	 * @param string $psBack
	 * @param string $psFolderTitle
	 * @return unknown|\Onion\View\Model\ViewModel
	 */
	public function grid ($pnStatus = 0, $pbActive = 1, $psBack = 'index')
	{
		$lnPage = $this->request('p', 0);
		$lnRows = $this->request('rows', $this->_nGridNumRows);
		$lsOrder = $this->request('ord', $this->_sGridOrder);
		$lsOrderCol = $this->request('col', $this->_sGridOrderCol);
		$lsQuery = $this->request('q', '');
		$lsField = $this->request('f', '');
		$lsField = (isset($this->_aSearchFields[$lsField]) ? $lsField : $this->_sSearchFieldDefault);
		$lsWhere = '';
		
		if ($this->_bSearch && !empty($lsQuery))
		{
			$loSearch = new Search();
			$loSearch->set('sSearchFields', $lsField);
			$lsWhere .= $loSearch->createRLikeQuery('"' . $lsQuery . '"', 'r');
		}
		
		$laParams = array(
			'status'	=> $pnStatus,
			'active' 	=> $pbActive,
			'rows'		=> $lnRows,
			'page' 		=> $lnPage,
			'col' 		=> $lsOrderCol,
			'ord' 		=> $lsOrder,
			'q' 		=> $lsQuery,
			'where' 	=> $lsWhere,
		);

		if (method_exists($this, 'gridBeforeSelect'))
		{
			$laParams = $this->gridBeforeSelect($laParams);
		}
		
		if (method_exists($this, 'gridList'))
		{
			$laResult = $this->gridList($laParams);
		}
		else 
		{
			$laResult = $this->getEntityManager()->getRepository($this->_sEntityExtended)->getList($laParams, $lbCache=false);
		}
		
		if (method_exists($this, 'gridAfterSelect'))
		{
			$laResult = $this->gridAfterSelect($laResult);
		}
		
		$loPagination = new Pagination();
		$loPagination->set('sUri', '?' . $this->_sGetParam);
		$loPagination->set('nResPerPage', $lnRows);			
		$loPagination->setPaginator(
				$laResult['totalCount'],
				$lnPage
		);
		
		$laMessages = '';
		
		$laMessages = $this->flashMessenger()->getMessages();

		//Debug::displayd($laResult['resultSet']);
		$lsGrid = $this->renderGrid(array(
			$psBack,
			$laMessages,
			$laResult['resultSet'],
			$loPagination,
			$lnRows,
			$lsQuery,
			$lsField,
			array('ord' => $lsOrder, 'col' => $lsOrderCol)
		));

		if (method_exists($this, 'gridAfterRender'))
		{
			$lsGrid = $this->gridAfterRender($lsGrid);
		}
		
		return $lsGrid;
	}
	
	
	/**
	 * 
	 * @return unknown
	 */
	public function searchSelectAction ()
	{
		$lnPage = $this->request('p', 0);
		$lnRows = $this->request('rows', $this->_nSearchGridNumRows);
		$lsOrder = $this->request('ord', $this->_sSearchGridOrder);
		$lsOrderCol = $this->request('col', $this->_sSearchGridOrderCol);
		$lsQuery = $this->request('q', '');
		$lsField = $this->request('f', '');
		$lsField = (isset($this->_aSearchFields[$lsField]) ? $lsField : $this->_sSearchFieldDefault);
		$lsMark = $this->request('st', 'radio');
		$lsWindow = $this->request('w', 'default');
		$lsFilter = $this->request('filter', '');		
		
		$laFilter = json_decode(base64_decode($lsFilter), true);
	
		$lsWhere = (isset($laFilter['where']) ? $laFilter['where'] : "");

		if ($this->_bSearch && !empty($lsQuery))
		{
			$loSearch = new Search();
			$loSearch->set('sSearchFields', $lsField);
			$lsWhere .= $loSearch->createRLikeQuery('"' . $lsQuery . '"', 'r');
		}
	
		$laParams = array(
			'status'	=> (isset($laFilter['status']) ? $laFilter['status'] : 0),
			'active' 	=> (isset($laFilter['active']) ? $laFilter['active'] : 1),
			'rows'		=> $this->_nSearchGridNumRows,
			'page' 		=> $lnPage,
			'col' 		=> $lsOrderCol,
			'ord' 		=> $lsOrder,
			'q' 		=> $lsQuery,
			'where' 	=> $lsWhere,
		);
		
		if (method_exists($this, 'searchBeforeSelect'))
		{
			$laParams = $this->searchBeforeSelect($laParams);
		}
	
		if (method_exists($this, 'searchList'))
		{
			$laResult = $this->searchList($laParams);
		}
		else
		{
			$laResult = $this->getEntityManager()->getRepository($this->_sEntityExtended)->getList($laParams, $lbCache=false);
		}
	
		if (method_exists($this, 'searchAfterSelect'))
		{
			$laResult = $this->searchAfterSelect($laResult);
		}
		
		$loPagination = new Pagination();
		$loPagination->set('sUri', "/" . $this->_sRoute . '/search-select/?');
		$loPagination->set('nResPerPage', $lnRows);
		$loPagination->setPaginator(
				$laResult['totalCount'],
				$lnPage
		);
	
		//Debug::displayd($laResult['resultSet']);
		$lsGrid = $this->renderSelectGrid(array(
			$laResult['resultSet'],
			$loPagination,
			$lnRows,
			$lsQuery,
			$lsField,
			array('ord' => $lsOrder, 'col' => $lsOrderCol),
			'/' . $this->_sRoute .'/search-select',
			$this->_sSearchLabelField,
			$lsWindow,
			$lsFilter,
			),
			$lsMark
		);
		
		if (method_exists($this, 'searchAfterRender'))
		{
			$lsGrid = $this->searchAfterRender($lsGrid);
		}
	
		$loView = new ViewModel();
		return $this->setResponseType($loView, $lsGrid);
	}	
	
	
	/**
	 * 
	 */
	public function addAction ()
	{
		$loForm = Application::factory($this->_sForm);
		$loForm->setObjectManager($this->getEntityManager());
		$loForm->setActionType('add');
		$loForm->setEntity($this->_sEntityExtended);
		$loForm->setForm();
		$loEntity = null;
		
		$this->_sWindowType = $this->request('w', 'default');
		$loForm->setWindowType($this->_sWindowType);
		$loForm->setCancelBtnType($this->_sWindowType == 'default' ? 'cancel' : 'close');
		
		if (method_exists($this, 'addFormSettings'))
		{
			$this->addFormSettings($loForm);
		}
		
		$lsSecurity = $this->requestPost('security', null);
	
		if ($this->requestIsPost() && $lsSecurity !== null)
		{
			$loEntity = Application::factory($this->_sEntity);
			$loEntity->getObject();
			$loForm->bind($loEntity);
			
			if (method_exists($loForm, 'getInputFilter'))
			{
				$loForm->setInputFilter($loForm->getInputFilter());
			}

			$loForm->setData($this->requestPost());

			if ($loForm->isValid())
			{
				$loEntity->setDefault($this->_sTable);
				$laPostData = $loForm->getDataForm();
				$laFileData = $this->requestFiles()->toArray();
				$loEntity->populate($laPostData);
				$loEntity->addValidate();
				
				$lbResponse = true;
				
				if (method_exists($this, 'addBeforeFlush'))
				{
					$lbResponse = $this->addBeforeFlush($laPostData, $laFileData, $loForm, $loEntity);
				}
				
				if ($lbResponse)
				{
					$lnUserId = $this->getAuthenticatedUser();
						
					if (null !== $lnUserId)
					{
						$loEntity->set('User_id', $lnUserId);							
					}
						
					$this->getEntityManager()->persist($loEntity);
					$lbResponse = $this->entityFlush();

					if (method_exists($this, 'addAfterFlush'))
					{
						$lbResponse = $this->addAfterFlush($laPostData, $laFileData, $loForm, $loEntity, $lbResponse);
					}
					
					if ($lbResponse)
					{
						$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>Translator::i18n('Registrado com sucesso!')));
					}
				}
				
				if (method_exists($this, 'addRedirect'))
				{
					$lmReturn = $this->addRedirect($loEntity);
					
					if ($lmReturn !== false)
					{
						return $lmReturn;
					}
				}
				else 
				{
					if ($this->_sWindowType == 'default')
					{
						return $this->redirect()->toRoute($this->_sRoute, array(
							'action' => $this->requestPost('back', 'index')
						));
					}
					else
					{
						return $this->redirect()->toRoute($this->_sRoute, array(
							'action' => 'message'
						));
					}
				}
			}
		}

		if (method_exists($this, 'addFormSettingsAfter'))
		{
			$this->addFormSettingsAfter($loForm, $loEntity);
		}
		
		$loView = new ViewModel(array(
			'lsBack' => $this->requestPost('back', 'index'),
			'lsTitleS' => $this->_sTitleS,
			'lsTitleP' => $this->_sTitleP,
			'lsRoute' => $this->_sRoute,
			'loForm' => $loForm,
		));
		
		return $this->setResponseType($loView);
	}
	
	
	/**
	 * 
	 */
	public function editAction ($pnId = null)
	{
		$lnId = $this->request('id', $pnId);
		$this->_sWindowType = $this->request('w', 'default');

		if ($lnId === null)
		{
			return $this->redirect()->toRoute($this->_sRoute, array(
				'action' => 'add'
			));
		}
	
		$loEntity = $this->getEntityManager()->find($this->_sEntityExtended, $lnId);
		$loEntity->getObject();
		
		$loForm = Application::factory($this->_sForm);
		$loForm->setObjectManager($this->getEntityManager());
		$loForm->setActionType('edit');
		$loForm->setEntity($this->_sEntityExtended);
		$loForm->setRecordId($lnId);
		$loForm->setForm();
		$loForm->setWindowType($this->_sWindowType);
		$loForm->setCancelBtnType($this->_sWindowType == 'default' ? 'cancel' : 'close');
		
		if (method_exists($this, 'editFormSettings'))
		{
			$this->editFormSettings($loForm, $loEntity);
		}
		
		$loForm->setBindOnValidate(false);
		$loForm->bind($loEntity);
				
		$lsSecurity = $this->requestPost('security', null);

		if ($this->requestIsPost() && $lsSecurity !== null)
		{
			if (method_exists($loForm, 'getInputFilter'))
			{
				$loForm->setInputFilter($loForm->getInputFilter());
			}
			
			$loForm->setData($this->requestPost());
			
			$loForm->setEntityData($loEntity);			
			
			if ($loForm->isValid())
			{
				$loForm->bindValues();

				$laPostData = $loForm->getDataForm();
				$laFileData = $this->requestFiles()->toArray();

				$lbResponse = true;
				
				if (method_exists($this, 'editBeforeFlush'))
				{
					$lbResponse = $this->editBeforeFlush($laPostData, $laFileData, $loForm, $loEntity);
				}
				
				$loEntityOriginal = clone $loEntity;
				$loEntityOriginal->set('id', $lnId);
				$loEntity->populate($laPostData);
				$loEntity->editValidate();
				
				if ($lbResponse)
				{
					$this->getEntityManager()->persist($loEntity);
					$lbResponse = $this->entityFlush();
										
					if (method_exists($this, 'editAfterFlush'))
					{
						$lbResponse = $this->editAfterFlush($laPostData, $laFileData, $loForm, $loEntity, $loEntityOriginal, $lbResponse);
					}
					
					if ($lbResponse)
					{
						$lnUserId = $this->getAuthenticatedUser();
						$laChanges = $this->checkChangeEntity($loEntityOriginal, $loEntity);
						Event::log(array('userId'=>$lnUserId, 'module'=>$this->_sModule, 'action'=>'edit', 'record'=>$loEntity->get('id'), 'changes'=>$laChanges), 6);
						
						$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>Translator::i18n('Alterado com sucesso!')));
					}
				}
	
				if (method_exists($this, 'editRedirect'))
				{
					$lmReturn = $this->editRedirect($loEntity);
					
					if ($lmReturn !== false)
					{
						return $lmReturn;
					}
				}
				else 
				{
					if ($this->_sWindowType == 'default')
					{
						return $this->redirect()->toRoute($this->_sRoute, array(
							'action' => $this->requestPost('back', 'index')
						));
					}
					else
					{
						return $this->redirect()->toRoute($this->_sRoute, array(
							'action' => 'message'
						));
					}
				}
			}
		}
		
		if (method_exists($this, 'editFormSettingsAfter'))
		{
			$this->editFormSettingsAfter($loForm, $loEntity);
		}
	
		$loView = new ViewModel(array(
			'lsBack' => $this->requestPost('back', 'index'),
			'lsTitleS' => $this->_sTitleS,
			'lsTitleP' => $this->_sTitleP,
			'lsRoute' => $this->_sRoute,
			'lnId' => $lnId,
			'loForm' => $loForm
		));
		
		return $this->setResponseType($loView);
	}
	
	
	/**
	 * 
	 * @return unknown|\Onion\View\Model\ViewModel
	 */
	public function viewAction ()
	{
		$lnId = $this->request('id', null);
		$this->_sWindowType = $this->request('w', 'default');
		
		if ($lnId === null)
		{
			return $this->redirect()->toRoute($this->_sRoute);
		}
	
		$loEntity = $this->getEntityManager()->find($this->_sEntityExtended, $lnId);

		$loView = new ViewModel();

		if ($this->isXmlHttpRequest())
		{
			$lsView = $this->renderView(array(
				'', '', '',
				$loEntity->getFormatedData()
			), true);
		}
		else 
		{
			$lsView = $this->renderView(array(
				$this->request('back', 'index'),
				$this->_sTitleS,
				$this->_sRoute,
				$loEntity->getFormatedData()
			));
		}				
			
		$loView->setVariables(array(
			'lsTitle' => $this->_sTitleS,
			'lsBack' => $this->request('back', 'index'),
			'lsRoute' => $this->_sRoute,
			'laData' => $loEntity->getFormatedData(),
			'lsView' => $lsView
		), true);

		return $this->setResponseType($loView, $lsView);
	}
	
	
	/**
	 * 
	 */
	public function moveAction ()
	{
		$lnId = $this->request('id', null);
	
		if ($this->isXmlHttpRequest())
		{
			$this->set('_bPushMessage', true);
		}
		
		if ($lnId === null)
		{
			if ($this->isXmlHttpRequest())
			{
				$loView = new ViewModel();
				return $this->setResponseType($loView);
			}
			else
			{
				return $this->redirect()->toRoute($this->_sRoute, array(
					'action' => $this->requestPost('back', 'index')
				));
			}
		}
	
		$loEntity = $this->getEntityManager()->find($this->_sEntity, $lnId);
	
		if ($loEntity)
		{
			$loEntity->moveTo();
			
			if ($this->entityFlush())
			{	
				$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>Translator::i18n('Movido com sucesso!')));
			}
		}
		else
		{
			$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>Translator::i18n('Ops! Algo errado aconteceu. O registro não foi encontrado!')));
		}
			
		if ($this->isXmlHttpRequest())
		{
			$loView = new ViewModel();
			return $this->setResponseType($loView);
		}
		else
		{
			return $this->redirect()->toRoute($this->_sRoute, array(
				'action' => $this->requestPost('back', 'index')
			));
		}
	}

	
	/**
	 * 
	 */
	public function moveListAction ()
	{
		$laCheck = $this->request('ckd');

		if (! is_array($laCheck))
		{
			$laCheck = explode(',', $laCheck);
			
			if (empty($laCheck[0]))
			{
				$laCheck = null;
			}
		}

		if ($laCheck === null)
		{
			return $this->redirect()->toRoute($this->_sRoute, array(
				'action' => $this->requestQuery('back', 'index')
			));
		}
		else
		{
			$lbError = false;
			
			foreach ($laCheck as $lnId)
			{
				$loEntity = $this->getEntityManager()->find($this->_sEntity, $lnId);
				
				if ($loEntity)
				{
					$loEntity->moveTo();
					
					$lbError = ! $this->entityFlush();
				}
				else
				{
					$lbError = true;
				}
			}
			
			if (! $lbError)
			{
				$this->flashMessenger()->addMessage(
						array(
							'id' => $this->get('_sModule') . '-' . microtime(true),
							'hidden' => $this->get('_bHiddenPushMessage'),
							'push' => $this->get('_bPushMessage'),
							'type' => 'success',
							'msg' => Translator::i18n('Movido com sucesso!')
						));
			}
			else
			{
				$this->flashMessenger()->addMessage(
						array(
							'id' => $this->get('_sModule') . '-' . microtime(true),
							'hidden' => $this->get('_bHiddenPushMessage'),
							'push' => $this->get('_bPushMessage'), 
							'type'=>'warning', 
							'msg'=>Translator::i18n('Ops! Algo errado aconteceu.!')
						));
			}
		}
				
		return $this->redirect()->toRoute($this->_sRoute, array(
			'action' => $this->requestQuery('back', 'index')
		));
	}
	
	
	/**
	 * 
	 */
	public function deleteListAction ()
	{
		if ($this->requestIsPost())
		{
			$laCheck = $this->requestPost('ckd');
	
			if (!is_array($laCheck))
			{
				$laCheck = explode(',', $laCheck);
	
				if (empty($laCheck[0]))
				{
					$laCheck = null;
				}
			}
				
			if ($laCheck === null)
			{
				return $this->redirect()->toRoute($this->_sRoute, array(
					'action' => $this->requestPost('back', 'index')
				));
			}
			else
			{
				$lbError = false;
	
				foreach ($laCheck as $lnId)
				{
					$loEntity = $this->getEntityManager()->find($this->_sEntity, $lnId);
	
					if ($loEntity)
					{
						if (!$loEntity->isSystem())
						{
							$this->getEntityManager()->remove($loEntity);
							$lbError = !$this->entityFlush();
						}
						else 
						{
							$lbError = true;
							$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>sprintf(Translator::i18n('Ops! O registro (id=%s) não pode ser apagado, pois é um registro do sistema.'), $lnId)));							
						}
					}
					else
					{
						$lbError = true;
						$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>sprintf(Translator::i18n('Ops! O registro (id=%s) não foi encontrado para ser apagado!'), $lnId)));
					}
				}
	
				if (!$lbError)
				{
					$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>Translator::i18n('Registro(s) apagados(s) permanentemente com sucesso!')));
				}
			}
				
			return $this->redirect()->toRoute($this->_sRoute, array(
				'action' => $this->requestPost('back', 'index')
			));
		}
	}
	
	
	/**
	 * 
	 */
	public function deleteAction ()
	{
		$lnId = $this->request('id', null);
	
		if ($lnId === null)
		{
			return $this->redirect()->toRoute($this->_sRoute, array(
				'action' => $this->requestPost('back', 'index')
			));
		}
	
		$loEntity = $this->getEntityManager()->find($this->_sEntity, $lnId);
	
		if ($loEntity)
		{
			if (!$loEntity->isSystem())
			{
				$this->getEntityManager()->remove($loEntity);
				
				if ($this->entityFlush())
				{
					$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>Translator::i18n('Registro apagado permanentemente com sucesso!')));
				}
			}
			else
			{
				$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>sprintf(Translator::i18n('Ops! O registro (id=%s) não pode ser apagado, pois é um registro do sistema.'), $lnId)));							
			}
		}
		else
		{
			$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>sprintf(Translator::i18n('Ops! O registro (id=%s) não foi encontrado para ser apagado!'), $lnId)));
		}
			
		return $this->redirect()->toRoute($this->_sRoute, array(
			'action' => $this->requestPost('back', 'index')
		));
	}

	
	/**
	 * 
	 */
	public function pdfAction ()
	{
		
	}
	
	
	/**
	 * 
	 * @param array $paParams
	 * @return string
	 */
	public function renderGrid(array $paParams = array())
	{
		list(
			$lsBack,
			$laMessages,
			$laResults,
			$loPagination,
			$lnRows,
			$lsQuery,
			$lsField,
			$laOrder
		) = $paParams;
		
		$laData = $loPagination->get('aData');
		
		$lnCurrent = ($laData['total'] == 0 ? '0' : $laData['current']);
		
		$lsGrid = '<link href="/vendor/m3uzz/onionjs-0.16.4/dist/css/grid.css" media="all" rel="stylesheet" type="text/css">';
		
		$lsGrid .= '
		<div id="gridContent">';
		
		if (is_array($laMessages))
		{
			foreach ($laMessages as $lnKey => $laMessage)
			{
				$lsGrid .= '<div class="alert alert-' . $laMessage['type'] . ' alert-dismissable">';
				$lsGrid .= '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
				$lsGrid .= $laMessage['msg'];
				$lsGrid .= '</div>';
			}
		}
		
		$lsGrid .= '
		 	<form id="actForm" action="#" method="POST" data-request="HTTP">
				<input id="back" type="hidden" name="back" value="' . $lsBack . '" />
				<input id="id" type="hidden" name="id" />
		 		<input id="ckd" type="hidden" name="ckd" />
				<input id="col" type="hidden" name="col" value="' . $laOrder['col'] . '" />
				<input id="ord" type="hidden" name="ord" value="' . $laOrder['ord'] . '" />
				<input id="rows" type="hidden" name="rows" value="' . $lnRows . '" />
				<input id="p" type="hidden" name="p" value="' . $lnCurrent . '" />		
				<input id="q" type="hidden" name="q" value="' . $lsQuery . '" />
				<input id="f" type="hidden" name="f" value="' . $lsField . '" />
						
				<!--#%FORM_FILTER%#-->		
			</form>';
		
//TODO: automatizar toolbar

		if ($this->_bShowToolbar)
		{
			$lsGrid .= '
				<nav class="navbar navbar-inverse" role="navigation">
					<div class="container-fluid">
			   			<ul class="nav navbar-nav">';
					
					if (isset($this->_aMassActions) && is_array($this->_aMassActions))
					{
						if (count($this->_aMassActions) > 0)
						{
							$lsGrid .= '
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Ações em massa') . '<b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">';
		
									foreach ($this->_aMassActions as $lsAct => $laActions)
									{
										if (empty($laActions['folder']) || (isset($laActions['folder'][$lsBack]) && $laActions['folder'][$lsBack] == true))
										{
											if (preg_match("/massActBtn/", $laActions['class']))
											{
												$lsGrid .= '
													<li>
														<a class="' . $laActions['class'] . '" href="#" data-msg="'. Translator::i18n($laActions['message']) .'" data-act="' . $laActions['url'] . '" title="' . Translator::i18n($laActions['description']) . '">
															<i class="' . $laActions['icon'] . '"></i> ' . Translator::i18n($laActions['title']) . '
														</a>
													</li>';
											}
											elseif (preg_match("/massActPopUpBtn/", $laActions['class']))
											{
												$lsGrid .= '
													<li>
														<a class="' . $laActions['class'] . '" href="#" data-url="' . $laActions['url'] . '" data-params="" data-wname="' . $this->_sRoute . '-' . $lsAct . '" data-wwindow="modal" data-wwidth="100%" data-wheight="100%" title="' . Translator::i18n($laActions['description']) . '">
															<i class="' . $laActions['icon'] . '"></i> ' . Translator::i18n($laActions['title']) . '
														</a>
													</li>';
											}
										}
									}
		
							$lsGrid .= '						    					
						  		    </ul>
								</li>';
						}
					}
					else 
					{
						$lsGrid .= '
			   				<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Ações em massa') . '<b class="caret"></b></a>
								<ul class="dropdown-menu" role="menu">';
								
								if ($lsBack == 'trash')
								{
									$lsGrid .= '
									    <li>
								    		<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja mover os registros selecionados para a lixeira?') . '" data-act="/' . $this->_sRoute . '/move-list">
								    			<i class="glyphicon glyphicon-transfer"></i> ' . Translator::i18n('Restaurar registros') . '
								    		</a>
									    </li>
										<li class="divider"></li>
										<li>
											<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja apagar permanentemente os registros selecionados?') . '" data-act="/' . $this->_sRoute . '/delete-list">
								    			<i class="glyphicon glyphicon-remove"></i> ' . Translator::i18n('Apagar permanentemente') . '
								    		</a>
									    </li>';
								}
								else 
								{
									$lsGrid .= '								
										<li>
											<a class="massActBtn" href="#" data-msg="' . Translator::i18n('Você tem certeza que deseja mover os registros selecionados para a lixeira?') . '" data-act="/' . $this->_sRoute . '/move-list">
												<i class="glyphicon glyphicon-trash"></i> ' . Translator::i18n('Mover para lixeira') . '
											</a>
										</li>';
								}
	
						$lsGrid .= '						    					
					  		    </ul>
							</li>';
					}
					
					if ($this->_bAddButton)
					{
						$lsGrid .= '
									<li>
										<a href="/' . $this->_sRoute . '/add" title=" ' . Translator::i18n('Adicionar novo') . ' ' . $this->_sTitleS . '">
											<i class="glyphicon glyphicon-plus-sign"></i> ' . Translator::i18n('Adicionar') . '
										</a>
									</li>';
					}
					
					if (isset($this->_aFolders) && is_array($this->_aFolders))
					{
						if (count($this->_aFolders) > 0)
						{
							$lsGrid .= '
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;' . Translator::i18n('Ir para a pasta') . '<b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">';
		
									foreach ($this->_aFolders as $laFolder)
									{
										$lsGrid .= '
											<li>
												<a href="' . $laFolder['url'] . '" title="' . Translator::i18n($laFolder['description']) . '">
													<i class="' . $laFolder['icon'] . '"></i> ' . Translator::i18n($laFolder['title']) . '
												</a>
											</li>';
									}
		
							$lsGrid .= '						    					
						  		    </ul>
								</li>';
						}
					}
					else
					{
						if ($lsBack == 'trash')
						{
							
							$lsGrid .= '
								<li>
									<a href="/' . $this->_sRoute . '/?' . $this->_sGetParam . '" title="' . Translator::i18n('Voltar para a entrada') . '">
										<i class="glyphicon glyphicon-inbox"></i> ' . Translator::i18n('Entrada') . '
									</a>
								</li>';
						}
						else
						{
							$lsGrid .= '
								<li>	
									<a href="/' . $this->_sRoute . '/trash/?' . $this->_sGetParam . '" title="' . Translator::i18n('Ir para lixeira') . '">
										<i class="glyphicon glyphicon-trash"></i> ' . Translator::i18n('Lixeira') . '
									</a>
								</li>';
						}
					}
					
					$lsGrid .= '</ul>';
							
					if ($this->_bSearch && (is_array($this->_aSearchFields) && count($this->_aSearchFields) > 0))
					{
						$lsGrid .= '
							<div class="navbar-form navbar-right" role="search">
						    	<div class="input-group" style="margin:0; width:250px;">
									<input id="searchQuery" name="q" type="text" data-act="?' . $this->_sGetParam . '" class="form-control" placeholder="' . Translator::i18n(current($this->_aSearchFields)) . ' " value="' . $lsQuery .'" />
									<span id="searchClear" class="input-group-addon"><i class="glyphicon glyphicon-remove-circle"></i></span>
								</div>
							</div>';
						
						$lsGrid .= '
								<ul class="nav navbar-nav navbar-right">
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Buscar por:') . '<b class="caret"></b></a>
										<ul class="dropdown-menu" role="menu">';
			
										foreach ($this->_aSearchFields as $lsField => $lsLabel)
										{
											$lsGrid .= '	
												<li>
													<a class="searchField" href="#" data-field="' . $lsField . '" title="' . Translator::i18n($lsLabel) . '">
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
			}

			$lsGrid .= '
			<div class="table-responsive">
				<table class="table table-hover table-condensed">
					<tr class="active">';
						
						if ($this->_bGridCheckbox)
						{					
							$lsGrid .= 
								'<th style="text-align:center;">
									<a id="changeRowCheck" href="#" title="' . Translator::i18n('Marcar todos') . '" data-inv="' . Translator::i18n('Desmarcar todos') . '"><i class="glyphicon glyphicon-unchecked"></i></a>
								</th>';
						}

							if (isset($this->_aGridCols) && is_array($this->_aGridCols))
							{
								foreach($this->_aGridCols as $lmField => $lsTitle)
								{
									$lsGrid .= '<th style="overflow:auto; resize:horizontal;">';
									
									if (is_string($lmField))
									{
										if ($laOrder['col'] == $lmField)
										{
											if ($laOrder['ord'] == 'ASC')
											{
												$lsGrid .= '<a class="colOrder" href="#" data-act="?' . $this->_sGetParam . '" data-col="' . $lmField . '" data-ord="DESC">' . $lsTitle . '<b class="caret"></b></a>';
											}
											else 
											{
												$lsGrid .= '<a class="colOrder dropup" href="#" data-act="?' . $this->_sGetParam . '" data-col="' . $lmField . '" data-ord="ASC">' . $lsTitle . '<b class="caret"></b></a>';
											}
										}
										else 
										{
											$lsGrid .= '<a class="colOrder" href="#" data-act="?' . $this->_sGetParam . '" data-col="' . $lmField . '" data-ord="ASC">' . $lsTitle . '</a>';
										}
									}
									else 
									{
										$lsGrid .= $lsTitle;
									}
									
									$lsGrid .= '</th>';
								}
							}

							if ($this->_bIndividualButtons)
							{
								$lsGrid .= '<th></th>';
							}
							
						$lsGrid .= '</tr>';

					if (is_array($laResults) && count($laResults) > 0)
					{ 
						foreach ($laResults as $laRes)
						{
							$lsGrid .= '
									<tr class="gridRow">';
							
							if ($this->_bGridCheckbox)
							{
								$lsGrid .=	
										'<td class="onion-grid-col-check" style="text-align:center; width:15px; background-color:#f0f0f0;">
											<input type="checkbox" name="ck[]" value="' . $laRes['id'] . '" />
										</td>';
							}
							
							if (isset($this->_aGridFields) && is_array($this->_aGridFields))
							{
								foreach($this->_aGridFields as $lnKey => $lsField)
								{
									$lsAlign = '';
									$lsWidth = '';
									$lsColor = '';
									$lsContent = '';

									if (isset($this->_aGridAlign[$lnKey]) && !empty($this->_aGridAlign[$lnKey]))
									{
										$lsAlign = "text-align:{$this->_aGridAlign[$lnKey]};";
									}
									
									if (isset($this->_aGridWidth[$lnKey]) && !empty($this->_aGridWidth[$lnKey]))
									{
										$lsWidth = " width:{$this->_aGridWidth[$lnKey]};";
									}
									
									if (isset($this->_aGridColor[$lnKey]) && !empty($this->_aGridColor[$lnKey]))
									{
										$lsColor = " background:{$this->_aGridColor[$lnKey]};";
									}
									
									
									if (array_key_exists($lsField, $laRes))
									{
										if (method_exists($this, 'formatFieldToGrid'))
										{
											$lsContent = $this->formatFieldToGrid($lsField, $laRes[$lsField]);
										}
										else
										{
											$lsContent = $laRes[$lsField];
										}
									}
									
									$lsStyle = $lsAlign . $lsWidth . $lsColor;
									
									if (!empty($lsStyle))
									{
										$lsStyle = 'style="' . $lsStyle . '"';
									}
									
									$lsGrid .= '<td class="onion-grid-col-' . $this->_sModule . '-' . $lsField . '" ' . $lsStyle . '>' . $lsContent . '</td>';
								}
							}

							if ($this->_bIndividualButtons)
							{
								$lsGrid .= '<td class="onion-grid-col-actions" style="width:35px; background-color:#f0f0f0;">';
	
								if (isset($this->_aIndividualButtons) && is_array($this->_aIndividualButtons))
								{
									if (count($this->_aIndividualButtons) > 1)
									{
										$lsGrid .= '
												<div class="dropdown">
													<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Options"><i class="glyphicon glyphicon-menu-hamburger"></i><span class="caret"></span></a>
													<ul class="dropdown-menu pull-right" role="menu">';
													
														foreach ($this->_aIndividualButtons as $lsAct => $laActions)
														{
															if (empty($laActions['folder']) || $laActions['folder'] == $lsBack)
															{
																$laActions['url'] = preg_replace('/#%ROUTER%#/', $this->_sRoute, $laActions['url']);
																
																$lbConfirm = 'false';
																
																if (!empty($laActions['message']))
																{
																	$lbConfirm = 'true';
																}
																
																$lsParams = '';
																
																if (isset($laActions['params']))
																{
																	$lsParams = $laActions['params'];
																}
																
																if ($laActions['class'] == 'rowActBtn')
																{
																	$lsGrid .= '
																	<li class="text-left text-nowrap">
																		<a class="' . $laActions['class'] . '" href="#" data-act="' . $laActions['url'] . '" data-value="' . $laRes['id'] . '" data-params="' . $lsParams .'" data-confirm="' . $lbConfirm . '" title="' . Translator::i18n($laActions['description']) . '" data-msg="' . $laActions['message'] . '">
																			<i class="' . $laActions['icon'] . '"></i> ' . Translator::i18n($laActions['title']) . '
																		</a>
																	</li>';
																}
																elseif ($laActions['class'] == 'openPopUpBtn')
																{
																	$lsWidth = isset($laActions['width']) ? $laActions['width'] : "80%";
																	$lsHeight = isset($laActions['height']) ? $laActions['height'] : "80%";
																	
																	$lsGrid .= '
																	<li class="text-left text-nowrap">
																		<a class="' . $laActions['class'] . '" href="#" data-url="' . $laActions['url'] . '" data-params="id=' . $laRes['id'] . '&' . $lsParams . '" data-confirm="' . $lbConfirm . '" data-wname="' . $this->_sRoute . '-' . $lsAct . '" data-wwindow="modal" data-wwidth="' . $lsWidth . '" data-wheight="' . $lsHeight . '" title="' . Translator::i18n($laActions['description']) . '" data-msg="' . $laActions['message'] . '">
																			<i class="' . $laActions['icon'] . '"></i> ' . Translator::i18n($laActions['title']) . '
																		</a>
																	</li>';
																}
															}
														}
													
														$lsGrid .= '
										  		    </ul>
												</div>';
									}
									else 
									{
										$laButton = $this->_aIndividualButtons;
										$laActions = array_pop($laButton);
										
										if (empty($laActions['folder']) || $laActions['folder'] == $lsBack)
										{
											
											$lbConfirm = 'false';
											
											if (!empty($laActions['message']))
											{
												$lbConfirm = 'true';
											}
											
											$lsParams = '';
											
											if (isset($laActions['params']))
											{
												$lsParams = $laActions['params'];
											}
											
											if ($laActions['class'] == 'rowActBtn')
											{
												$lsGrid .= '
													<a class="' . $laActions['class'] . '" href="#" data-act="' . $laActions['url'] . '" data-value="' . $laRes['id'] . '" data-params="' . $lsParams .'" data-confirm="' . $lbConfirm . '" title="' . Translator::i18n($laActions['description']) . '" data-msg="' . $laActions['message'] . '">
														<i class="' . $laActions['icon'] . '"></i>
													</a>';
											}
											elseif ($laActions['class'] == 'openPopUpBtn')
											{
												$lsWidth = isset($laActions['width']) ? $laActions['width'] : "80%";
												$lsHeight = isset($laActions['height']) ? $laActions['height'] : "80%";
												$lsWindow = isset($laActions['window']) ? $laActions['window'] : "modal";
												
												$lsGrid .= '
													<a class="' . $laActions['class'] . '" href="#" data-url="' . $laActions['url'] . '" data-params="id=' . $laRes['id'] . '&' . $lsParams . '" data-confirm="' . $lbConfirm . '" data-wname="' . $this->_sRoute . '-' . $laActions['title'] . '" data-wwindow="' . $lsWindow . '" data-wwidth="' . $lsWidth . '" data-wheight="' . $lsHeight . '" title="' . Translator::i18n($laActions['description']) . '" data-msg="' . $laActions['message'] . '">
														<i class="' . $laActions['icon'] . '"></i>
													</a>';
											}
										}
									}
								}
								else
								{
									$lsGrid . '
										<p class="text-center text-nowrap" style="width:70px;">';
									
									if ($this->_bView)
									{
										$lsGrid .= '
													<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/view/" data-value="' . $laRes['id'] . '">
														<i class="glyphicon glyphicon-eye-open btn-xs" title="' . Translator::i18n('Visualizar') . '"></i>
													</a>&nbsp;';
									}
	
									if ($this->_bEdit)
									{
										$lsGrid .= '
													<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/edit/" data-value="' . $laRes['id'] . '">
														<i class="glyphicon glyphicon-edit btn-xs" title="' . Translator::i18n('Editar') . '"></i>
													</a>&nbsp;';
									}
																	
									if ($lsBack == 'trash')
									{
										if ($this->_bDelete)
										{
											$lsGrid .= '
													<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/move/" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja restaurar este registro da lixeira?') . '">
														<i class="glyphicon glyphicon-inbox btn-xs" title="' . Translator::i18n('Restaurar registro') . '"></i>
													</a>&nbsp;';
										}
										
										if ($this->_bRemove)
										{
											$lsGrid .= '
													<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/delete/" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja apagar permanentemente este registro?') . '">
														<i class="glyphicon glyphicon-remove btn-xs" title="' . Translator::i18n('Apagar permanentemente') . '"></i>
													</a>';
										}
									}
									else 
									{
										if ($this->_bDelete)
										{
											$lsGrid .= '
													<a class="rowActBtn" href="#" data-act="/' . $this->_sRoute . '/move/" data-value="' . $laRes['id'] . '" data-confirm="true" data-msg="' . Translator::i18n('Você tem certeza que deseja mover este registro para a lixeira?') . '">
														<i class="glyphicon glyphicon-trash btn-xs" title="' . Translator::i18n('Mover para lixeira') . '"></i>
													</a>';
										}
									}
									
									$lsGrid .= '
										</p>';
								}
							
								$lsGrid .= '
										</td>';
							}
							
							$lsGrid .='
									</tr>';
						}
					}
					else
					{
						$lnCols = count($this->_aGridFields);
						
						if ($this->_bGridCheckbox)
						{
							$lnCols++;
						}
						
						if ($this->_bIndividualButtons)
						{
							$lnCols++;
						}
						
						$lsGrid .= '<tr>';
							$lsGrid .= '<td colspan="' . $lnCols . '" style="text-align:center;">';
								$lsGrid .= '<p>';
									$lsGrid .= '<strong>' . Translator::i18n('Nenhum registro encontrado!') . '</strong>';
								$lsGrid .= '</p>';
							$lsGrid .= '</td>';
						$lsGrid .= '</tr>';	
					}
				
				$lsGrid .= '
						</table>
					</div>';
				
			$lsGrid .= '				
				<nav class="navbar navbar-default">
					<div class="container">
						<div class="row">
							<div class="col-md-2">
				   				<div class="btn-group" style="margin:20px 0;">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										' . Translator::i18n('Registros') .' <span class="caret"></span>
									</button>		   				
									<ul class="dropdown-menu" role="menu">
									    <li><a class="selectNumRows" href="#" data-act="?' . $this->_sGetParam . '" data-rows="6">6</a></li>
										<li><a class="selectNumRows" href="#" data-act="?' . $this->_sGetParam . '" data-rows="12">12</a></li>
									    <li><a class="selectNumRows" href="#" data-act="?' . $this->_sGetParam . '" data-rows="25">25</a></li>
									    <li><a class="selectNumRows" href="#" data-act="?' . $this->_sGetParam . '" data-rows="50">50</a></li>
									    <li><a class="selectNumRows" href="#" data-act="?' . $this->_sGetParam . '" data-rows="100">100</a></li>
						  		    </ul>
								</div>
							</div>				
							<div class="col-md-2">
								<div style="margin-top:26px;"> 
									&nbsp;&nbsp;&nbsp;' . $lnCurrent . ' - ' . $laData['until'] . ' (' . $laData['total'] . ')
								</div>
							</div>
							<div class="col-md-8">
								' . $loPagination->renderPagination($this->_sGetParam) . '
							</div>
						</div>			
					</div>
				</nav>';
			

		$lsGrid .= '			
			<div id="modalAlert" class="modal fade bs-example-modal-lg hidden-print" tabindex="-1" role="dialog" aria-labelledby="modalAlertLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="modalAlertLabel">' . Translator::i18n('Alerta') . '</h4>
			      		</div>
			      		<div class="modal-body">
			      		</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">' . Translator::i18n('Fechar') . '</button>
			      		</div>
			    	</div>
			  	</div>
			</div>';
		
		$lsGrid .= '
			<div id="modalConfirmation" class="modal fade bs-example-modal-lg hidden-print" tabindex="-1" role="dialog" aria-labelledby="modalConfirmationLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="modalConfirmationLabel">' . Translator::i18n('Confirmação') . '</h4>
			      		</div>
			      		<div class="modal-body">
			      		</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">' . Translator::i18n('Fechar') . '</button>
			        		<button id="modalConfirmBtn" type="button" class="btn btn-primary" data-dismiss="modal">' . Translator::i18n('Confirmar') . '</button>
			        	</div>
			    	</div>
			  	</div>
			</div>';
			
		$lsGrid .= '<script type="text/javascript" src="/vendor/m3uzz/onionjs-0.16.4/dist/js/grid.js"></script>';
			
		$lsGrid .= '
		</div>';
		
		return $lsGrid;	
	}
	
	
	/**
	 * 
	 * @param array $paParams
	 * @param string $pbBasic
	 * @return string
	 */
	public function renderView (array $paParams = array(), $pbBasic = false)
	{
		list(
			$lsBack,
			$lsTitle,
			$lsRoute,
			$laData
		) = $paParams;

		$lsContent = "";
		
		if (is_array($laData))
		{
			foreach ($laData as $lsField => $lmValue)
			{
				$lsContent .= '<div>';
				$lsContent .= '<label>' . $lsField . ':&nbsp;&nbsp;</label>';
				$lsContent .= '<span>' . $lmValue . '</span>';
				$lsContent .= '</div>';
			}
		}
		
		if ($pbBasic)
		{
			return $lsContent;
		}
		
		$lsView = '
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>' . $lsTitle . '</h4>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							' . $lsContent . '
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<a href="/' . $lsRoute . '/' . $lsBack . '">Voltar</a>
				</div>
			</div>';
		
		return $lsView;
	}
	
	
	/**
	 * 
	 * @param array $paParams
	 * @param string $psMark
	 * @return string
	 */
	public function renderSelectGrid(array $paParams = array(), $psMark = 'radio')
	{
		list(
				$laResults,
				$loPagination,
				$lnRows,
				$lsQuery,
				$lsField,
				$laOrder,
				$lsUrl,
				$lsLabelField,
				$lsWindow,
				$lsFilter,
		) = $paParams;
	
		$laData = $loPagination->get('aData');
	
		$lnCurrent = ($laData['total'] == 0 ? '0' : $laData['current']);

		$lsGrid = '<link href="/vendor/m3uzz/onionjs-0.16.4/dist/css/grid.css" media="all" rel="stylesheet" type="text/css">';
		
		$lsGrid .= '
		<div id="gridSearchContent">';
	
		$lsGrid .= '
		 	<form id="actFormSearch" action="#" method="POST" data-request="AJAX">
		 		<input type="hidden" name="ckd" />
				<input type="hidden" name="col" value="' . $laOrder['col'] . '" />
				<input type="hidden" name="ord" value="' . $laOrder['ord'] . '" />
				<input type="hidden" name="rows" value="' . $lnRows . '" />
				<input type="hidden" name="p" value="' . $lnCurrent . '" />
				<input type="hidden" name="q" value="' . $lsQuery . '" />
				<input type="hidden" name="f" value="' . $lsField . '" />
				<input type="hidden" name="st" value="' . $psMark . '" />
				<input type="hidden" name="w" value="' . $lsWindow . '" />
				<input type="hidden" name="filter" value="' . $lsFilter . '" />
			</form>';
	
		//TODO: automatizar toolbar
		$lsGrid .= '
			<nav class="navbar navbar-inverse" role="navigation">
				<div class="container-fluid">';
	
		if ($this->_bSearchAddButton)
		{
			$lsGrid .= '
					<ul class="nav navbar-nav">
						<li>
							<a class="openPopUpBtn" href="#" data-url="/' . $this->_sRoute . '/add" data-params="" data-wname="' . $this->_sRoute . '-add" data-wheight="80%" data-wwidth="80%" title="' . Translator::i18n('Adicionar novo') . ' ' . $this->_sTitleS . '">
								<i class="glyphicon glyphicon-plus-sign"></i> ' . Translator::i18n('Adicionar') . '
							</a>
						</li>
					</ul>';
		}
		
		$lsGrid .= '
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a id="modalSearchReloadBtn" data-act="' . $lsUrl . '" href="#" title="' . Translator::i18n('Reload') . '">
								<i class="glyphicon glyphicon-refresh"></i>
							</a>
						</li>
					</ul>';
		
		if ($this->_bSearch && (is_array($this->_aSearchFields) && count($this->_aSearchFields) > 0))
		{
			$lsGrid .= '
						<div class="navbar-form navbar-right" role="search">
					    	<div class="input-group" style="margin:0; width:250px;">
								<input id="searchQuery" name="q" type="text" data-act="' . $lsUrl . '" class="form-control" placeholder="' . Translator::i18n(current($this->_aSearchFields)) . '" value="' . $lsQuery .'" />
								<span id="searchClear" class="input-group-btn"><button class="btn btn-default" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>
							</div>
						</div>';
				
			$lsGrid .= '
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . Translator::i18n('Buscar por:') . '<b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">';
	
			foreach ($this->_aSearchFields as $lsField => $lsLabel)
			{
				$lsGrid .= '
											<li>
												<a class="searchField" href="#" data-field="' . $lsField . '" title="' . Translator::i18n($lsLabel) . '">
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
		
		$lsGrid .= '
			<div class="table-responsive">
				<table class="table table-hover table-condensed">
					<tr class="active">
						<th style="text-align:center;">';
		
						if ($psMark == 'checkbox')
						{
							$lsGrid .= '<a id="changeSearchRowCheck" href="#" title="' . Translator::i18n('Marcar todos') . '" data-inv="' . Translator::i18n('Desmarcar todos') . '"><i class="glyphicon glyphicon-unchecked"></i></a>';
						}
		
		$lsGrid .= '
						</th>';
	
		if (isset($this->_aSearchGridCols) && is_array($this->_aSearchGridCols))
		{
			foreach($this->_aSearchGridCols as $lmField => $lsTitle)
			{
				$lsGrid .= '
						<th>';
					
				if (is_string($lmField))
				{
					if ($laOrder['col'] == $lmField)
					{
						if ($laOrder['ord'] == 'ASC')
						{
							$lsGrid .= '<a class="colOrder" href="#" data-act="' . $lsUrl . '" data-col="' . $lmField . '" data-ord="DESC">' . $lsTitle . '<b class="caret"></b></a>';
						}
						else
						{
							$lsGrid .= '<a class="colOrder dropup" href="#" data-act="' . $lsUrl . '" data-col="' . $lmField . '" data-ord="ASC">' . $lsTitle . '<b class="caret"></b></a>';
						}
					}
					else
					{
						$lsGrid .= '<a class="colOrder" href="#" data-act="' . $lsUrl . '" data-col="' . $lmField . '" data-ord="ASC">' . $lsTitle . '</a>';
					}
				}
				else
				{
					$lsGrid .= $lsTitle;
				}
					
				$lsGrid .= '
						</th>';
			}
		}
	
		$lsGrid .= '
					</tr>';
	
		if (is_array($laResults) && count($laResults) > 0)
		{
			foreach ($laResults as $laRes)
			{
				$lsReturnValue = "";
				$laLabelField = explode("|", $lsLabelField);
				
				if (is_array($laLabelField))
				{
					$lsPipe = "";
					
					foreach ($laLabelField as $lsFieldName)
					{
						if (method_exists($this, 'formatFieldToGrid'))
						{
							$lsContentField = $this->formatFieldToGrid($lsFieldName, $laRes[$lsFieldName]);
						}
						else
						{
							$lsContentField = $laRes[$lsFieldName];
						}
						
						$lsReturnValue .= $lsPipe . $lsContentField;
						$lsPipe = " | ";
					}
				}
				
				$lsGrid .= '
					<tr class="gridRow">
						<td class="onion-grid-col-check" style="text-align:center; width:15px; background-color:#f0f0f0;">
							<input type="' . $psMark . '" name="sck[]" value="' . $laRes['id'] . '" />
							<input type="hidden" name="lb-' . $laRes['id'] . '" value="' . $lsReturnValue . '" />
						</td>';
	
				if (isset($this->_aSearchGridFields) && is_array($this->_aSearchGridFields))
				{
					foreach($this->_aSearchGridFields as $lnKey => $lsField)
					{
						$lsAlign = '';
						$lsWidth = '';
						$lsColor = '';
						$lsContent = '';
						
						if (isset($this->_aSearchGridAlign[$lnKey]) && !empty($this->_aSearchGridAlign[$lnKey]))
						{
							$lsAlign = "text-align:{$this->_aSearchGridAlign[$lnKey]};";
						}
						
						if (isset($this->_aSearchGridWidth[$lnKey]) && !empty($this->_aSearchGridWidth[$lnKey]))
						{
							$lsWidth = " width:{$this->_aSearchGridWidth[$lnKey]};";
						}
						
						if (isset($this->_aSearchGridColor[$lnKey]) && !empty($this->_aSearchGridColor[$lnKey]))
						{
							$lsColor = " background:{$this->_aSearchGridColor[$lnKey]};";
						}
						
						
						if (isset($laRes[$lsField]))
						{
							if (method_exists($this, 'formatFieldToGrid'))
							{
								$lsContent = $this->formatFieldToGrid($lsField, $laRes[$lsField]);
							}
							else
							{
								$lsContent = $laRes[$lsField];
							}
						}
						
						$lsStyle = $lsAlign . $lsWidth . $lsColor;
						
						if (!empty($lsStyle))
						{
							$lsStyle = 'style="' . $lsStyle . '"';
						}
						
						$lsGrid .= '<td class="onion-grid-col-' . $this->_sModule . '-' . $lsField . '" ' . $lsStyle . '>' . $lsContent . '</td>';
					}
				}
	
				$lsGrid .= '
					</tr>';
			}
		}
		else
		{
			$lnCols = count($this->_aSearchGridFields) + 2;
			
			$lsGrid .= '
					<tr>
						<td colspan="' . $lnCols . '" style="text-align:center;">
							<p>
								<strong>' . Translator::i18n('Nenhum registro encontrado!') . '</strong>
							</p>
						</td>
					</tr>';
		}
	
		$lsGrid .= '
				</table>
			</div>';
		
		$lsGrid .= '
			<nav class="navbar navbar-default">
				<div class="container">
					<div class="row">
						<div class="col-md-2">
							<div style="margin-top:26px;">
								&nbsp;&nbsp;&nbsp;' . $lnCurrent . ' - ' . $laData['until'] . ' (' . $laData['total'] . ')
							</div>
						</div>
						<div class="col-md-10">
							' . $loPagination->renderPagination() . '
						</div>
					</div>
				</div>
			</nav>';
			
		$lsGrid .= '
		</div>';
		
		$lsGrid .= '<script type="text/javascript" src="/vendor/m3uzz/onionjs-0.16.4/dist/js/search.js"></script>';
		$lsGrid .= '<script type="text/javascript" src="/vendor/m3uzz/onionjs-0.16.4/dist/js/common.js"></script>';
		$lsGrid .= '<script type="text/javascript" src="/js/backend.js"></script>';
		
		return $lsGrid;
	}
	
	
	/**
	 * 
	 * @return \Onion\Mvc\Controller\unknown
	 */
	public function searchAction ()
	{
		$this->_sResponse = 'json';
	
		$lsFilter = $this->request('filter', null);
		$lsTerm = $this->request('term', null);
		$lsField = $this->request('field', null);
	
		$laResponse = array();
	
		$laFilter = json_decode(base64_decode($lsFilter), true);
	
		$laWhere[] = (isset($laFilter['where']) ? $laFilter['where'] : "");
	
		$loSearch = new Search();
		$loSearch->set('sSearchFields', "a.{$lsField}");
		$laWhere[] = $loSearch->createRLikeQuery('"' . $lsTerm . '"', 'r');
	
		$laParams = array(
			'status'	=> 0,
			'active' 	=> 1,
			'rows'		=> $this->_nSearchGridNumRows,
			'col' 		=> "a.{$lsField}",
			'ord' 		=> 'ASC',
			'q' 		=> $lsTerm,
			'where' 	=> $laWhere,
		);
	
		//Debug::display($laParams);
	
		$laEntity = $this->getEntityManager()
		->getRepository($this->_sEntity)
		->search($laParams);
	
		if (isset($laEntity['resultSet']) && is_array($laEntity['resultSet']))
		{
			foreach ($laEntity['resultSet'] as $laItem)
			{
				$lsReturnValue = "";
				$laSearchLabelField = explode("|", $this->_sSearchLabelField);
				
				if (is_array($laSearchLabelField))
				{
					$lsPipe = "";
						
					foreach ($laSearchLabelField as $lsFieldName)
					{
						if (method_exists($this, 'formatFieldToGrid'))
						{
							$lsContentField = $this->formatFieldToGrid($lsFieldName, $laItem[$lsFieldName]);
						}
						else
						{
							$lsContentField = $laItem[$lsFieldName];
						}
						
						$lsReturnValue .= $lsPipe . $lsContentField;
						$lsPipe = " | ";
					}
				}
				
				$laResponse[] = array('id'=>$laItem['id'], 'value'=>$lsReturnValue);
			}
		}
	
		$loView = new ViewModel();
	
		return $this->setResponseType($loView, Json::encode($laResponse));
	}
	
	
	/**
	 *
	 * @return \Zend\Http\Response
	 */
	public function csvAction ()
	{
		return $this->csv();
	}
		
	
	/**
	 * @param array $paParams
	 * @return \Zend\Http\Response
	 */
	public function csv (array $paParams = null)
	{
		$laParams = array(
			'status'	=> 0,
			'active' 	=> 1,
			'rows'		=> 0,
			'page' 		=> 0,
			'col' 		=> $this->_sGridOrderCol,
			'ord' 		=> $this->_sGridOrder,
			'q' 		=> '',
			'where' 	=> '',
		);
		
		if (is_array($paParams))
		{
			$laParams = $paParams;
		}
	
		$laResult = $this->getEntityManager()->getRepository($this->_sEntityExtended)->getList($laParams);
		//Debug::displayd($laResult);

		$lsFileName = $this->_sRoute . date('Y-m-d') . '.csv';
	
		$lsData = '';
	
		if (is_array($this->_aGridCols))
		{
			$lsComma = '';
	
			foreach ($this->_aGridCols as $lsCol => $lsTitle)
			{
				$lsData .= $lsComma . '"' . $lsTitle . '"';
				$lsComma = ',';
			}
	
			$lsData .= ";\n";
		}
	
		if (is_array($laResult['resultSet']))
		{
			foreach ($laResult['resultSet'] as $lsline => $laValue)
			{
				if (is_array($laValue))
				{
					$lsComma = '';

					foreach ($this->_aGridFields as $lsCol => $lsField)
					{
						$lsData .= $lsComma . '"' . $this->formatFieldToGrid($lsField, $laValue[$lsField]) . '"';
						$lsComma = ',';
					}
	
					$lsData .= ";\n";
				}
			}
		}
	
		header('Content-Description: File Transfer');
		header('Content-Type: text/plan');
		header('Content-Disposition: attachment; filename="' . $lsFileName . '"');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . count($lsData));
	
		echo $lsData;
	
		$loView = new ViewModel();
		$loView->setTerminal(true);
		$loResponse = $this->getResponse();
	
		return $loResponse;
	}
}
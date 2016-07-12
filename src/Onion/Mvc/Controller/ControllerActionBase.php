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
use \Zend\Mvc\Controller as Zend;
use Onion\ORM\EntityManager;
use Onion\Config\Config;
use Onion\Log\Debug;
use Onion\I18n\Translator;
use Onion\View\Model\ViewModel;
use Onion\Json\Json;
use Zend\Session\Container;
use Onion\Lib\Session;


abstract class ControllerActionBase extends Zend\AbstractActionController
{
	protected $_sTable = '';
	
	protected $_sModule = '';
	
	protected $_sRoute = '';
	
	protected $_sGetParam = '';
	
	protected $_sLayout = 'Backend';
	
	protected $_sWindowType = 'default'; // default, popup, modal
	
	protected $_sResponse = 'html'; // html, ajax, json, stream 
	
	protected $_nStreamRetry = 4000;
	
	protected $_bPushMessage = false;
	
	protected $_bHiddenPushMessage = false;
	
	protected $_sEntity = '';
	
	protected $_sEntityExtended = '';
	
	protected $_sForm = '';
	
	protected $_sTitleS = '';
	
	protected $_sTitleP = '';
	
	protected $_aSearchFields = array();
	
	protected $_sSearchFieldDefault = '';
	
	protected $_sGridOrderCol = 'id';
	
	protected $_sGridOrder = 'ASC';
	
	protected $_aGridCols = array('id'=>'Id');
	
	protected $_aGridAlign = array();
	
	protected $_aGridWidth = array();

	protected $_aGridColor = array();
	
	protected $_aGridFields = array('id');
	
	protected $_nGridNumRows = 30;
	
	protected $_bSearch = false;
	
	protected $_aExportTo = array();
	
	protected $_sSearchGridOrderCol = 'id';
	
	protected $_sSearchGridOrder = 'DESC';
	
	protected $_aSearchGridCols = array('id'=>'Id');
	
	protected $_aSearchGridAlign = array();
	
	protected $_aSearchGridWidth = array();
	
	protected $_aSearchGridCollor = array();
	
	protected $_aSearchGridFields = array('id');
	
	protected $_sSearchLabelField = 'id';
	
	protected $_nSearchGridNumRows = 6;	
	
	protected $_bSearchAddButton = true;
	
	protected $_bShowToolbar = true;
	
	protected $_bAddButton = true;
	
	protected $_bView = false;
	
	protected $_bEdit = true;
	
	protected $_bDelete = true;
	
	protected $_bRemove = true;
	
	protected $_aFolders = null;
	
	protected $_aMassActions = null;
	
	protected $_bIndividualButtons = true;
	
	protected $_aIndividualButtons = array(
			/*
			'View' => array(
				'title' => 'Abrir',
				'description' => 'Visualizar',
				'message' => '',
				'icon' => 'glyphicon glyphicon-eye-open',
				'url' => '/#%ROUTER%#/view/',
				'folder' => '',
				'class' => 'rowActBtn'
			),
			*/
			'Edit' => array(
				'title' => 'Editar',
				'description' => 'Editar registro',
				'message' => '',
				'icon' => 'glyphicon glyphicon-edit',
				'url' => '/#%ROUTER%#/edit/',
				'folder' => '',
				'class' => 'rowActBtn',
				'params' => ''
			),
			'Trash' => array(
				'title' => 'Deletar',
				'description' => 'Mover o registro para a lixeira',
				'message' => 'Você tem certeza que deseja mover o registro para a lixeira?',
				'icon' => 'glyphicon glyphicon-trash',
				'url' => '/#%ROUTER%#/move/',
				'folder' => 'index',
				'class' => 'rowActBtn',
				'params' => ''
			),
			'Restore' => array(
				'title' => 'Restaurar',
				'description' => 'Restaurar regisro da lixeira para a entrada',
				'message' => 'Você tem certeza que deseja mover o registro para a entrada?',
				'icon' => 'glyphicon glyphicon-transfer',
				'url' => '/#%ROUTER%#/move/',
				'folder' => 'trash',
				'class' => 'rowActBtn',
				'params' => ''
			),
			'Delete' => array(
				'title' => 'Apagar',
				'description' => 'Apagar permanentemente o registro',
				'message' => 'Você tem certeza que deseja apagar permanentemente o registro?',
				'icon' => 'glyphicon glyphicon-remove',
				'url' => '/#%ROUTER%#/delete/',
				'folder' => 'trash',
				'class' => 'rowActBtn',
				'params' => ''
			),
		);
	
	protected $_bGridCheckbox = true;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $_oEntityManager;
	
	protected $_oRequest = null;
	
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
	
	/**
	 * 
	 * @param unknown $psProperty
	 * @param unknown $pmValue
	 */
	public function set ($psProperty, $pmValue)
	{
		if (property_exists($this, $psProperty))
		{
			$this->$psProperty = $pmValue;
		}
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
	
	/**
	 * 
	 * @param unknown $psProperty
	 */
	public function get ($psProperty)
	{
		if (property_exists($this, $psProperty))
		{
			return $this->$psProperty;
		}
	}
	
	/**
	 * 
	 * @param EntityManager $poEntityManager
	 */
	public function setEntityManager(EntityManager $poEntityManager)
	{
		$this->_oEntityManager = $poEntityManager;
	}
		
	/**
	 * 
	 * @return \Onion\Mvc\Controller\Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		if (null === $this->_oEntityManager)
		{
			$this->_oEntityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
	
		return $this->_oEntityManager;
	}
	
	/**
	 * 
	 */
	public function getServiceLocator ()
	{
		return $this->serviceLocator;
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	public function entityFlush ()
	{
		try
		{
			$this->getEntityManager()->flush();
			
			return true;
		}
		catch (\Exception $e)
		{
			$laErrorInfo = $e->getPrevious()->errorInfo;
			switch ($laErrorInfo['0'])
			{
				case 23000:
					$lsMessage = sprintf(Translator::i18n('O cadastro não pode ser completado. Já existe um registro com uma chave única. <br/> Erro: [%s] %s'), $laErrorInfo['0'], $laErrorInfo['2']);
				break;
				default:
					$lsMessage = $e->getMessage();
			}
			
			$this->flashMessenger()->addMessage(array(
				'id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'),
				'type' => 'warning',
				'msg' => $lsMessage
			));
			
			return false;
		}
	}
	
	/**
	 * 
	 */
	public function getZendRequest ()
	{
		if (null === $this->_oRequest)
		{
			$this->_oRequest = $this->getRequest();
		}
	}
	
	/**
	 * 
	 * @param string $psVar
	 * @param string $pmDefault
	 */
	public function requestPost ($psVar = null, $pmDefault = null)
	{
		$this->getZendRequest();

		return $this->_oRequest->getPost($psVar, $pmDefault);
	}

	/**
	 * 
	 * @param string $psVar
	 * @param string $pmDefault
	 */
	public function requestQuery ($psVar = null, $pmDefault = null)
	{
		$this->getZendRequest();

		return $this->_oRequest->getQuery($psVar, $pmDefault);
	}

	/**
	 * 
	 * @param string $psVar
	 * @param string $pmDefault
	 */
	public function requestFiles ($psVar = null, $pmDefault = null)
	{
		$this->getZendRequest();
	
		return $this->_oRequest->getFiles($psVar, $pmDefault);
	}
		
	/**
	 * 
	 */
	public function requestIsPost ()
	{
		$this->getZendRequest();
	
		return $this->_oRequest->isPost();
	}
		
	/**
	 * 
	 * @param string $psVar
	 * @param string $pmDefault
	 * @param string $psFirst
	 */
	public function request ($psVar, $pmDefault = null, $psFirst = 'post')
	{
		$this->getZendRequest();
		
		if ($psFirst === 'post')
		{
			return $this->_oRequest->getPost($psVar, $this->_oRequest->getQuery($psVar, $pmDefault));
		}
		else 
		{
			return $this->_oRequest->getQuery($psVar, $this->_oRequest->getPost($psVar, $pmDefault));
		}
	}
	
	
	/**
	 *
	 * @param string $psFirst
	 */
	public function requestAll ($psFirst = 'post')
	{
		$this->getZendRequest();
	
		$laPost = $this->_oRequest->getPost()->toArray();
		$laGet = $this->_oRequest->getQuery()->toArray();
		
		if ($psFirst === 'post')
		{
			$laRequest = array_merge($laGet, $laPost);	
		}
		else
		{
			$laRequest = array_merge($laPost, $laGet);
		}
		
		return $laRequest;
	}
	

	/**
	 * 
	 */
	public function isXmlHttpRequest ()
	{
		$this->getZendRequest();
		
		return $this->_oRequest->isXmlHttpRequest();
	}
	
	/**
	 * 
	 * @param string $pbReturnObject
	 * @return object|string|null
	 */
	public function getAuthenticatedUser ($pbReturnObject = false)
	{
	    $loSession = new Session();
		$loUser = $loSession->getRegister('OnionAuth');
		//$loAuthService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
		
		//if ($loAuthService->hasIdentity())
		if (is_object($loUser))
		{
			//$loUser = $loAuthService->getIdentity();
			
			if ($pbReturnObject)
			{
				return $loUser;
			}
			else 
			{
				return $loUser->get('id');
			}
		}
		else
		{
			$this->flashMessenger()->addMessage(array(
				'id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'),
				'type' => 'danger',
				'msg' => Translator::i18n('You need to be logged to execute this action!')
			));			
		}
		
		return null;
	}
	
	/**
	 * 
	 * @param object $poView
	 * @param string $psResponse
	 * @param string $pbRenderView
	 * @return object
	 */
	public function setResponseType ($poView, $psResponse = null, $pbRenderView = false)
	{
		if ($this->isXmlHttpRequest() || $this->_sResponse != 'html')
		{
			$poView->setTerminal(true); // desabilita o layout
			$loResponse = $this->getResponse();
			$loResponse->setStatusCode(200);

			if ($this->_sResponse == 'json')
			{
				$loResponse->getHeaders()->addHeaderLine('Content-Type', 'application/json');
			}
			elseif ($this->_sResponse == 'stream')
			{
				$loResponse->getHeaders()->addHeaderLine('Content-Type', 'text/event-stream');
				$psResponse = "data: {$psResponse}\n";
				$psResponse .= "retry: {$this->_nStreamRetry}\n\n";
			}
			
			if ($psResponse !== null)
			{
				$loResponse->setContent($psResponse);
			}
			elseif ($pbRenderView)
			{
				return $poView;
			}
			
			return $loResponse;
		}
		else
		{
			$laOnionOptions = \Onion\Config\Config::getAppOptions();
			$laModuleLayouts = $laOnionOptions['layout']['module_layouts'];
			
			if ($this->_sWindowType == 'popup')
			{
				$this->layout($laModuleLayouts['Popup']);
			}
			elseif ($this->_sWindowType == 'modal')
			{
				$this->layout($laModuleLayouts['Container']);
			}
			else
			{
				$this->layout($laModuleLayouts[$this->_sLayout]);
			}
		}
		
		return $poView;
	}
		
	/**
	 * 
	 * @param string $psArea
	 * @param string $psHookAction
	 * @param array $paParams
	 * @return boolean
	 */
	public function hook($psArea, $psHookAction = null, array $paParams = null)
	{
		$laHooks = Config::getAppOptions('hooks');
	
		if ($psHookAction !== null)
		{
			return $this->getHook($laHooks, $psArea, $psHookAction, $paParams);
		}
		else
		{
			if (isset($laHooks[$psArea]) && is_array($laHooks[$psArea]))
			{
				foreach ($laHooks[$psArea] as $lsHookAction => $laOptions)
				{
					echo $this->getHook($laHooks, $psArea, $lsHookAction);
				}
			}
		}
	}
	
	/**
	 * 
	 * @param array $paHooks
	 * @param string $psArea
	 * @param string $psHookAction
	 * @param array $paParams
	 * @throws \Exception
	 * @return boolean
	 */
	public function getHook($paHooks, $psArea, $psHookAction, array $paParams = null)
	{
		if (isset($paHooks[$psArea][$psHookAction]['controller']))
		{
			$lsController = $paHooks[$psArea][$psHookAction]['controller'];
			$laParams = array();
				
			if ($paParams !== null)
			{
				$laParams = $paParams;
			}
			elseif (isset($paHooks[$psArea][$psHookAction]['params']))
			{
				$laParams = $paHooks[$psArea][$psHookAction]['params'];
			}
				
			$laParams['action'] = $psHookAction;

			$lsTemplate = $this->layout()->getTemplate();
			$loResult = $this->forward()->dispatch($lsController, $laParams);				
			$this->layout($lsTemplate);
				
			$loViewManager = $this->getServiceLocator()->get('ViewManager');
			return $loViewManager->getRenderer()->render($loResult);
		}
		else
		{
			throw new \Exception("Hook method ($psHookAction) is not registred for the ($psArea) area!");
		}
	
		return false;
	}
	
	/**
	 * 
	 * @param object $poOriginalEntity
	 * @param object $poChangedEntity
	 * @return array|NULL
	 */
	public function checkChangeEntity($poOriginalEntity, $poChangedEntity)
	{
		$laOriginalEntity = null;
		$laChangedEntity = null;
		$laChanged = null;
		
		if (is_object($poOriginalEntity))
		{
			$laOriginalEntity = $poOriginalEntity->getArrayCopy();
		}
		
		if (is_object($poChangedEntity))
		{
			$laChangedEntity = $poChangedEntity->getArrayCopy();
		}
		
		if (is_array($laOriginalEntity) && is_array($laChangedEntity))
		{
			foreach ($laOriginalEntity as $lsKey => $lsValue)
			{
				if ($laChangedEntity[$lsKey] != $lsValue)
				{
					$laChanged[$lsKey]['from'] = $lsValue;
					$laChanged[$lsKey]['to'] = $laChangedEntity[$lsKey];
				}
			}
		}
		
		return $laChanged;
	}
	
	/**
	 * 
	 * @return \Onion\Mvc\Controller\unknown
	 */
	public function testMessageAction ()
	{
		$this->set('_sResponse', 'ajax');
		$this->set('_bHiddenPushMessage', false);
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>true, 'type'=>'danger', 'msg'=>'aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa'));
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>false, 'type'=>'warning', 'msg'=>'bbbbbbbbbbbbbbbbbbbbbb'));
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>true, 'type'=>'success', 'msg'=>'cccccccccccccccccccccc'));
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>false, 'type'=>'primary', 'msg'=>'dddddddddddddddddddddd'));
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>false, 'type'=>'info', 'msg'=>'eeeeeeeeeeeeeeeeeeeeee'));
		$this->flashMessenger()->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'default', 'msg'=>'ffffffffffffffffffffff'));
	
		$loView = new ViewModel();
		return $this->setResponseType($loView, 'ok');
	}
	
	/**
	 * 
	 * @return \Onion\Mvc\Controller\unknown
	 */
	public function pushAction ()
	{
		$this->set('_sResponse', 'stream');
		$laMessages = '';
		
		if (PUSH_MESSAGE && isset($_SESSION['FlashMessenger']))
		{
			$loContainer = $_SESSION['FlashMessenger'];
			
			if (is_object($loContainer))
			{
				if ($loContainer->offsetExists('default'))
				{
					$loNamespace = $loContainer->offsetget('default');
					
					if (!$loNamespace->isEmpty())
					{						
						$laMsgs = array_reverse($loNamespace->toArray(), true);
						
						foreach ($laMsgs as $lnKey => $laMessage)
						{
							if ($laMessage['push'])
							{
								$laMessages[$lnKey] = $laMessage;
								
								if ($loNamespace->offsetExists($lnKey))
								{
									$loNamespace->offsetUnset($lnKey);
								}
							}
						}
					}
				}
			}
		}

		$loView = new ViewModel();
		$lsMessages = Json::encode($laMessages);
		return $this->setResponseType($loView, $lsMessages);
	}

	/**
	 * 
	 * @return \Onion\Mvc\Controller\unknown
	 */
	public function clearFlashAction ()
	{
		$this->set('_sResponse', 'ajax');
		$lnId = $this->request('id', null);
		
		$this->flashMessenger()->clearCurrentMessages();
		
		$loView = new ViewModel();
		return $this->setResponseType($loView, $laMessages);
	}
}
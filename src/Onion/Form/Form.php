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

namespace Onion\Form;
use Traversable;
use \Zend\Form as Zend;
use \Zend\Stdlib\ArrayUtils;
use Onion\Config\Config;
use Onion\Log\Debug;
use Onion\Lib\Util;
use Onion\I18n\Translator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Onion\Form\Render as Render;
	
class Form extends Zend\Form implements ObjectManagerAwareInterface
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $_oEntityManager;
	
	protected $_sActionType;
	
	protected $_sEntity;
	
	protected $_nRecordId;
	
	protected $_oEntityData;
	
	protected $_sModule;
	
	protected $_sForm;
		
	protected $_sWindowType = 'default'; // default, modal, popup
	
	protected $_sRequestType = 'post'; // post, get, ajax
	
	protected $_sResponseType = 'html'; // html, ajax, none
	
	protected $_sCancelBtnType = 'cancel'; // cancel, close, none
	
	protected $_bShowCollapseBtn = false;
	
	protected $_sHelp = "";
	
	protected $_bCollapsed = false;
	
	protected $_aFieldSet = null;
	
	protected $_bformHTML5Validate = true;
	
	protected $_nColLength = 6;
	
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
	 * @param string $psProperty
	 * @param mixed $pmValue
	 */
	public function set ($psProperty, $pmValue)
	{
		if (property_exists($this, $psProperty))
		{
			$this->$psProperty = $pmValue;
		}
	}
		
	
	/**
	 *
	 * @param string $psProperty
	 * @return mixed | null
	 */
	public function getFormProperty ($psProperty)
	{
		if (property_exists($this, $psProperty))
		{
			return $this->$psProperty;
		}
		else
		{
			$lsProperty = "_{$psProperty}";
			
			if (property_exists($this, $lsProperty))
			{
				return $this->$lsProperty;
			}
		}
		
		return null;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::setObjectManager()
	 */
	public function setObjectManager (ObjectManager $poObjectManager)
	{
		$this->_oEntityManager = $poObjectManager;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see \DoctrineModule\Persistence\ObjectManagerAwareInterface::getObjectManager()
	 */
	public function getObjectManager ()
	{
		return $this->_oEntityManager;
	}

	
	/**
	 * 
	 * @param string $psType
	 */
	public function setActionType ($psType)
	{
		$this->_sActionType = $psType;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getActionType ()
	{
		return $this->_sActionType;
	}
	
	
	/**
	 * 
	 * @param string $psEntity
	 */
	public function setEntity ($psEntity)
	{
		$this->_sEntity = $psEntity;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getEntity ()
	{
		return $this->_sEntity;
	}
	
	
	/**
	 * 
	 * @param int $pnId
	 */
	public function setRecordId ($pnId)
	{
		$this->_nRecordId = $pnId;
	}
	
	
	/**
	 * 
	 * @return int
	 */
	public function getRecordId ()
	{
		return $this->_nRecordId;
	}
	
	
	/**
	 * 
	 * @param obejct $poData
	 */
	public function setEntityData ($poData)
	{
		$this->_oEntityData = $poData;
	}
	
	
	/**
	 * 
	 */
	public function getEntityData ()
	{
		return $this->_oEntityData;
	}
	
	
	/**
	 * 
	 * @param string $psData
	 */
	public function setWindowType ($psData)
	{
		$this->_sWindowType = $psData;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getWindowType ()
	{
		return $this->_sWindowType;
	}
	
	
	/**
	 * 
	 * @param string $psData
	 */
	public function setRequestType ($psData)
	{
		$this->_sRequestType = $psData;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getRequestType ()
	{
		return $this->_sRequestType;
	}	

	
	/**
	 * 
	 * @param string $psData
	 */
	public function setResponseType ($psData)
	{
		$this->_sResponseType = $psData;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getResponseType ()
	{
		return $this->_sResponseType;
	}
		
	
	/**
	 * 
	 * @param string $psData
	 */
	public function setCancelBtnType ($psData)
	{
		$this->_sCancelBtnType = $psData;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getCancelBtnType ()
	{
		return $this->_sCancelBtnType;
	}	
	
	
	/**
	 * 
	 * @param boolean $pbData
	 */
	public function setShowCollapseBtn ($pbData)
	{
		$this->_bShowCollapseBtn = $pbData;
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	public function getShowCollapseBtn ()
	{
		return $this->_bShowCollapseBtn;
	}
	
	
	/**
	 * 
	 * @param boolean $pbData
	 */
	public function setCollapsed ($pbData)
	{
		$this->_bCollapsed = $pbData;
	}
	
	
	/**
	 * 
	 * @param array $paData
	 */
	public function setFieldSet ($paData)
	{
		$this->_aFieldSet = $paData;	
	}
	
	
	/**
	 * 
	 * @return array
	 */
	public function getFieldSet ()
	{
		return $this->_aFieldSet;	
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function getCollapsed ()
	{
		return $this->_bCollapsed;
	}
	
	
	/**
	 *
	 * @return string
	 */
	public function getFormHTML5Validate ()
	{
		return $this->_bformHTML5Validate;
	}
	
	/**
	 * 
	 * @param string $psModule
	 * @return array
	 */
	public function getFormOptions ($psModule)
	{
		$laFormOptions = Config::getAppOptions('form');
		
		if (isset($laFormOptions[$psModule]))
		{
			return $laFormOptions[$psModule];
		}
		
		return null;
	}

	
	/**
	 * 
	 * @param string $psModule
	 */
	public function setFormOptions ($psModule)
	{}


	/**
	 * 
	 * @param object $paData
	 */
	public function setData ($poData)
	{
		if ($poData instanceof Traversable)
		{
			$laData = ArrayUtils::iteratorToArray($poData);
		}
		else 
		{
			$laData = $poData;
		}
		
		if (is_array($laData))
		{
			foreach ($laData as $lsProperty => $lmValue)
			{
				if (substr($lsProperty, 0, 2) === 'dt')
				{
					$lmValue = Translator::dateP2S($lmValue);
					$laData[$lsProperty] = trim($lmValue);
				}
			}
		}
		
		parent::setData($laData);
	}
	
	
	public function getDataForm ()
	{
		return $this->data;
	}
	
	
	/**
	 * 
	 */
	public function clientSets ()
	{
		$laFormClientConfig = $this->getFormOptions($this->_sModule);
		
		if (is_array($laFormClientConfig) && (isset($laFormClientConfig[$this->_sModule]) && isset($laFormClientConfig[$this->_sForm]) && is_array($laFormClientConfig[$this->_sForm])))
		{
			foreach ($laFormClientConfig[$this->_sForm] as $lsField => $laPropertie)
			{
				if (isset($laPropertie['disable']) && Util::toBoolean($laPropertie['disable']))
				{
					$this->remove($lsField);
				}
				else
				{
					unset($laPropertie['disable']);
					
					$loElement = $this->get($lsField);
					
					if (is_object($loElement) && is_array($laPropertie))
					{
						foreach ($laPropertie as $lsProp => $lmValue)
						{
							if ($lsProp == 'value')
							{
								$loElement->setValue($lmValue);
							}
							elseif ($lsProp == 'label')
							{
								$loElement->setLabel($lmValue);
							}
							elseif ($lsProp == 'empty_option')
							{
								$loElement->setEmptyOption($lmValue);
							}
							elseif ($lsProp == 'value_options')
							{
								$loElement->setValueOptions($lmValue);
							}
								
							
							if ($loElement->hasAttribute($lsProp) != null)
							{
								$loElement->setAttribute($lsProp, $lmValue);
							}
							elseif ($loElement->getOption($lsProp) != null)
							{
								$loElement->setOption($lsProp, $lmValue);
							}
						}
					}
				}
			}
		}
	}

	
	/**
	 * 
	 * @param object $poView
	 * @return string
	 */
	public function renderFieldSet ($poView)
	{
		if (is_array($this->_aFieldSet))
		{
			$laElements = $this->getElements();
			$lsMsgError = "";
			
			if (is_array($laElements))
			{
				$lsError = '';
				
				foreach ($laElements as $loElement)
				{
					$laMessages = $loElement->getMessages();
						
					if (is_array($laMessages))
					{
						foreach ($laMessages as $lsMsg)
						{
							$lsError .= "<li>$lsMsg</li>";
						}
					}
				}
					
				if (!empty($lsError))
				{
					$lsMsgError .= '<div class="alert alert-warning alert-dismissable">';
					$lsMsgError .= '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
					$lsMsgError .= '	<ul>' . $lsError . '</ul>';
					$lsMsgError .= '</div>';
				}
			}
			
			$lsActive = ' active';
			$lsTab = '	<ul class="nav nav-tabs" role="tablist">';
			$lsContent = '	<div class="tab-content">';
			
			foreach ($this->_aFieldSet as $lsFieldSetId => $laFieldSet)
			{
				$lnCount = 0;
				
				if (is_array($laFieldSet['fields']))
				{
					foreach ($laFieldSet['fields'] as $lsFieldId)
					{
						if ($this->has($lsFieldId))
						{
							$lnCount++;
						}
					}
				}
				
				if ($lnCount > 0)
				{
				$lsClass = isset($laFieldSet['class']) ? $laFieldSet['class'] : '';
				$lsLabel = isset($laFieldSet['label']) ? $laFieldSet['label'] : '';
				$lsIcon = '';
				
				if (isset($laFieldSet['icon']))
				{
					$lsIcon = '<i class="' . $laFieldSet['icon'] . '"></i> ';
				}
	
				$lsTab .= '		<li role="presentation" class="' . $lsActive . '">';
				$lsTab .= '			<a id="tab-' . $lsFieldSetId . '" href="#' . $lsFieldSetId . '" aria-controls="' . $lsFieldSetId . '" role="tab" data-toggle="tab">' . $lsIcon . $lsLabel . '</a>';
				$lsTab .= '		</li>';
				
				$lsContent .= '		<div role="tabpanel" class="tab-pane ' . $lsClass . $lsActive . '" id="' . $lsFieldSetId . '">';
				$lsContent .= $this->renderField($poView, $laFieldSet['fields']);
				$lsContent .= '		</div>';
				
				$lsActive = '';
				}
			}
			
			$lsTab .= '	</ul>';
			$lsContent .= '	</div>';
			
			$lsTabPanel = $lsMsgError;
			$lsTabPanel .= '<div role="tabpanel">';
			$lsTabPanel .= $lsTab;
			$lsTabPanel .= $lsContent;
			$lsTabPanel .= '</div>';

			return $lsTabPanel;
		}
		else 
		{
			return $this->renderField($poView);
		}
	}
	
	
	/**
	 * 
	 * @param object $poView
	 * @param array $paFields
	 * @return string
	 */
	public function renderField ($poView, $paFields = null)
	{
		$laElements = null;
		
		if (is_array($paFields))
		{
			foreach ($paFields as $lsFieldId)
			{
				if ($this->has($lsFieldId))
				{
					$laElements[] = $this->get($lsFieldId);
				} 
				elseif ("-" === $lsFieldId)
				{
					$laElements[] = $lsFieldId;
				}				
			}
		}
		else 
		{
			$laElements = $this->getElements();
		}
		
		$lsInput = '';
		
		if (is_array($laElements))
		{
			foreach($laElements as $lnElement => $loElement)
			{
				if (is_object($loElement))
				{ 
					$laMessages = $loElement->getMessages();
							
					if (is_array($laMessages))
					{
						foreach ($laMessages as $lsMsg)
						{
							$poView->flashMessenger()->addMessage(array(
								'id'=>$this->_sModule . '-' . microtime(true), 
								'hidden'=>false, 
								'push'=>false, 
								'type'=>'danger', 
								'msg'=>$lsMsg
							));
						}
					}				
					
					if ($loElement->getAttribute('type') == 'password')
					{
						$loElement->setValue(null);
					}
					
					switch ($loElement->getName())
					{
						case "security":
						case "id":
							$lsInput .= $poView->formRow($loElement);
							break;
						case 'submit':
							break;
						default:
							switch ($loElement->getOption('data-type'))
							{
								case "openModalBtn":
									$loField = new Render\OpenModalBtn($loElement);
									$lsInput .= $loField->render();
									break;
								case "openPopUpBtn":
									$loField = new Render\OpenPopUpBtn($loElement);
									$lsInput .= $loField->render();
									break;
								case "fieldActionBtn":					
									$loField = new Render\FieldActionBtn($loElement);
									$lsInput .= $loField->render();
									break;
								case "actionBtn":
									$loField = new Render\ActionBtn($loElement);
									$lsInput .= $loField->render();
									break;
								case "table":
									$loField = new Render\Table($loElement);
									$lsInput .= $loField->render();
									break;
								case "searchField":
									$loField = new Render\SearchField($loElement);
									$lsInput .= $loField->render();
									break;
								case "datePicker":
									$loField = new Render\DatePicker($loElement);
									$lsInput .= $loField->render();
									break;
								case "timePicker":
									$loField = new Render\TimePicker($loElement);
									$lsInput .= $loField->render();
									break;
								case "dateTimePicker":
									$loField = new Render\DateTimePicker($loElement);
									$lsInput .= $loField->render();
									break;
								case "display":
									$loField = new Render\Display($loElement);
									$lsInput .= $loField->render($poView);
									break;
								case "text":
									$loField = new Render\Text($loElement);
									$lsInput .= $loField->render($poView);
									break;
								case "ckEditor":
									$loField = new Render\CkEditor($loElement);
									$lsInput .= $loField->render($poView);
									break;
								case "ajaxUpload":
									$loField = new Render\AjaxUpload($loElement);
									$lsInput .= $loField->render($poView);
									break;
								default:
									$loField = new Render\DefaultField($loElement);
									$lsInput .= $loField->render($poView);
							}
					}
				}
				elseif ($loElement === '-')
				{
					$lsInput .= '<div class="col-lg-12"><hr/></div>';
				}
			}
		}
		
		return $lsInput;
	}
	
	
	/**
	 * 
	 * @param object $poView
	 * @param string $psRoute
	 * @param string $psTitle
	 * @param string $psBackTo
	 */
	public function addForm ($poView, $psRoute, $psTitle, $psBackTo = '')
	{
		$psTitle = 'Adicionar ' . strtolower($psTitle);
		
		echo $this->renderForm($poView, $psRoute, $psTitle, $psBackTo, 'add');
	}
	
	
	/**
	 * 
	 * @param object $poView
	 * @param int $pnId
	 * @param string $psRoute
	 * @param string $psTitle
	 * @param string $psBackTo
	 */
	public function editForm ($poView, $pnId, $psRoute, $psTitle, $psBackTo = '')
	{
		$psTitle = 'Editar ' . strtolower($psTitle);
	
		echo $this->renderForm($poView, $psRoute, $psTitle, $psBackTo, 'edit', $pnId);
	}	

	
	/**
	 * 
	 * @param object $poView
	 * @param string $psRoute
	 * @param string $psTitle
	 * @param string $psBackTo
	 * @param string $psAction
	 * @param int $pnId
	 * @return string
	 */
	public function renderForm ($poView, $psRoute, $psTitle, $psBackTo = '', $psAction = '', $pnId = null)
	{
		$poView->headTitle(strtolower($psTitle));
	
		$this->setAttribute('action', $poView->url($psRoute, array(
			'action' => $psAction,
			'id' => $pnId
		)));

		if (!$this->_bformHTML5Validate)
		{
			$this->setAttribute('novalidate', 'novalidate');
		}
		
		$this->prepare();
	
		$poView->headLink()->appendStylesheet($poView->basePath('/vendor/onion/css/form.css'));
	
		$lsForm = $this->getFlashMessage($poView);
	
		$lsForm .= $poView->form()->openTag($this);
	
		$lsForm .= '
			<div class="panel panel-default">
				<div class="panel-heading">';
		
		$lsCollapseIcon = 'up';
		$lsCollapse = 'in';
		
		if ($this->getShowCollapseBtn())
		{
			if ($this->getCollapsed())
			{
				$lsCollapseIcon = 'down';
				$lsCollapse = 'collapse';
			}
			
			$lsForm .= '	
					<div class="row">
						<div class="col-md-11">
							<a title="' . Translator::i18n('Clique para abrir opções de filtro') . '" data-toggle="collapse" href="#collapseFormBody-' . $psRoute . '" aria-expanded="false" aria-controls="collapseFormBody-' . $psRoute . '">
								<h4>' . $poView->escapeHtml($psTitle). '</h4>
							</a>
						</div>
						<div class="col-md-1">
							<a title="' . Translator::i18n('Clique para abrir opções de filtro') . '" data-toggle="collapse" href="#collapseFormBody-' . $psRoute . '" aria-expanded="false" aria-controls="collapseFormBody-' . $psRoute . '" class="btn btn-link pull-right hidden-print"><i class="glyphicon glyphicon-collapse-' . $lsCollapseIcon . '"></i></a>
						</div>
					</div>';
		}
		else 
		{
			$lsForm .= '<h4>' . $poView->escapeHtml($psTitle). '</h4>';
		}
		
		$lsForm .= '
				</div>';
				
		if ($this->getShowCollapseBtn())
		{
			$lsForm .= '<div class="' . $lsCollapse . '" id="collapseFormBody-' . $psRoute . '">';
		}
		
		$lsForm .= '
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<input id="back" type="hidden" name="back" value="' . $psBackTo . '">
							<input id="w" type="hidden" name="w" value="' . $this->_sWindowType . '">
							' . $this->renderFieldSet($poView) . '
						</div>
					</div>
				</div>
				<div class="panel-footer">';
	
		$loField = new Render\Submit($this->get('submit'));
		$lsForm .= $loField->render();
		
	
		if ($this->getCancelBtnType() === 'cancel')
		{
			$lsForm .= ' <a class="formCancelBtn" href="/' . $psRoute . '/' . $psBackTo . '" >' . Translator::i18n('Cancelar') . '</a>';
		}
		elseif ($this->getCancelBtnType() === 'close')
		{
			if ($this->getWindowType() === 'popup')
			{
				$lsForm .= ' <a class="closeWindowBtn" href="javascript:window.close();" >' . Translator::i18n('Fechar') . '</a>';
			}
			else
			{
				$lsForm .= ' <a class="closeModalBtn" data-dismiss="modal" aria-hidden="true" href="#">' . Translator::i18n('Fechar') . '</a>';
			}
		}
	
		$lsForm .= '
				</div>';
		
		if ($this->getShowCollapseBtn())
		{
			$lsForm .= '</div>';
		}
		
		$lsForm .= '
			</div>';
	
		$lsForm .= $poView->form()->closeTag();
	
		$lsForm .= $this->renderSeachModal();
	
		$poView->inlineScript()->prependFile($poView->basePath('/vendor/onion/js/form.js'));
		
		return $lsForm;
	}
			
	
	/**
	 * 
	 * @param object $poView
	 * @return string
	 */
	public function getFlashMessage ($poView)
	{
		$lsEchoMessage = '';
		
		$laMessages = $poView->flashMessenger()->getCurrentMessages();
		
		if (is_array($laMessages))
		{
			foreach ($laMessages as $lnKey => $laMessage)
			{
				$lsEchoMessage .= '
					<div class="alert alert-' . $laMessage['type'] . ' alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						' . $laMessage['msg'] . '
					</div>';
			}
		}
		
		return $lsEchoMessage;
	}
	
	
	/**
	 * 
	 * @param object $poView
	 * @param int $pnId
	 * @param string $psRoute
	 * @param string $psTitle
	 * @param string $psBackTo
	 * @param string $psAction
	 */
	public function changePasswordForm ($poView, $pnId, $psRoute, $psTitle, $psBackTo = '', $psAction = 'change-password')
	{
		$poView->headTitle('Change Password ' . strtolower($psTitle));
	
		$this->setAttribute('data-act', $poView->url($psRoute, array('action' => $psAction)));
		$this->setAttribute('action', '#');
		$this->setAttribute('data-request', 'AJAX');
		$this->setAttribute('data-container', '#ChangePasswordContainer');
		
		if (!$this->_bformHTML5Validate)
		{
			$this->setAttribute('novalidate', 'novalidate');
		}
		
		$this->prepare();
	
		$lsForm = '
			<div id="ChangePasswordContainer" >
				<div class="row">';
				
		$poView->headLink()->appendStylesheet($poView->basePath('/vendor/onion/css/form.css'));
		
		$lsForm .= $this->getFlashMessage($poView);
			
		$lsForm .= $poView->form()->openTag($this);
		
		$lsForm .= '
					<input type="hidden" name="back" value="' . $psBackTo . '">
					<input type="hidden" name="w" value="' . $this->_sWindowType . '">';
				
		$lsForm .= $this->renderFieldSet($poView);
		
		$lsForm .= $poView->form()->closeTag();
		
		$poView->inlineScript()->prependFile($poView->basePath('/vendor/onion/js/form.js'));

		$lsForm .= '
				</div>
			</div>';
		
		echo $lsForm;
	}
	
	
	/**
	 * 
	 * @param object $poView
	 */
	public function message ($poView)
	{		
		$lsEcho = '
			<div class="panel panel-default">
				<div class="panel-body">';
		
		$lsEcho .= $this->getFlashMessage($poView);
		
		$lsEcho .= '
				</div>
				<div class="panel-footer">';
		
		if ($this->getCancelBtnType() === 'cancel')
		{
			//$lsEcho .= ' <a class="btn btn-default formCancelBtn" href="/' . $psRoute . '/' . $psBackTo . '" >' . Translator::i18n('Cancelar') . '</a>';
		}
		elseif ($this->getCancelBtnType() === 'close')
		{
			if ($this->getWindowType() === 'popup')
			{
				$lsEcho .= ' <a class="btn btn-default closeWindowBtn" href="javascript:window.close();" >' . Translator::i18n('Fechar') . '</a>';
			}
			else
			{
				$lsEcho .= ' <a class="btn btn-default closeModalBtn" data-dismiss="modal" aria-hidden="true" href="#">' . Translator::i18n('Fechar') . '</a>';
			}
		}		

		$lsEcho .= '
				</div>
			</div>';
		
		echo $lsEcho;
	}
	
	
	/**
	 * 
	 * @param object $poView
	 * @param string $psRoute
	 * @param string $psTitle
	 * @param string $psBackTo
	 * @param string $pbSubmitBtn
	 * @return string
	 */
	public function renderGridFilterForm ($poView, $psRoute, $psTitle, $psBackTo = '', $pbSubmitBtn = false)
	{
		$poView->headTitle(strtolower($psTitle));
	
		$this->prepare();
	
		$poView->headLink()->appendStylesheet($poView->basePath('/vendor/onion/css/form.css'));
	
		$lsForm = $this->getFlashMessage($poView);
	
		$lsForm .= '
			<div class="panel panel-default">
				<div class="panel-heading">';
	
		$lsCollapseIcon = 'up';
		$lsCollapse = 'in';
		$lsDivId = preg_replace("/ /", "-", trim($psTitle));
	
		if ($this->getShowCollapseBtn())
		{
			if ($this->getCollapsed())
			{
				$lsCollapseIcon = 'down';
				$lsCollapse = 'collapse';
			}
				
			$lsForm .= '
					<div class="row">
						<div class="col-md-11">
							<a title="' . Translator::i18n('Clique para abrir') . '" data-toggle="collapse" href="#collapseFormBody-' . $lsDivId . '" aria-expanded="false" aria-controls="collapseFormBody-' . $lsDivId . '">
								<h4>' . $poView->escapeHtml($psTitle). '</h4>
							</a>
						</div>
						<div class="col-md-1">
							<a title="' . Translator::i18n('Clique para abrir') . '" data-toggle="collapse" href="#collapseFormBody-' . $lsDivId . '" aria-expanded="false" aria-controls="collapseFormBody-' . $lsDivId . '" class="btn btn-link pull-right hidden-print"><i class="glyphicon glyphicon-collapse-' . $lsCollapseIcon . '"></i></a>
						</div>
					</div>';
		}
		else
		{
			$lsForm .= '<h4>' . $poView->escapeHtml($psTitle). '</h4>';
		}
	
		$lsForm .= '
				</div>';
	
		if ($this->getShowCollapseBtn())
		{
			$lsForm .= '<div class="' . $lsCollapse . '" id="collapseFormBody-' . $lsDivId . '">';
		}
	
		$lsForm .= '
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-12">
							<input id="w" type="hidden" name="w" value="' . $this->_sWindowType . '">
							' . $this->renderFieldSet($poView) . '
						</div>
					</div>
				</div>
				<div class="panel-footer">';
	
		if ($pbSubmitBtn)
		{
			if ($this->has('submit'))
			{
				$loField = new Render\Submit($this->get('submit'));
				$lsForm .= $loField->render();
			}
		}
		else 
		{
			$lsForm .= ' <a class="btn btn-primary formSubmitBtn" href="#" data-act="/' . $psRoute . '/' . $psBackTo . '">' . Translator::i18n('Filtrar') . '</a>';
		}
	
		if ($this->getCancelBtnType() === 'cancel')
		{
			$lsForm .= ' <a class="formCancelBtn" href="/' . $psRoute . '/' . $psBackTo . '" >' . Translator::i18n('Cancelar') . '</a>';
		}
		elseif ($this->getCancelBtnType() === 'close')
		{
			if ($this->getWindowType() === 'popup')
			{
				$lsForm .= ' <a class="closeWindowBtn" href="javascript:window.close();" >' . Translator::i18n('Fechar') . '</a>';
			}
			else
			{
				$lsForm .= ' <a class="closeModalBtn" data-dismiss="modal" aria-hidden="true" href="#">' . Translator::i18n('Fechar') . '</a>';
			}
		}
	
		$lsForm .= '
				</div>';
	
		if ($this->getShowCollapseBtn())
		{
			$lsForm .= '</div>';
		}
	
		$lsForm .= '
			</div>';
	
		$poView->inlineScript()->prependFile($poView->basePath('/vendor/onion/js/form.js'));
	
		return $lsForm;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function renderSeachModal ()
	{
		$lsForm = '
			<div id="ajaxFormModal" class="modal fade bs-example-modal-lg hidden-print" tabindex="-1" role="dialog" aria-labelledby="ajaxFormModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="ajaxFormModalLabel"></h4>
			      		</div>
			      		<div class="modal-body">
			      		</div>
			      		<div class="modal-footer">
			        		<button id="ajaxFormModalCancelBtn" type="button" class="btn btn-default" data-dismiss="modal">' . Translator::i18n('Cancelar') . '</button>
			        		<button id="ajaxFormModalConfirmBtn" type="button" class="btn btn-primary" data-dismiss="modal">' . Translator::i18n('Confirmar') . '</button>
			        	</div>
			    	</div>
			  	</div>
			</div>';

		return $lsForm;
	}
}
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

namespace Onion\Form\Render;
use Onion\Log\Debug;
use Onion\Lib\Util;
use Onion\Template\Layout;
use Onion\I18n\Translator;
use Onion\Form\Render\ElementInterface;

abstract class ElementAbstract implements ElementInterface
{
	protected $_oElement;

	protected $_sName;
	
	protected $_sValue;

	protected $_aAttr = array();
	
	protected $_aOpts = array();

	protected $_aDataOpts = array();

	protected $_nColLength = 6;

	protected $_sTemplateName = "default";

	protected $_sTemplate = "";


	/**
	 *
	 * @param object $poElement
	 * @param string $psTemplate
	 * @return \Onion\Form\Render\Element
	 */
	public function __construct ($poElement, $psTemplate = "default")
	{
		$this->_oElement = $poElement;

		$this->_sName = $this->_oElement->getName();
		
		$this->_sValue = $this->_oElement->getValue();

		$this->_aAttr = $this->_oElement->getAttributes();
		
		$this->_aOpts = $this->_oElement->getOptions();
		
		if ($this->_oElement->getOption('data-type') !== null)
		{
			if ($this->_oElement->getOption($this->_oElement->getOption('data-type')) !== null)
			{
				$this->_aDataOpts = $this->_oElement->getOption($this->_oElement->getOption('data-type'));
			}
		}

		$lnColLength = $this->_oElement->getOption('length');

		if (!empty($lnColLength))
		{
			$this->_nColLength = $lnColLength;
		}

		$this->_sTemplateName = $psTemplate;

		if ($this->_sTemplateName == "default")
		{
			$this->_sTemplate = $this->getDefaultTemplate();
		}
		else
		{
			$this->_sTemplate = Layout::getTemplate($this->_stemplate);
		}

		return $this;
	}
	

	/**
	 *
	 * @param string $psIten
	 * @param string $psDefault
	 * @return string
	 */
	public function getAttrVal ($psIten, $psDefault = "")
	{
		if (array_key_exists($psIten, $this->_aAttr))
		{
			if ($psIten == 'required' && $this->_aAttr[$psIten] == true)
			{
				return 'required="required"';
			}
			elseif ($psIten == 'readonly' && $this->_aAttr[$psIten] == true)
			{
				return 'readonly="readonly"';
			}
			elseif ($psIten == 'placeholder' && !empty($this->_aAttr[$psIten]))
			{
				return 'placeholder="' . $this->_aAttr[$psIten] . '"';
			}
			elseif ($psIten == 'pattern' && !empty($this->_aAttr[$psIten]))
			{
				return 'pattern="' . $this->_aAttr[$psIten] . '"';
			}
			elseif ($psIten == 'data-mask' && !empty($this->_aAttr[$psIten]))
			{
				return 'data-mask="' . $this->_aAttr[$psIten] . '"';
			}
			elseif ($psIten == 'data-maskalt' && !empty($this->_aAttr[$psIten]))
			{
				return 'data-maskalt="' . $this->_aAttr[$psIten] . '"';
			}
			elseif ($psIten == 'data-toUpper' && $this->_aAttr[$psIten] == false)
			{
				return 'data-toUpper="false"';
			}
			elseif ($psIten == 'data-toUpper' && $this->_aAttr[$psIten] == true)
			{
				return 'data-toUpper="true"';
			}
			else
			{
				return $this->_aAttr[$psIten];
			}
		}
		else
		{
			return $psDefault;
		}
	}
	
	
	/**
	 * 
	 * @param string $psIten
	 * @param string $psDefault
	 * @return string
	 */
	public function getOptsVal ($psIten, $psDefault = "")
	{
		if (array_key_exists($psIten, $this->_aOpts))
		{
			return $this->_aOpts[$psIten];
		}
		else 
		{
			return $psDefault;
		}
	}
	
	
	/**
	 *
	 * @param string $psIten
	 * @param string $psDefault
	 * @return string
	 */
	public function getDataOptsVal ($psIten, $psDefault = "")
	{
		if (array_key_exists($psIten, $this->_aDataOpts))
		{
			return $this->_aDataOpts[$psIten];
		}
		else
		{
			return $psDefault;
		}
	}

	
	/**
	 *
	 * @param object $poElement
	 * @return array
	 */
	public function getFieldMessageX ($poElement)
	{
		$laMessages = $poElement->getMessages();
		$lsMsgError = "";
		$lsClassError = "";
	
		if (is_array($laMessages))
		{
			foreach ($laMessages as $lsMsg)
			{
				$lsMsgError .= "<li>$lsMsg</li>";
			}
		}
	
		if (!empty($lsMsgError))
		{
			$lsClassError = " input-error";
			$lsMsgError = "<ul>$lsMsgError</ul>";
		}
	
		return array(
			'class'=>$lsClassError,
			'msg'=>$lsMsgError
		);
	}
	
	
	/**
	 *
	 * @param object $poElement
	 * @return array
	 */
	public function getFieldMessage ()
	{
		$laMessages = $this->_oElement->getMessages();
		$lsMsgError = "";
		$lsClassError = "";
	
		if (is_array($laMessages))
		{
			foreach ($laMessages as $lsMsg)
			{
				$lsMsgError .= "<li>$lsMsg</li>";
			}
		}
	
		if (!empty($lsMsgError))
		{
			$lsClassError = " input-error";
			$lsMsgError = "<ul>$lsMsgError</ul>";
		}
	
		return array(
			'class'=>$lsClassError,
			'msg'=>$lsMsgError
		);
	}
	
	
	/**
	 *
	 * @return void|string
	 */
	public function getIconArea ()
	{
		$lsIcon = $this->getOptsVal('icon');
	
		if (!empty($lsIcon))
		{
			return '<i id="'.$this->_sName.'Icon" class="' . $lsIcon . '"></i> ';
		}
	
		return;
	}
}
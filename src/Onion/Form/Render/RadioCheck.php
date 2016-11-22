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
use Onion\Form\Render\ElementAbstract;

class RadioCheck extends ElementAbstract
{
	
	/**
	 *
	 * @param string $psType
	 * @return string
	 */
	public function render ($poView = null)
	{
		if ($this->getOptsVal('data-default'))
		{		
			$this->_sTemplate = $this->getClearTemplate();
		}
		
		$laMessage = $this->getFieldMessage();
	
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));
		Layout::parseTemplate($this->_sTemplate, "#%HELPICON%#", $this->getHelpArea());
		Layout::parseTemplate($this->_sTemplate, "#%REQUIREDICON%#", $this->getRequiredArea());	
		Layout::parseTemplate($this->_sTemplate, "#%MSGICON%#", $this->getMessageArea($laMessage));
		Layout::parseTemplate($this->_sTemplate, "#%CLASSERROR%#", $laMessage['class']);		
		Layout::parseTemplate($this->_sTemplate, "#%TITLE%#", $this->getAttrVal('title'));
		Layout::parseTemplate($this->_sTemplate, "#%ITEMAREA%#", $this->getItems($laMessage['class']));
	
		return $this->_sTemplate;	
	}

	
	/**
	 * 
	 * @param string $psClass
	 * @return string
	 */
	public function getItems ($psClass = "")
	{
		$lsItemArea = "";
		
		$lmOptChecked = $this->_sValue;
		$laOptions = $this->getOptsVal('value_options');
		
		if (is_array($laOptions))
		{
			foreach ($laOptions as $lsValue => $lmLabel)
			{
				$lsActive = '';
				$lsChecked = '';
				$lsColor = '';
				$lsLabel = '';
				$lsIcon = '';
				$lsTitle = '';
				$lsCheckbox = '';
				
				if (is_array($lmLabel))
				{
					$lsLabel = $lmLabel['label'];
					$lsIcon = isset($lmLabel['icon']) ? $this->getIcon($lmLabel['icon']) : '';
					$lsTitle = isset($lmLabel['title']) ? 'title="' . $lmLabel['title'] . '"' : '';
				}
				else 
				{
					$lsLabel = $lmLabel;
				}
				
				$lsType = $this->getAttrVal('type');
				
				if ($lsType == 'multi_checkbox')
				{
					$lsType = 'checkbox';
					$lsCheckbox = '[]';
				}
				
				if ($lsType == "checkbox")
				{
				    if (is_array($lmOptChecked))
				    {
				        foreach ($lmOptChecked as $lsOptChecked)
				        {
				            if ($lsOptChecked == $lsValue)
    				        {
    					       $lsActive = ' active';
    					       $lsChecked = 'checked';
    					       //$lsColor = " btn-info";
    				        }	
				        }
				    }
				    elseif ($lmOptChecked == $lsValue)
    				{
    					$lsActive = ' active';
    					$lsChecked = 'checked';
    					//$lsColor = " btn-info";			        
				    }
				}
				else
				{
    				if ($lmOptChecked == $lsValue)
    				{
    					$lsActive = ' active';
    					$lsChecked = 'checked';
    					//$lsColor = " btn-info";
    				}				
				}
				
				$lsItemTemplate = $this->getItemTemplate();
				
				if ($this->getOptsVal('data-default'))
				{
					$lsItemTemplate = $this->getClearItemTemplate($this->getOptsVal('data-display'));
				}
				
				Layout::parseTemplate($lsItemTemplate, "#%NAME%#", $this->_sName . $lsCheckbox);
				Layout::parseTemplate($lsItemTemplate, "#%ID%#", $this->_sName . '-' . $lsValue);
				Layout::parseTemplate($lsItemTemplate, "#%COLOR%#", $lsColor);
				Layout::parseTemplate($lsItemTemplate, "#%TYPE%#", $lsType);
				Layout::parseTemplate($lsItemTemplate, "#%CLASS%#", $psClass);
				Layout::parseTemplate($lsItemTemplate, "#%ACTIVE%#", $lsActive);
				Layout::parseTemplate($lsItemTemplate, "#%VALUE%#", $lsValue);
				Layout::parseTemplate($lsItemTemplate, "#%CHECKED%#", $lsChecked);
				Layout::parseTemplate($lsItemTemplate, "#%TOUPPER%#", $this->getAttrVal('data-toUpper'));
				Layout::parseTemplate($lsItemTemplate, "#%REQUIRED%#", $this->getAttrVal('required'));
				Layout::parseTemplate($lsItemTemplate, "#%READONLY%#", $this->getAttrVal('readonly'));
				Layout::parseTemplate($lsItemTemplate, "#%ITEMCLASS%#", $this->getAttrVal('class'));
				Layout::parseTemplate($lsItemTemplate, "#%ITEMLABEL%#", $lsLabel);
				Layout::parseTemplate($lsItemTemplate, "#%ICON%#", $lsIcon);
				Layout::parseTemplate($lsItemTemplate, "#%TITLE%#", $lsTitle);
				
				$lsItemArea .= $lsItemTemplate;
			}
		}
		
		return $lsItemArea;
	}
	
	
	/**
	 *
	 * @return void|string
	 */
	public function getIcon ($psIcon)
	{
		if (!empty($psIcon))
		{
			return '<i class="' . $psIcon . '"></i> ';
		}
	
		return;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getItemTemplate ()
	{
		$lsEcho = '
		<label class="btn btn-default #%CLASS%# #%ACTIVE%# #%COLOR%#" #%TITLE%#>
			<input
				type="#%TYPE%#"
				name="#%NAME%#"
				id="#%ID%#"
				value="#%VALUE%#"
				autocomplete="off"
		        class="#%ITEMCLASS%#"
		        #%TITLE%#
		        #%TOUPPER%#
				#%CHECKED%#
				#%REQUIRED%#
				#%READONLY%#
				> #%ICON%# #%ITEMLABEL%#
		</label>';
		
		return $lsEcho;
	}
	
	
	/**
	 *
	 * @return string
	 */
	public function getClearItemTemplate ($psDisplay = 'block')
	{
		$lsEcho = '
		<label class="#%CLASS%# #%ACTIVE%#" #%TITLE%#>
			<input
				type="#%TYPE%#"
				name="#%NAME%#"
				id="#%ID%#"
				value="#%VALUE%#"
				autocomplete="off"
		        class="#%ITEMCLASS%#"
		        #%TITLE%#
		        #%TOUPPER%#
				#%CHECKED%#
				#%REQUIRED%#
				#%READONLY%#
				> #%ICON%# #%ITEMLABEL%#
		</label>';
		
		if ($psDisplay == 'block')
		{
			$lsEcho .= '<br/>';
		}
		else
		{
			$lsEcho .= '&nbsp;&nbsp;&nbsp;';
		}
	
		return $lsEcho;
	}

	
	/**
	 *
	 * @return string
	 */
	public function getDefaultTemplate ()
	{
		$lsEcho = '
		<div class="input-form input-form-sm col-lg-#%COLLENGTH%# #%CLASSERROR%#">
			<label for="#%FOR%#">#%LABEL%#</label>#%REQUIREDICON%##%HELPICON%##%MSGICON%#<br/>
			<div class="btn-group" data-toggle="buttons">
				#%ITEMAREA%#
			</div>
		</div>';
	
		return $lsEcho;
	}
	
	
	/**
	 *
	 * @return string
	 */
	public function getClearTemplate ()
	{
		$lsEcho = '
		<div class="input-form input-form-sm col-lg-#%COLLENGTH%# #%CLASSERROR%#">
			<label for="#%FOR%#">#%LABEL%#</label>#%REQUIREDICON%##%HELPICON%##%MSGICON%#<br/>
			<div class="">
				#%ITEMAREA%#
			</div>
		</div>';
	
		return $lsEcho;
	}
}
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

class SearchField extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	public function renderSearchField ($poElement)
	{
		$laOptions = $poElement->getOption('searchField');
		$laElementName = $poElement->getName();
	
		$laMessage = $this->getFieldMessage($poElement);
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho = '<div class="input-form input-form-sm col-lg-'.$lnColLength.'">';
		$lsEcho .= '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label>';
	
		if (isset($laOptions['data-url']) && !empty($laOptions['data-url']))
		{
			$lsEcho .= '	<div class="input-group">';
	
			$lsEcho .= '		<span class="input-group-btn">';
			$lsEcho .= '			<button
									type="button"
									id="'.(isset($laOptions['id']) ? $laOptions['id'] : $laElementName.'Btn').'"
									title="'.(isset($laOptions['data-title']) ? $laOptions['data-title'] : (isset($laAttr['title']) ? $laAttr['title'] : "")).'"
									class="openPopUpBtn '.(isset($laOptions['data-class']) ? $laOptions['data-class'] : (isset($laAttr['class']) ? $laAttr['class'] : "btn btn-default")).'"
									data-wname="'.(isset($laOptions['data-wname']) ? $laOptions['data-wname'] : "").'"
									data-url="'.(isset($laOptions['data-url']) ? $laOptions['data-url'] : "").'"
									data-params="'.(isset($laOptions['data-params']) ? $laOptions['data-params'] : "").'"
									data-wheight="'.(isset($laOptions['data-wheight']) ? $laOptions['data-wheight'] : "90%").'"
									data-wwidth="'.(isset($laOptions['data-wwidth']) ? $laOptions['data-wwidth'] : "90%").'"
									>
									<i class="'.(isset($laOptions['data-icon']) ? $laOptions['data-icon'] : "glyphicon glyphicon-plus-sign").'"></i>
									</button>';
			$lsEcho .= '		</span>';
		}
		else
		{
			$lsEcho .= '	<div>';
		}
	
		$lsEcho .= '		<input
							type="search"
							id="'.$poElement->getAttribute('id').'"
							name="'.$poElement->getName().'"
							title="'.$poElement->getAttribute('title').'"
							placeholder="'.$poElement->getAttribute('placeholder').'"
							'.($poElement->getAttribute('data-mask') ? 'data-mask="' . $poElement->getAttribute('data-mask') . '"' : "").'
							'.($poElement->getAttribute('data-maskalt') ? 'data-maskalt="' . $poElement->getAttribute('data-maskalt') . '"' : "").'
							'.($poElement->getAttribute('required') ? 'required="required"' : "").'
							'.($poElement->getAttribute('readonly') ? 'readonly="readonly"' : "").'
							class="'.$poElement->getAttribute('class') . $laMessage['class'] . ' searchField"
							value="'.$poElement->getValue().'"
							data-act="'.(isset($laOptions['data-act']) ? $laOptions['data-act'] : "").'"
							data-field="'.(isset($laOptions['data-field']) ? $laOptions['data-field'] : "").'"
							data-return="'.(isset($laOptions['data-return']) ? $laOptions['data-return'] : "").'"
							data-filter="'.(isset($laOptions['data-filter']) ? $laOptions['data-filter'] : "").'"
							data-select="'.(isset($laOptions['data-select']) ? $laOptions['data-select'] : "").'"
							data-fnCall="'.(isset($laOptions['data-fnCall']) ? $laOptions['data-fnCall'] : "").'"
							>';
	
		if (isset($laOptions['data-viewAct']) && !empty($laOptions['data-viewAct']))
		{
			$lsEcho .= '		<span class="input-group-btn">';
			$lsEcho .= '			<button
									type="button"
									id="'.(isset($laOptions['id']) ? $laOptions['id'] : $laElementName.'ViewBtn').'"
									title="'.(isset($laOptions['title']) ? $laOptions['title'] : (isset($laOptions['data-title']) ? $laOptions['data-title'] : "")).'"
									class="'.(isset($laOptions['class']) ? $laOptions['class'] : "btn btn-default").'"
									data-title="'.(isset($laOptions['data-viewTitle']) ? $laOptions['data-viewTitle'] : "").'"
									data-act="'.(isset($laOptions['data-viewAct']) ? $laOptions['data-viewAct'] : "").'"
									>
									<i class="'.(isset($laOptions['data-viewIcon']) ? $laOptions['data-viewIcon'] : "glyphicon glyphicon-eye-open").'"></i>
									</button>';
			$lsEcho .= '		</span>';
		}
			
		$lsEcho .= $laMessage['msg'];
		$lsEcho .= '		<i class="requiredMark"></i>';
		$lsEcho .= '		<span class="hintHelp"></span>';
		$lsEcho .= '	</div>';
		$lsEcho .= '</div>';
	
		return $lsEcho;
	}
	
	
	/**
	 *
	 * @param object $poView
	 * @return string
	 */
	public function render ($poView = null)
	{
		$laMessage = $this->getFieldMessage();
		$lsBtnAddTemplate = "";
		$lsBtnExtTemplate = "";
		$lsDivClass = "";
		
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));

		if ($this->getDataOptsVal('data-url') != "")
		{
			$lsBtnAddTemplate = $this->getBtnAddTemplate();
			
			Layout::parseTemplate($lsBtnAddTemplate, "#%BTNID%#", $this->getDataOptsVal('id', $this->_sName . 'Btn'));
			Layout::parseTemplate($lsBtnAddTemplate, "#%BTNTITLE%#", $this->getDataOptsVal('title', $this->getAttrVal('title')));
			Layout::parseTemplate($lsBtnAddTemplate, "#%BTNCLASS%#", $this->getDataOptsVal('class', $this->getAttrVal('class', "btn btn-default")));
			
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAWNAME%#", $this->getDataOptsVal('data-wname'));
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAURL%#", $this->getDataOptsVal('data-url'));
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAPARAMS%#", $this->getDataOptsVal('data-params'));
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAWHEIGHT%#", $this->getDataOptsVal('data-wheight', "90%"));
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAWWIDTH%#", $this->getDataOptsVal('data-wwidth', "90%"));
			Layout::parseTemplate($lsBtnAddTemplate, "#%DATAICON%#", $this->getDataOptsVal('data-icon', "glyphicon glyphicon-plus-sign"));
			
			$lsDivClass = "input-group";
		}
	
		Layout::parseTemplate($this->_sTemplate, "#%BTNADDAREA%#", $lsBtnAddTemplate);
		
		Layout::parseTemplate($this->_sTemplate, "#%ID%#", $this->getAttrVal('id'));
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		Layout::parseTemplate($this->_sTemplate, "#%CLASS%#", $this->getAttrVal('class') . $laMessage['class']);
		Layout::parseTemplate($this->_sTemplate, "#%VALUE%#", $this->_sValue);
		Layout::parseTemplate($this->_sTemplate, "#%TITLE%#", $this->getAttrVal('title'));
		
		Layout::parseTemplate($this->_sTemplate, "#%PLACEHOLDER%#", $this->getAttrVal('placeholder'));
		Layout::parseTemplate($this->_sTemplate, "#%REQUIRED%#", $this->getAttrVal('required'));
		Layout::parseTemplate($this->_sTemplate, "#%READONLY%#", $this->getAttrVal('readonly'));
		Layout::parseTemplate($this->_sTemplate, "#%PATTERN%#", $this->getAttrVal('pattern'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASK%#", $this->getAttrVal('data-mask'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASKALT%#", $this->getAttrVal('data-maskalt'));
		
		Layout::parseTemplate($this->_sTemplate, "#%DATAACT%#", $this->getDataOptsVal('data-act'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAFIELD%#", $this->getDataOptsVal('data-field'));
		Layout::parseTemplate($this->_sTemplate, "#%DATARETURN%#", $this->getDataOptsVal('data-return'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAFILTER%#", $this->getDataOptsVal('data-filter'));
		Layout::parseTemplate($this->_sTemplate, "#%DATASELECT%#", $this->getDataOptsVal('data-select'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAFNCALL%#", $this->getDataOptsVal('data-fnCall'));
		
		$lsBtnExtraTemplate = "";

		if ($this->getDataOptsVal('data-extAct') != "")
		{
			$lsBtnExtraTemplate = $this->getBtnExtraTemplate();
			
			Layout::parseTemplate($lsBtnExtraTemplate, "#%BTNID%#", $this->getDataOptsVal('data-extId', $this->_sName . 'ExtBtn'));
			Layout::parseTemplate($lsBtnExtraTemplate, "#%BTNTITLE%#", $this->getDataOptsVal('data-extTitle', $this->getAttrVal('title')));
			Layout::parseTemplate($lsBtnExtraTemplate, "#%BTNCLASS%#", $this->getDataOptsVal('data-extClass', $this->getAttrVal('class', "btn btn-default")));
			Layout::parseTemplate($lsBtnExtraTemplate, "#%DATATITLE%#", $this->getDataOptsVal('data-extTitle'));
			Layout::parseTemplate($lsBtnExtraTemplate, "#%DATAACT%#", $this->getDataOptsVal('data-extAct'));
			Layout::parseTemplate($lsBtnExtraTemplate, "#%DATAICON%#", $this->getDataOptsVal('data-extIcon', "glyphicon glyphicon-edit"));
			
			$lsDivClass = "input-group";
		}

		Layout::parseTemplate($this->_sTemplate, "#%BTNEXTRAAREA%#", $lsBtnExtraTemplate);
		
		Layout::parseTemplate($this->_sTemplate, "#%DIVCLASS%#", $lsDivClass);
		
		Layout::parseTemplate($this->_sTemplate, "#%MSG%#", $laMessage['msg']);
		
		return $this->_sTemplate;
	}

	
	/**
	 *
	 * @return string
	 */
	public function getBtnAddTemplate ()
	{
		$lsEcho = '
		<span class="input-group-btn">
			<button
				type="button"
				id="#%BTNID%#"
				title="#%BTNTITLE%#"
				class="openPopUpBtn #%BTNCLASS%#"
				data-wname="#%DATAWNAME%#"
				data-url="#%DATAURL%#"
				data-params="#%DATAPARAMS%#"
				data-wheight="#%DATAWHEIGHT%#"
				data-wwidth="#%DATAWWIDTH%#">
					<i class="#%DATAICON%#"></i>
			</button>
		</span>';
		
		return $lsEcho;
	}
	

	/**
	 *
	 * @return string
	 */
	public function getBtnExtraTemplate ()
	{
		$lsEcho = '
		<span class="input-group-btn">
			<button
				type="button"
				id="#%BTNID%#"
				title="#%BTNTITLE%#"
				class="#%BTNCLASS%#"
				data-title="#%DATATITLE%#"
				data-act="#%DATAACT%#">
					<i class="#%DATAICON%#"></i>
			</button>
		</span>';
	
		return $lsEcho;
	}
	
	
	/**
	 *
	 * @return string
	 */
	public function getDefaultTemplate ()
	{
		$lsEcho = '
		<div class="input-form input-form-sm col-lg-#%COLLENGTH%#">
			<label for="#%FOR%#">#%LABEL%# </label>
			<div class="#%DIVCLASS%#">
				#%BTNADDAREA%#
				<input
					type="search"
					id="#%ID%#"
					name="#%NAME%#"
					class="#%CLASS%# searchField"
					value="#%VALUE%#"
					title="#%TITLE%#"
					#%PLACEHOLDER%#
					#%PATTERN%#
					#%DATAMASK%#
					#%DATAMASKALT%#
					#%REQUIRED%#
					#%READONLY%#
					data-act="#%DATAACT%#"
					data-field="#%DATAFIELD%#"
					data-return="#%DATARETURN%#"
					data-filter="#%DATAFILTER%#"
					data-select="#%DATASELECT%#"
					data-fnCall="#%DATAFNCALL%#">
				#%BTNEXTRAAREA%#
				#%MSG%#
				<i class="requiredMark"></i>
				<span class="hintHelp"></span>
			</div>
		</div>';
	
		return $lsEcho;
	}
}
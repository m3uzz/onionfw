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

class DateTimePicker extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	public function renderDateTimePicker ($poElement)
	{
		$laOptions = $poElement->getOption('dateTimePicker');
		$laElementName = $poElement->getName();
	
		$laMessage = $this->getFieldMessage($poElement);
	
		$laDateTime = preg_split("/ |T/", $poElement->getValue());
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho = '<div class="input-form input-form-sm col-lg-'.$lnColLength.'">';
		$lsEcho .= '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label>';
		$lsEcho .= '	<input class="dateTimeField" type="hidden" id="'.$poElement->getAttribute('id').'" name="'.$poElement->getName().'" value="'.$poElement->getValue().'">';
		$lsEcho .= '	<div class="input-group date-time" id="'.$poElement->getAttribute('id').'Group">';
		$lsEcho .= '		<input
							type="'.$poElement->getAttribute('type').'"
							id="'.$poElement->getAttribute('id').'Date"
							name="'.$poElement->getName().'Date"
							title="'.$poElement->getAttribute('title').'"
							placeholder="'.$poElement->getAttribute('placeholder').'"
							'.($poElement->getAttribute('required') ? 'required="required"' : "").'
							'.($poElement->getAttribute('readonly') ? 'readonly="readonly"' : "").'
							class="'.$poElement->getAttribute('class') . $laMessage['class'] . ' datepickerField"
							'.($poElement->getAttribute('pattern') ? 'pattern="' . $poElement->getAttribute('pattern') . '"' : "").'
							'.($poElement->getAttribute('data-mask') ? 'data-mask="' . $poElement->getAttribute('data-mask') . '"' : "").'
							'.($poElement->getAttribute('data-maskalt') ? 'data-maskalt="' . $poElement->getAttribute('data-maskalt') . '"' : "").'
							value="'.(isset($laDateTime[0]) ? Translator::dateS2P($laDateTime[0]) : "").'"
							autocomplete="off",
							>';
		$lsEcho .= '		<span class="input-group-btn">';
		$lsEcho .= '			<button
								type="button"
								id="'.(isset($laOptions['id']) ? $laOptions['id'] : $laElementName.'Btn').'"
								title="'.(isset($laOptions['title']) ? $laOptions['title'] : (isset($laOptions['data-title']) ? $laOptions['data-title'] : "")).'"
								class="'.(isset($laOptions['class']) ? $laOptions['class'] : "btn btn-default datepickerTrigger").'"
								>
								<i class="'.(isset($laOptions['data-icon']) ? $laOptions['data-icon'] : "glyphicon glyphicon-calendar").'"></i>
								</button>';
		$lsEcho .= '		</span>';
		$lsEcho .= '		<input
							type="'.$poElement->getAttribute('type').'"
							id="'.$poElement->getAttribute('id').'Time"
							name="'.$poElement->getName().'Time"
							title="'.$poElement->getAttribute('title').'"
							placeholder="  :"
							'.($poElement->getAttribute('required') ? 'required="required"' : "").'
							'.($poElement->getAttribute('readonly') ? 'readonly="readonly"' : "").'
							class="'.$poElement->getAttribute('class') . $laMessage['class'] . ' timepickerField"
							'.($poElement->getAttribute('pattern') ? 'pattern="' . $poElement->getAttribute('pattern') . '"' : "").'
							'.($poElement->getAttribute('data-mask') ? 'data-mask="' . $poElement->getAttribute('data-mask') . '"' : "").'
							'.($poElement->getAttribute('data-maskalt') ? 'data-maskalt="' . $poElement->getAttribute('data-maskalt') . '"' : "").'
							value="'.(isset($laDateTime[1]) ? $laDateTime[1] : "").'"
							autocomplete="off"
							>';
		$lsEcho .= '		<span class="input-group-btn">';
		$lsEcho .= '			<button
								type="button"
								id="'.(isset($laOptions['id']) ? $laOptions['id'] : $laElementName.'Btn').'"
								title="'.(isset($laOptions['title']) ? $laOptions['title'] : (isset($laOptions['data-title']) ? $laOptions['data-title'] : "")).'"
								class="'.(isset($laOptions['class']) ? $laOptions['class'] : "btn btn-default timepickerTrigger").'"
								>
								<i class="'.(isset($laOptions['data-icon']) ? $laOptions['data-icon'] : "glyphicon glyphicon-time").'"></i>
								</button>';
		$lsEcho .= '		</span>';
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
	
		$laDateTime = preg_split("/ |T/", $this->_sValue);
		$lsDate = (isset($laDateTime[0]) ? $laDateTime[0] : "");
		$lsTime = (isset($laDateTime[1]) ? $laDateTime[1] : "");
		
		if ($this->getDataOptsVal('format-date', true))
		{
			$lsDate = Translator::dateS2P($lsDate);
		}
		
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));
		
		Layout::parseTemplate($this->_sTemplate, "#%ID%#", $this->getAttrVal('id'));
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		Layout::parseTemplate($this->_sTemplate, "#%VALUE%#", Translator::dateS2P($this->_sValue));
		Layout::parseTemplate($this->_sTemplate, "#%TYPE%#", $this->getAttrVal('type'));
		Layout::parseTemplate($this->_sTemplate, "#%CLASS%#", $this->getAttrVal('class') . $laMessage['class']);	
		Layout::parseTemplate($this->_sTemplate, "#%VALUE1%#", $lsDate);
		Layout::parseTemplate($this->_sTemplate, "#%VALUE2%#", $lsTime);
		Layout::parseTemplate($this->_sTemplate, "#%TITLE%#", $this->getAttrVal('title'));
		
		Layout::parseTemplate($this->_sTemplate, "#%PLACEHOLDER%#", $this->getAttrVal('placeholder'));
		Layout::parseTemplate($this->_sTemplate, "#%REQUIRED%#", $this->getAttrVal('required'));
		Layout::parseTemplate($this->_sTemplate, "#%READONLY%#", $this->getAttrVal('readonly'));
		Layout::parseTemplate($this->_sTemplate, "#%PATTERN%#", $this->getAttrVal('pattern'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASK%#", $this->getAttrVal('data-mask'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASKALT%#", $this->getAttrVal('data-maskalt'));
		
		Layout::parseTemplate($this->_sTemplate, "#%BTNID%#", $this->getDataOptsVal('id', $this->_sName . 'Btn'));
		
		Layout::parseTemplate($this->_sTemplate, "#%BTNTITLE1%#", $this->getDataOptsVal('data-title1', $this->getAttrVal('title')));
		Layout::parseTemplate($this->_sTemplate, "#%BTNCLASS1%#", $this->getDataOptsVal('data-class1', "btn btn-default"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAICON1%#", $this->getDataOptsVal('data-icon1', "glyphicon glyphicon-calendar"));
		
		Layout::parseTemplate($this->_sTemplate, "#%BTNTITLE2%#", $this->getDataOptsVal('data-title2', $this->getAttrVal('title')));
		Layout::parseTemplate($this->_sTemplate, "#%BTNCLASS2%#", $this->getDataOptsVal('data-class2', "btn btn-default"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAICON2%#", $this->getDataOptsVal('data-icon2', "glyphicon glyphicon-time"));
		
		Layout::parseTemplate($this->_sTemplate, "#%MSG%#", $laMessage['msg']);
		
		return $this->_sTemplate;
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
			<input class="dateTimeField" type="hidden" id="#%ID%#" name="#%NAME%#" value="#%VALUE%#">
			<div class="input-group date-time" id="#%ID%#Group">
				<input
					type="#%TYPE%#"
					id="#%ID%#Date"
					name="#%NAME%#Date"
					class="#%CLASS%# datepickerField"
					value="#%VALUE1%#"
					title="#%TITLE%#"
					#%PLACEHOLDER%#
					#%REQUIRED%#
					#%READONLY%#
					#%PATTERN%#
					#%DATAMASK%#
					#%DATAMASKALT%#
					autocomplete="off">
				<span class="input-group-btn">
					<button
						type="button"
						id="#%BTNID%#Dt"
						title="#%BTNTITLE1%#"
						class="#%BTNCLASS1%# datepickerTrigger">
							<i id="#%NAME%#IconDate" class="#%DATAICON1%#"></i>
					</button>
				</span>
				<input
					type="#%TYPE%#"
					id="#%ID%#Time"
					name="#%NAME%#Time"
					placeholder="  :"
					class="#%CLASS%# timepickerField"
					value="#%VALUE2%#"
					title="#%TITLE%#"
					#%REQUIRED%#
					#%READONLY%#
					#%PATTERN%#
					#%DATAMASK%#
					#%DATAMASKALT%#
					autocomplete="off">
				<span class="input-group-btn">
					<button
						type="button"
						id="#%BTNID%#Tm"
						title="#%BTNTITLE2%#"
						class="#%BTNCLASS2%# timepickerTrigger">
							<i id="#%NAME%#IconTime" class="#%DATAICON2%#"></i>
					</button>
				</span>
				#%MSG%#
				<i class="requiredMark"></i>
				<span class="hintHelp"></span>
			</div>
		</div>';
	
		return $lsEcho;
	}
}
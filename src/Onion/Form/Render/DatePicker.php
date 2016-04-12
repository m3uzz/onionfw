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

class DatePicker extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	public function renderDatePicker ($poElement)
	{
		$laOptions = $poElement->getOption('datePicker');
		$laElementName = $poElement->getName();
	
		$laMessage = $this->getFieldMessage($poElement);
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho = '<div class="input-form input-form-sm col-lg-'.$lnColLength.'">';
		$lsEcho .= '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label>';
		$lsEcho .= '	<div class="input-group date" id="'.$poElement->getAttribute('id').'Group">';
		$lsEcho .= '		<input
							type="'.$poElement->getAttribute('type').'"
							id="'.$poElement->getAttribute('id').'"
							name="'.$poElement->getName().'"
							title="'.$poElement->getAttribute('title').'"
							placeholder="'.$poElement->getAttribute('placeholder').'"
							'.($poElement->getAttribute('required') ? 'required="required"' : "").'
							'.($poElement->getAttribute('readonly') ? 'readonly="readonly"' : "").'
							class="'.$poElement->getAttribute('class') . $laMessage['class'] . ' datepickerField"
							'.($poElement->getAttribute('pattern') ? 'pattern="' . $poElement->getAttribute('pattern') . '"' : "").'
							'.($poElement->getAttribute('data-mask') ? 'data-mask="' . $poElement->getAttribute('data-mask') . '"' : "").'
							'.($poElement->getAttribute('data-maskalt') ? 'data-maskalt="' . $poElement->getAttribute('data-maskalt') . '"' : "").'
							value="'.Translator::dateS2P($poElement->getValue()).'"
							autocomplete="off"
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
		$lsEcho .= $laMessage['msg'];
		$lsEcho .=  '		<i class="requiredMark"></i>';
		$lsEcho .=  '		<span class="hintHelp"></span>';
		$lsEcho .=  '	</div>';
		$lsEcho .=  '</div>';
	
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
	
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));
		
		Layout::parseTemplate($this->_sTemplate, "#%TYPE%#", $this->getAttrVal('type'));
		Layout::parseTemplate($this->_sTemplate, "#%ID%#", $this->getAttrVal('id'));
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		Layout::parseTemplate($this->_sTemplate, "#%CLASS%#", $this->getAttrVal('class') . $laMessage['class']);
		Layout::parseTemplate($this->_sTemplate, "#%VALUE%#", Translator::dateS2P($this->_sValue));
		Layout::parseTemplate($this->_sTemplate, "#%TITLE%#", $this->getAttrVal('title'));
		
		Layout::parseTemplate($this->_sTemplate, "#%PLACEHOLDER%#", $this->getAttrVal('placeholder'));
		Layout::parseTemplate($this->_sTemplate, "#%REQUIRED%#", $this->getAttrVal('required'));
		Layout::parseTemplate($this->_sTemplate, "#%READONLY%#", $this->getAttrVal('readonly'));
		Layout::parseTemplate($this->_sTemplate, "#%PATTERN%#", $this->getAttrVal('pattern'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASK%#", $this->getAttrVal('data-mask'));
		Layout::parseTemplate($this->_sTemplate, "#%DATAMASKALT%#", $this->getAttrVal('data-maskalt'));
		
		Layout::parseTemplate($this->_sTemplate, "#%BTNID%#", $this->getDataOptsVal("id", $this->_sName . 'Btn'));
		Layout::parseTemplate($this->_sTemplate, "#%BTNTITLE%#", $this->getDataOptsVal('title', $this->getAttrVal('title')));
		Layout::parseTemplate($this->_sTemplate, "#%BTNCLASS%#", $this->getDataOptsVal("class", "btn btn-default"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAICON%#", $this->getDataOptsVal("data-icon", "glyphicon glyphicon-calendar"));
		
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
			<div class="input-group date" id="#%ID%#Group">
				<input
					type="#%TYPE%#"
					id="#%ID%#"
					name="#%NAME%#"
					class="#%CLASS%# datepickerField"
					value="#%VALUE%#"
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
						id="#%BTNID%#"
						title="#%BTNTITLE%#"
						class="#%BTNCLASS%# datepickerTrigger">
							<i id="#%NAME%#Icon" class="#%DATAICON%#"></i>
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
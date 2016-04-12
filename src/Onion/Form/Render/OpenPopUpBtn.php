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

class OpenPopUpBtn extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	public function renderOpenPopUpBtn ($poElement)
	{
		$laOptions = $poElement->getOption('openPopUpBtn');
		$laAttr = $poElement->getAttributes();
	
		$laElementName = $poElement->getName();
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho = '<div class="input-form input-form-sm col-lg-'.$lnColLength.'">';
		$lsEcho .= '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label>';
		$lsEcho .= '	<div class="input-group">';
		$lsEcho .= '		<button
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
							<i class="'.(isset($laOptions['data-icon']) ? $laOptions['data-icon'] : "glyphicon glyphicon-cog").'"></i>
							'.$poElement->getValue().'
							</button>';
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
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		
		Layout::parseTemplate($this->_sTemplate, "#%ID%#", $this->getAttrVal("id", $this->_sName ));
		Layout::parseTemplate($this->_sTemplate, "#%TITLE%#", $this->getAttrVal("title"));
		Layout::parseTemplate($this->_sTemplate, "#%CLASS%#", $this->getAttrVal("class", "btn btn-default"));
		
		Layout::parseTemplate($this->_sTemplate, "#%DATAWNAME%#", $this->getDataOptsVal("data-wname"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAURL%#", $this->getDataOptsVal("data-url"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAPARAMS%#", $this->getDataOptsVal("data-params"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAWHEIGHT%#", $this->getDataOptsVal("data-wheight", "90%"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAWWIDTH%#", $this->getDataOptsVal("data-wwidth", "90%"));
		Layout::parseTemplate($this->_sTemplate, "#%DATAICON%#", $this->getDataOptsVal("data-icon", "glyphicon glyphicon-cog"));
		
		Layout::parseTemplate($this->_sTemplate, "#%VALUE%#", $this->_sValue);
	
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
			<div class="input-group">
				<button
					type="button"
					id="#%ID%#Btn"
					title="#%TITLE%#"
					class="openPopUpBtn #%CLASS%#"
					data-wname="#%DATAWNAME%#"
					data-url="#%DATAURL%#"
					data-params="#%DATAPARAMS%#"
					data-wheight="#%DATAWHEIGHT%#"
					data-wwidth="#%DATAWWIDTH%#">
						<i id="#%NAME%#Icon" class="#%DATAICON%#"></i>
						#%VALUE%#
				</button>
				<i class="requiredMark"></i>
				<span class="hintHelp"></span>
			</div>
		</div>';
		
		return $lsEcho;
	}
}
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

class table extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	public function renderTable ($poElement)
	{
		$laOptions = $poElement->getOption('table');
		$laAttr = $poElement->getAttributes();
	
		$laElementName = $poElement->getName();
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho = '<div class="input-form input-form-sm col-lg-12">';
		$lsEcho .= '	<input type="hidden" id="'.$poElement->getAttribute('id').'" name="'.$poElement->getName().'" value="'.$poElement->getValue().'" '.($poElement->getAttribute('required') ? 'required="required"' : "").' >';
		$lsEcho .= '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label>';
		$lsEcho .= '	<div class="formTable table-responsive">';
		$lsEcho .= '
							<table class="table table-hover table-condensed">
								<thead>
									<tr class="active">';
	
		if (isset($laOptions['data-column']))
		{
			foreach ($laOptions['data-column'] as $lsTitle)
			{
				$lsEcho .= '<th>' . $lsTitle . '</th>';
			}
		}
	
		$lsEcho .= '
									</tr>
								</thead>
								<tbody id="table-' . $laElementName . '">
								</tbody>
							</table>';
		$lsEcho .=  '		<div id="script-' . $laElementName . '">
							</div>';
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
		$laColumn = $this->getDataOptsVal('data-column');
		$lsTableTdArea = "";
		
		if (is_array($laColumn))
		{
			foreach ($laColumn as $lsTitle)
			{
				$lsTableTdArea .= '<th>' . $lsTitle . '</th>';
			}
		}
	
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%FOR%#", $this->getOptsVal('for'));
		Layout::parseTemplate($this->_sTemplate, "#%LABEL%#", $this->getOptsVal('label'));
		
		Layout::parseTemplate($this->_sTemplate, "#%ID%#", $this->getAttrVal("id"));
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		Layout::parseTemplate($this->_sTemplate, "#%VALUE%#", $this->_sValue);
		Layout::parseTemplate($this->_sTemplate, "#%REQUIRED%#", $this->getAttrVal("required"));
		
		Layout::parseTemplate($this->_sTemplate, "#%TABLETDAREA%#", $lsTableTdArea);
	
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
			<input 
				type="hidden" 
				id="#%ID%#" 
				name="#%NAME%#" 
				value="#%VALUE%#" 
				#%REQUIRED%#>
			<label for="#%FOR%#">#%LABEL%# </label>
			<div class="formTable table-responsive">
				<table class="table table-hover table-condensed">
					<thead>
						<tr class="active">	
							#%TABLETDAREA%#
						</tr>
					</thead>
					<tbody id="table-#%NAME%#">
					</tbody>
				</table>
				<div id="script-#%NAME%#">
				</div>
			</div>
		</div>';
	
		return $lsEcho;
	}
}
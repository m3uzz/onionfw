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

class Display extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poView
	 * @param object $poElement
	 * @return string
	 */
	public function renderDisplay ($poView, $poElement)
	{
		$lsEcho = '';
	
		$lsIcon = $poElement->getOption('icon');
	
		if (!empty($lsIcon))
		{
			$lsIcon = '<i class="' . $lsIcon . '"></i> ';
		}
	
		$lsLabel = $poElement->getOption('label');
	
		if (!empty($lsLabel))
		{
			$lsLabel = '	<label for="'.$poElement->getOption('for').'">'.$poElement->getOption('label').' </label><br/>';
			$poElement->setLabel('');
		}
	
		$lsElementName = $poElement->getName();
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho .= '<div class="input-form input-form-sm col-lg-'.$lnColLength.'">';
		$lsEcho .= 		$lsLabel;
		$lsEcho .=		$lsIcon;
		$lsEcho .= '	<span id="'.$lsElementName.'Span" class="input-display">';
		$lsEcho .= 			$poView->formRow($poElement);
		$lsEcho .= '	</span>';
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
		Layout::parseTemplate($this->_sTemplate, "#%COLLENGTH%#", $this->_nColLength);
		Layout::parseTemplate($this->_sTemplate, "#%LABELAREA%#", $this->getLabelArea());
		Layout::parseTemplate($this->_sTemplate, "#%ICONAREA%#", $this->getIconArea());
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);		
		
		Layout::parseTemplate($this->_sTemplate, "#%ELEMENT%#", $poView->formRow($this->_oElement));
		
		return $this->_sTemplate;
	}

	
	/**
	 * 
	 * @return void|string
	 */
	public function getLabelArea ()
	{
		$lsLabel = $this->getOptsVal('label');
		
		if (!empty($lsLabel))
		{
			$lsFor = $this->getOptsVal('for');
			$this->_oElement->setLabel('');
			
			return '<label for="' . $lsFor . '">' . $lsLabel . ' </label><br/>';
		}
		
		return;
	}
	
	
	/**
	 *
	 * @return string
	 */
	public function getDefaultTemplate ()
	{
		$lsEcho = '
		<div class="input-form input-form-sm col-lg-#%COLLENGTH%#">
		 	#%LABELAREA%#
			#%ICONAREA%#
			<span id="#%NAME%#Span" class="input-display">
		 		#%ELEMENT%#
			</span>
		</div>';
	
		return $lsEcho;
	}
}
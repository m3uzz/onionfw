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

class Text extends ElementAbstract
{
	
	/**
	 *
	 * @param object $poView
	 * @param object $poElement
	 * @return string
	 */
	public function renderText ($poView, $poElement)
	{
		$lsEcho = '';
	
		$lsElementName = $poElement->getName();
	
		$lsIcon = $poElement->getOption('icon');
	
		if (!empty($lsIcon))
		{
			$lsIcon = '<i id="'.$lsElementName.'Icon" class="' . $lsIcon . '"></i> ';
		}
	
		$lnColLength = $poElement->getOption('length');
	
		if (empty($lnColLength))
		{
			$lnColLength = $this->_nColLength;
		}
	
		$lsEcho .= '<div id="'.$lsElementName.'DivText" class="input-form input-form-sm input-text col-lg-'.$lnColLength.'">';
		$lsEcho .=		$lsIcon;
		$lsEcho .= '	<span id="'.$lsElementName.'" class="input-text-span '.$poElement->getOption('class').'">';
		$lsEcho .= 			$poElement->getOption('text');
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
		Layout::parseTemplate($this->_sTemplate, "#%CLASS%#", $this->getOptsVal('class'));
		Layout::parseTemplate($this->_sTemplate, "#%ICONAREA%#", $this->getIconArea());
		Layout::parseTemplate($this->_sTemplate, "#%NAME%#", $this->_sName);
		Layout::parseTemplate($this->_sTemplate, "#%TEXT%#", $this->getOptsVal('text'));
		
		return $this->_sTemplate;
	}

	
	/**
	 *
	 * @return string
	 */
	public function getDefaultTemplate ()
	{
		$lsEcho = '
		<div id="#%NAME%#DivText" class="input-form input-form-sm input-text col-lg-#%COLLENGTH%#">
			#%ICONAREA%#
			<span id="#%NAME%#" class="input-text-span #%CLASS%#">
		 		#%TEXT%#
			</span>
		</div>';
		
		return $lsEcho;
	}
}
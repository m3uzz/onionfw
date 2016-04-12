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

class FieldSet extends ElementAbstract
{
	/**
	 *
	 * @param object $poView
	 * @return string
	 */
	public function render ($poView = null)
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
	 * @return string
	 */
	public function getDefault ()
	{
		$lsEcho = '
		';
	
		return $lsEcho;
	}
}
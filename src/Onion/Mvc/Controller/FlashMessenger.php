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

namespace Onion\Mvc\Controller;
use \Zend\Mvc\Controller\Plugin as Zend;
use Onion\Config\Config;
use Onion\Log\Debug;
use Onion\I18n\Translator;
use Onion\Json\Json;


class FlashMessenger extends Zend\FlashMessenger
{
	
	public function testMessage ()
	{
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'danger', 'msg'=>'aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa aaaaaaaaaaaaaaaaaaaaaa'));
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'warning', 'msg'=>'bbbbbbbbbbbbbbbbbbbbbb'));
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'success', 'msg'=>'cccccccccccccccccccccc'));
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'primary', 'msg'=>'dddddddddddddddddddddd'));
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'info', 'msg'=>'eeeeeeeeeeeeeeeeeeeeee'));
		$this->addMessage(array('id'=>$this->get('_sModule') . '-' . microtime(true), 'hidden'=>$this->get('_bHiddenPushMessage'), 'push'=>$this->get('_bPushMessage'), 'type'=>'default', 'msg'=>'ffffffffffffffffffffff'));
	}
	
	static public function pushMessages ()
	{
		$laMessages = '';
	
		if (PUSH_MESSAGE)
		{
			$loContainer = $_SESSION['FlashMessenger'];
				
			if (is_object($loContainer))
			{
				if ($loContainer->offsetExists('default'))
				{
					$loNamespace = $loContainer->offsetget('default');
						
					if (!$loNamespace->isEmpty())
					{
						$laMsgs = array_reverse($loNamespace->toArray(), true);
	
						foreach ($laMsgs as $lnKey => $laMessage)
						{
							if ($laMessage['push'])
							{
								$laMessages[$lnKey] = $laMessage;
	
								if ($loNamespace->offsetExists($lnKey))
								{
									$loNamespace->offsetUnset($lnKey);
								}
							}
						}
					}
				}
			}
		}
	
		return Json::encode($laMessages);
	}
	
	static public function clearFlashAction ($psId = null)
	{
		if ($this->hasMessages()) {
			if (null === $psId)
			{
            	unset($this->messages[$this->getNamespace()]);
			}
			else 
			{
				foreach ($this->messages[$this->getNamespace()] as $lnKey => $laMessage)
				
				if ($laMessage['id'] === $psId)
				{
					unset($this->messages[$this->getNamespace()][$lnKey]);
				}
			}

            return true;
        }

        return false;
	}

	static public function renderFlashMessenger ()
	{
		$lsMsg = '';
		
		if(!PUSH_MESSAGE)
		{
			$laMessages = $this->getCurrentMessages();
				
			if (is_array($laMessages))
			{
				foreach ($laMessages as $lnKey => $laMessage)
				{
					$lsMsg .= '<blockquote id="' . $laMessage['id'] . '" class="alert-dismissable alert alert-' . $laMessage['type'] . '">';
					$lsMsg .= '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
					$lsMsg .= $laMessage['msg'];
					$lsMsg .= '</blockquote>';
				}
			}
		}
		
		return $lsMsg;
	}	
}
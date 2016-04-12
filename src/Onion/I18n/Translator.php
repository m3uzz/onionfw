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

namespace Onion\I18n;

use \Zend\I18n\Translator as Zend;
use Onion\Config\Config;
use Onion\Log\Debug;
use Locale;

class Translator extends Zend\Translator
{
	/**
	 * 
	 * @param string $psMsg
	 */
	public static function i18n($psMsg)
	{
		return $psMsg;
		
		$laTranslator = Config::getAppOptions('translator');

		$loI18n = new self;
		$loI18n->setLocale($laTranslator['locale']);
		$loI18n->addTranslationFilePattern($laTranslator['type'], $laTranslator['base_dir'], $laTranslator['pattern']);

		return $loI18n->translate($psMsg);
	}
	


	/**
	 *
	 * @param string $psDate
	 * @return string
	 */
	public static function dateP2S ($psDate)
	{
		$laOptions = Config::getAppOptions('translator');
	
		$laDate = explode(' ', $psDate);

		if (isset($laDate[0]) && !empty($laDate[0]))
		{
			$lsDate = preg_replace('/\//', '-', $laDate[0]);
			$lsDate = date('Y-m-d', strtotime($lsDate));
			$lsDate .= isset($laDate[1]) ? " {$laDate[1]}" : '';

			return $lsDate;
		}
		
		return $psDate;
	}
	
	
	/**
	 *
	 * @param string $psDate
	 * @return string
	 */
	public static function dateS2P ($psDate)
	{
		$laOptions = Config::getAppOptions('translator');
	
		$laDate = explode(' ', $psDate);
	
		if (isset($laDate[0]) && !empty($laDate[0]))
		{
			$lsDate = date($laOptions['dateFormat'], strtotime($laDate[0]));
			$lsDate .= isset($laDate[1]) ? " {$laDate[1]}" : '';
			
			return $lsDate;
		}
		
		return $psDate;
	}
}
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

namespace Onion\Template;
use Onion\Lib\UrlRequest;
use Onion\File\System;
use Onion\Log\Debug;
use Onion\Config\Config;

class Layout
{

	/**
	 *
	 * @var array
	 */
	private $_aHead;

	/**
	 *
	 * @var array
	 */
	private $_aHeader;

	/**
	 *
	 * @var array
	 */
	private $_aNav;

	/**
	 *
	 * @var array
	 */
	private $_aContent;

	/**
	 *
	 * @var array
	 */
	private $_aSideBar;

	/**
	 *
	 * @var array
	 */
	private $_aFooter;

	/**
	 * 
	 * @var string
	 */
	private $_sHTTPHeaderCode = "200";

	/**
	 * Carrega a template a ser utilizada
	 * 
	 * @param string $psTemplate        	
	 * @param boolean $psTraduzir        	
	 * @return string
	 */
	public static function getTemplate ($psTemplate)
	{
		if (file_exists(TEMPLATE_APATH . $psTemplate))
		{
			$lsBuffer = file_get_contents(TEMPLATE_APATH . $psTemplate);
		}

		return $lsBuffer;
	}

	/**
	 * Função pouco utilizada, substituida por get_template()
	 * 
	 * @param string $psUrl        	
	 * @param string $psTemplate        	
	 * @return string
	 */
	public static function getTemplateExterno ($psUrl, $psTemplate)
	{
		$laLk = parse_url($psUrl);
		
		if ($laLk['scheme'] == "http")
		{
			return UrlRequest::urlRequest($psUrl . $psTemplate);
		}
		else
		{
			return System::localRequest($psTemplate);
		}
	}

	/**
	 * Substitui na varíavel Subject, todas as marcações Pattern por Replace.
	 * 
	 * @param string $psSubject        	
	 * @param string $psPattern        	
	 * @param string $psReplace        	
	 * @param string $psMod        	
	 */
	public static function parseTemplate (&$psSubject, $psPattern, $psReplace, $psMod = "")
	{
		$lsP = "/" . $psPattern . "/" . $psMod;
		$psSubject = preg_replace($lsP, $psReplace, $psSubject);
	}

	/**
	 * Carrega o bloco do Template que está entre a Pattern.
	 * Ex.: <!--BLOCO-->*.*<!--/BLOCO--> Nesse exemplo, usando a Pattern BLOCO
	 * ele irá carregar o bloco que está entre as marcações
	 * 
	 * @param string $psArquivo        	
	 * @param string $psPattern        	
	 * @return string
	 */
	public static function getBlock ($psArquivo, $psPattern)
	{
		$lsP = "/<!--" . $psPattern . "-->(.*)<!--\/" . $psPattern . "-->/s";
		preg_match_all($lsP, $psArquivo, $laSubject);
		
		return trim($laSubject[1][0]);
	}
}
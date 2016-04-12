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

namespace Onion\Lib;


class Sitemap
{
	public $coDocument;

	public $cbLoadOK;

	public $csXmlEncoding = "UTF-8";

	public $csChangeFreq = "monthly";

	public $csPriority = "0.5";


	public function __construct()
	{
		$this->coDocument = new DOMDocument();
	}

	/**
	 * Executa comandos no shell
	 * @param string $psComando string a ser executada no sistema
	 * @return void
	 */
	public function executa($psComando, $ps2comando="")
	{
		$laOutput = array();
		$lnReturn = 0;

		$lsSaida = exec($psComando." 2>&1 ".$ps2comando, $laOutput, $lnReturn);

		if($lnReturn != 0)
		{
			die("Command Line error: ".$lsSaida);
		}

		return $laOutput;
	}

	/**
	 * Grava arquivo no sistema
	 * @param string $psLinha string que será gravada no arquivo
	 * @param string $psDir diretorio onde o arquivo será gravado
	 * @param string $psModo modo de gravação no arquivo (BOF = inicio, EOF = final, NEW = novo arquivo)
	 * @return void
	 */
	public function grava_arq($psNomeArq, $psConteudo, $psModo="NEW")
	{
		if($psModo === "EOF")
		{
			$lsComando = "echo '$psConteudo' >> $psNomeArq";
		}
		elseif($psModo === "BOF")
		{
			$lsArq = exec("cat ".$psNomeArq);
			$lsComando = "echo '$psConteudo' > $psNomeArq; echo '$lsArq' >> $psNomeArq";
		}
		else
		{
			$lsComando = "echo '$psConteudo' > $psNomeArq";
		}

		self::executa($lsComando);
	}

	/**
	 *
	 * @param string $psXml
	 * @param string $psMsgErro
	 * @return boolean
	 */
	public function xmlOpen($psXml, $psMsgErro="XML error")
	{
		if(!$this->coDocument->loadXML($psXml, LIBXML_COMPACT))
		{
			$this->cbLoadOK = false;
			return false;
		}
		else
		{
			$this->cbLoadOK = true;
			return true;
		}
	}

	/**
	 *
	 * @param string $psTag
	 * @param object $poObject
	 * @return object
	 */
	public function xmlById($psTag, &$poObject=null)
	{
		$loList = $this->xmlByTagName($psTag, $poObject);
		return $this->xmlGetItem($loList);
	}

	/**
	 *
	 * @param string $psTag
	 * @param object $poObject
	 * @return object|NULL
	 */
	public function xmlByTagName($psTag, &$poObject=null)
	{
		if($poObject==null)
		{
			$poObject = &$this->coDocument;
		}

		if($this->cbLoadOK)
		{
			$loList = $poObject->getElementsByTagName($psTag);

			if(is_object($loList) && $loList->length != 0)
			{
				return $loList;
			}
			else
			{
				return null;
			}
		}
	}

	/**
	 *
	 * @param object $poNodeList
	 * @param string $psIndex
	 * @return NULL|object
	 */
	public function xmlGetItem(&$poNodeList, $psIndex=0)
	{
		if(is_object($poNodeList))
		{
			return $poNodeList->item($psIndex);
		}
		else
		{
			return null;
		}
	}

	/**
	 *
	 * @param object $poElement
	 * @param string $psAttribute
	 * @return string|void
	 */
	public function xmlGetAttribute(&$poElement, $psAttribute)
	{
		if($poElement->hasAttribute($psAttribute)){
			return utf8_decode($poElement->getAttribute($psAttribute));
		}
	}

	/**
	 *
	 * @param object $poElement
	 * @return string
	 */
	function xmlGetValue(&$poElement)
	{
		return str_replace("&amp;", "&", urldecode(utf8_decode((is_object($poElement) ? $poElement->nodeValue : ""))));
	}

	/**
	 *
	 * @param string $psElement
	 * @param string $psValor
	 * @param array $paAttr
	 * @return string
	 */
	public function setElement($psElement, $psValor, array $paAttr = null)
	{
		$lsAttributs = $this->setAttribut($paAttr);

		return "<$psElement $lsAttributs>\n$psValor\n</$psElement>\n";
	}

	/**
	 *
	 * @param array $paAttr
	 * @return string
	 */
	public function setAttribut(array $paAttr = null)
	{
		$lsAttributs = "";

		if(is_array($paAttr))
		{
			foreach($paAttr as $lsAttr => $lsValue)
			{
				$lsAttributs .= $lsAttr.'="'.$lsValue.'" ';;
			}
		}

		return $lsAttributs;
	}

	/**
	 * @param string $psValor
	 * @param string $psCampo
	 * @param string $psTipo
	 * @return string
	 */
	public function cdata($psValor, $psCampo='', $psTipo='')
	{
		$lnLen = strlen($psValor);

		for($lnX = 0; $lnX<$lnLen; $lnX++)
		{
			if(ord($psValor[$lnX]) < 32)
			{
				$psValor[$lnX] = " ";
			}
		}

		if($psTipo == "rss")
		{
			if($psCampo == "description" || $psCampo == "title" || $psCampo == "author")
			{
				return "<![CDATA[".$psValor."]]>";
			}
			else
			{
				return preg_replace("/&/", "&amp;", $psValor);
			}
		}
		elseif(!empty($psValor))
		{
			return "<![CDATA[$psValor]]>";
		}
	}

	/**
	 *
	 * @param array $paLinks
	 * @return array
	 */
	public function setSitemap(array $paLinks = array())
	{
		$lnId0 = 0;

		$laSitemap['header'] = '<?xml version="1.0" encoding="'.$this->csXmlEncoding.'"?>';
		$laSitemap[$lnId0]['conteiner'] = 'urlset';
		$laSitemap[$lnId0]['attr']['xmlns:xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
		$laSitemap[$lnId0]['attr']['xsi:schemaLocation'] = 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';
		$laSitemap[$lnId0]['attr']['xmlns'] = 'http://www.sitemaps.org/schemas/sitemap/0.9';

		$lnId1 = 0;

		foreach($paLinks as $lnKey => $lsLink)
		{
			$laItem['conteiner'] = 'url';
			$laValue[0]['conteiner'] = 'loc';
			$laValue[0]['value'] = $lsLink;
			$laValue[1]['conteiner'] = 'lastmod';
			$laValue[1]['value'] = date('Y-m-dTH:i:s+00:00');
			$laValue[2]['conteiner'] = 'changefreq';
			$laValue[2]['value'] = $this->csChangeFreq;
			$laValue[3]['conteiner'] = 'priority';
			$laValue[3]['value'] = $this->csPriority;
				
			$laItem['value'] = $laValue;
			$laSitemap[$lnId0]['value'][$lnId1] = $laItem;
				
			$lnId1++;
		}

		return $laSitemap;
	}

	/**
	 *
	 * @param array $paSitemap
	 * @return string
	 */
	public function generateSitemap(array $paSitemap)
	{
		$lsSitemapXml = "";

		if(is_array($paSitemap))
		{
			if(isset($paSitemap['header']))
			{
				$lsSitemapXml = $paSitemap['header'] . "\n";
				unset($paSitemap['header']);
			}
				
			if(is_array($paSitemap))
			{
				foreach($paSitemap as $lnK => $laItem)
				{
					$lsSitemapXml .= $this->setXml($laItem);
				}
			}
		}

		return $lsSitemapXml;
	}

	/**
	 *
	 * @param array $paValue
	 * @return string|null
	 */
	public function setXml(array $paItem)
	{
		$laAttr = null;
		$lsConteiner = null;
		$lsValue = null;
			
		if(isset($paItem['conteiner']))
		{
			$lsConteiner = $paItem['conteiner'];
		}

		if(isset($paItem['attr']) && is_array($paItem['attr']))
		{
			$laAttr = $paItem['attr'];
		}

		if(isset($paItem['value']))
		{
			if(is_array($paItem['value']))
			{
				foreach($paItem['value'] as $lnK => $laItem)
				{
					$lsValue .= $this->setXml($laItem);
				}
			}
			else
			{
				$lsValue = $paItem['value'];
			}
		}

		return $this->setElement($lsConteiner, $lsValue, $laAttr);
	}

	/**
	 *
	 * @param string $psXML
	 */
	public function renderSitemap($psXML)
	{
		header("Content-type: text/xml; charset=".$this->csXmlEncoding);
		echo $psXML;
	}

	/**
	 * @param string $psLinha
	 * @param string $psDir
	 * @param string $psArq
	 */
	public function grava_arq_rss($psLinha, $psDir, $psArq)
	{
		$laDir = explode("/", $psDir);

		if(empty($laDir[0]))
		{
			unset($laDir[0]);
			$laDir[1] = DS.$laDir[1];
		}
		else
		{
			$laDir[0] = BASE_PATH.$laDir[0];
		}

		$lsArquivo = $psArq;
		$lnTam = strlen($psLinha);
		$psLinha = str_replace("'", "`", $psLinha);

		if($lnTam > 125000)
		{
			$lsAux = substr($psLinha, 0, 125000);
			passthru("echo '$lsAux' > ".$lsArquivo);

			$lnVezes = (int)($lnTam/125000);
				
			for($index = 1; $index <= $lnVezes; $index++)
			{
				if($index == $lnVezes){
					$lsAux = rtrim(substr($psLinha, $index*125000, 125000));
				}
				else
				{
					$lsAux = substr($psLinha, $index*125000, 125000);
				}

				passthru("echo '$lsAux' >> ".$lsArquivo);
			}
		}
		else
		{
			passthru("echo '$psLinha' > ".$lsArquivo);
		}
	}
}
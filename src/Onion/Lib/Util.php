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
use Onion;
use Onion\Json\Json;
use Onion\Log\Event;
use Onion\File\System;

class Util
{

	/**
	 * getImage: devolve a imagem solicitada.
	 *
	 * @param string $psPath        	
	 * @param string $psFile        	
	 */
	public static function getImage ($psFile, $psTamanho = null, $pnId = null, $psTitle = null, $psAlt = null, $psClass = null, $pbReturnPath = false)
	{
		// recupera o diretório da imagem
		$targetDir = System::getBalancedDir($psFile);
		$lsNameImage = preg_replace('/^(.+\/)?(.+)\.[^\.]+$/', '$2', $psFile);
		$ext = preg_replace('/^.+\.([^\.]+)$/', '$1', $psFile);
		$source = (! empty($psTamanho) ? $targetDir . $lsNameImage . '_' . $psTamanho . '.' . $ext : $targetDir . $lsNameImage . '.' . $ext);
		$class = (! empty($psClass) ? 'class="' . $psClass . '"' : '');
		$id = (! empty($pnId) ? 'id="' . $pnId . '"' : '');
		$title = (! empty($psTitle) ? 'title="' . $psTitle . '"' : '');
		$alt = (! empty($psAlt) ? 'alt="' . $psAlt . '"' : '');
		
		// Log\Debug::debug($source);
		
		$lsFilePath = realpath(CLIENT_PATH . $source);
		
		if (file_exists($lsFilePath))
		{
			if (! $pbReturnPath)
			{
				// cria um elemento <img> com o caminho da imagem solicitada
				$img = '<img src="' . $source . '"  ' . $id . ' ' . $title . ' ' . $alt . ' ' . $class . '/>';
			}
			else
			{
				$img = $source;
			}
			
			return $img;
		}
		else
		{
			Log\Debug::debug("A imagem solicitada '" . $targetDir . $source . "' não foi encontrada.");
			return null;
		}
	}

	/**
	 * Retorna instancia Dao do módulo solicitado (persistência)
	 *
	 * @param string $psClass        	
	 *
	 * @return object
	 */
	public static function getDao ($psClass, $psModule = null)
	{
		try
		{
			// Recuperando dados da Config
			$loConfig = new Zend_Config_ini(APP_CONFIG, APPLICATION_ENV);
			$loDao = null;
			
			if ($psModule === null)
			{
				$psModule = "Backend";
			}
			
			// Persistência no banco de dados (verificando adaptador)
			switch ($loConfig->resources->db->adapter)
			{
				case 'PDO_MYSQL':
					$prefixPdo = $psModule . '_Model_Dao_Pdo_Mysql_Dao';
					
					if ($psClass)
					{
						$lsClass = $prefixPdo . $psClass;
						
						if (class_exists($lsClass))
						{
							$loDao = new $lsClass();
						}
						else
						{
							throw new Exception("Classe " . $lsClass . " não existe!");
						}
					}
					break;
			}
			
			return $loDao;
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Retorna instancia de objeto Model do módulo solicitado
	 *
	 * @param string $psClass        	
	 * @param int $pnId        	
	 *
	 * @return object
	 */
	public static function getModel ($psClass, $pnId, $psModule = null)
	{
		try
		{
			if ($psClass)
			{
				if ($psModule === null)
				{
					$psModule = "Backend";
				}
				
				$prefix = $psModule . '_Model_';
				$lsClass = $prefix . $psClass;
				
				if (is_array($pnId))
				{
					$laPK = $pnId; // parametros para setar objeto
				}
				else
				{
					$laPK = array(
						'id' => $pnId
					); // parametros para setar objeto
				}
				
				$loClass = null;
				
				if (class_exists($lsClass))
				{
					$loClass = new $lsClass($laPK);
				}
				else
				{
					throw new Exception("Classe " . $lsClass . " não existe!");
				}
				
				return $loClass;
			}
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Retorna instancia de objeto Form do módulo solicitado
	 * 
	 * @param string $psClass        	
	 *
	 * @return object
	 */
	public static function getForm ($psClass, $psModule = null)
	{
		try
		{
			
			if ($psClass)
			{
				if ($psModule === null)
				{
					$psModule = "Backend";
				}
				
				$prefix = $psModule . '_Form_';
				$lsClass = $prefix . $psClass;
				$loClass = null;
				
				if (class_exists($lsClass))
				{
					$loClass = new $lsClass();
					
					$laFormConfig = Register::getConfigMerged("form.ini", null, false);
					
					$lsMod = Zend_Controller_Front::getInstance()->getRequest()->getParam('mod');
					
					if (isset($lsMod))
					{
						$lsMod = Zend_Controller_Front::getInstance()->getRequest()->getParam('mod');
					}
					else
					{
						$lsMod = 'default';
					}
					
					if (isset($laFormConfig[$psModule . $psClass][$lsMod]) && is_array($laFormConfig[$psModule . $psClass][$lsMod]))
					{
						foreach ($laFormConfig[$psModule . $psClass][$lsMod] as $lsField => $laPropertie)
						{
							if (isset($laPropertie['disable']) && self::toBoolean($laPropertie['disable']))
							{
								$loClass->removeElement($lsField);
							}
							else
							{
								unset($laPropertie['disable']);
								
								$loElement = null;
								$loElement = $loClass->getElement($lsField);
								
								if ($loElement != null && is_array($laPropertie))
								{
									foreach ($laPropertie as $lsProp => $lsValue)
									{
										if (method_exists($loElement, $lsProp))
										{
											
											if ($lsValue == "false" || $lsValue == "true")
											{
												$lsValue = self::toBoolean($lsValue);
											}
											
											if ($lsProp == "setAttrib")
											{
												if (is_array($lsValue))
												{
													foreach ($lsValue as $lsAttrib => $lsV)
													{
														$loElement->setAttrib($lsAttrib, $lsV);
													}
												}
											}
											else
											{
												$loElement->$lsProp($lsValue);
											}
										}
									}
								}
							}
						}
					}
				}
				else
				{
					throw new Exception("Classe " . $lsClass . " não existe!");
				}
				
				return $loClass;
			}
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Retorna instancia de objeto Service do módulo solicitado
	 *
	 * @param string $psClass        	
	 * @param string $psModule        	
	 */
	public static function getService ($psClass, $psModule)
	{
		try
		{
			if (! empty($psClass) && ! empty($psModule))
			{
				$prefix = $psModule . '_Service_';
				$lsClass = $prefix . $psClass;
				
				if (class_exists($lsClass))
				{
					$loClass = new $lsClass($laPK);
				}
				else
				{
					throw new Exception("Classe " . $lsClass . " não existe!");
				}
				
				return $loClass;
			}
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Retorna objeto encode json
	 * 
	 * @param array $paResource        	
	 * @return object encode json
	 */
	public static function getJSON (array $paResource)
	{
		try
		{
			header('Content-type: application/json');
			echo $loRegistro = Json::encode($paResource);
			exit(0);
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Retorna a Mensagem no padrão da Aplicação
	 *
	 * @param string $psLabel        	
	 * @param string $psType        	
	 * @return object encode json
	 */
	public static function getMessage ($psLabel, $psType, $paParams = null)
	{
		try
		{
			switch (strtoupper($psType))
			{
				case 'ERROR':
				case 'SUCCESS':
				case 'NOTLOGED':
				case 'FORBIDDEN':
					
					if (strtoupper($psType) != 'SUCCESS')
					{
						Event::log(array(
							"userId" => null,
							"class" => "Util",
							"method" => "getMessage",
							'Mensagem' => $psLabel,
							'Tipo' => strtoupper($psType)
						), Event::ERR);
					}
					
					$mensagem = Json::encode(
							array(
								'mensagem' => array(
									'label' => $psLabel,
									'type' => strtoupper($psType),
									'params' => $paParams
								)
							));
					
					break;
				
				default:
					
					throw new Exception("Tipo de mensagem " . $psType . " não está definido!");
					
					break;
			}
			
			return $mensagem;
		}
		catch (Zend_Exception $e)
		{
			die($e->__toString());
		}
	}

	/**
	 * Tratamento de entrada de dados para o BD
	 *
	 * @param string $psString        	
	 */
	public static function escapeString ($psString)
	{
		$loConfig = new Zend_Config_ini(APP_CONFIG, APPLICATION_ENV);
		$lsString = NULL;
		
		// Persistência no banco de dados (verificando adaptador)
		switch ($loConfig->resources->db->adapter)
		{
			case 'PDO_MYSQL':
				// TODO COLOCAR VALIDAÇÃO ESPECÍFICA PARA O TIPO DE BANCO
				
				$laSearch = array(
					"\\",
					"\0",
					"\n",
					"\r",
					"\x1a",
					"'",
					'"'
				);
				$laReplace = array(
					"\\\\",
					"\\0",
					"\\n",
					"\\r",
					"\Z",
					"\'",
					'\"'
				);
				
				$lsString = str_replace($laSearch, $laReplace, $psString);
				
				break;
		}
		
		return $lsString;
	}

	/**
	 *
	 * @param
	 *        	string &$psSubject
	 * @param string $psPattern        	
	 * @param string $psReplace        	
	 * @param string $psMod        	
	 * @return void
	 */
	public static function parse (&$psSubject, $psPattern, $psReplace, $psMod = "")
	{
		$lsP = "/" . $psPattern . "/" . $psMod;
		$psSubject = preg_replace($lsP, $psReplace, $psSubject);
	}

	/**
	 *
	 * @param string $psCookie_name        	
	 * @param string $psCookie_value        	
	 * @param int $pnTempo        	
	 */
	public static function storeCookie ($psCookie_name, $psCookie_value, $pnTempo)
	{
		setcookie("$psCookie_name", "$psCookie_value", "$pnTempo", "$SCRIPT_FILENAME", "$HTTP_HOST");
	}

	/**
	 *
	 * @return number
	 */
	public static function getMicrotime ()
	{
		list ($lnUsec, $lnSec) = explode(" ", microtime());
		return ((float) $lnUsec + (float) $lnSec);
	}

	/**
	 * Setar data no formato para inserção no BD
	 *
	 * @param string $psData        	
	 */
	public static function setDateBD ($psData)
	{
		$lsData = implode("-", array_reverse(explode("/", $psData)));
		return $lsData;
	}

	/**
	 * Verifica se o valor de uma variável é true ou false e retorna booleano
	 *
	 * @param string|int|boolean $psVar        	
	 * @return boolean
	 */
	public static function toBoolean ($psVar)
	{
		if ($psVar === "0" || $psVar === 0 || $psVar === false || $psVar === "false" || $psVar === "")
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function clearCache ()
	{
		// TODO: Define CACHE_APATH
		Log\Debug::debug('rm ' . CACHE_APATH . DS . 'ONION_Cache---*');
		system('rm ' . CACHE_APATH . DS . 'ONION_Cache---*');
	}
}
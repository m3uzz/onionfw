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
use Onion\Config\Config;

class String
{
	
	/**
	 * 
	 * @return string
	 */
	public static function generateDynamicSalt ()
	{
		$lsDynamicSalt = '';
	
		for ($i = 0; $i < 50; $i ++)
		{
			$lsDynamicSalt .= chr(rand(33, 126));
		}
	
		return $lsDynamicSalt;
	}	

	
	/**
	 * 
	 * @param string $psPassword
	 * @param string $psDynamicSalt
	 * @return string
	 */
	public static function encriptPassword ($psPassword, $psDynamicSalt)
	{
		$laOptions = Config::getAppOptions('settings');
		
		if ($laOptions['criptPassword'])
		{
			$psPassword = md5($laOptions['staticSalt'] . $psPassword . $psDynamicSalt);
		}
		
		return $psPassword;
	}


	/**
	 * geraSenha: gera uma senha aleatória de 6 digitos, contendo numeros e
	 * letras maiúsculas e minúsculas
	 *
	 * @return string
	 */
	public static function generatePassword ()
	{
		// Caracteres de cada tipo
		$lsLower = 'abcdefghijklmnopqrstuvwxyz';
		$lsUpper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$lsNum = '1234567890';
		
		$lnRand = mt_rand(1, 26);
		$laReturn[] = $lsLower[$lnRand - 1];
		$lnRand = mt_rand(1, 26);
		$laReturn[] = $lsLower[$lnRand - 1];
		$lnRand = mt_rand(1, 26);
		$laReturn[] = $lsUpper[$lnRand - 1];
		$lnRand = mt_rand(1, 26);
		$laReturn[] = $lsUpper[$lnRand - 1];
		$lnRand = mt_rand(1, 10);
		$laReturn[] = $lsNum[$lnRand - 1];
		$lnRand = mt_rand(1, 10);
		$laReturn[] = $lsNum[$lnRand - 1];
		
		shuffle($laReturn);
		$lsReturn = implode("", $laReturn);
		
		return $lsReturn;
	}
	

	/**
	 * Retorna a string com a quantidade de palavras desejadas, eliminando o
	 * restante
	 *
	 * @param string $psText
	 *        	string a ser analisada
	 * @param int $pnLimit
	 *        	número de palavras a retornar
	 * @return string
	 */
	public static function limitWords ($psText, $pnLimit)
	{
		$laWords = explode(" ", $psText);
		$lsString = "";
		
		for ($lnIdx = 0; ($lnIdx < count($laWords) && $lnIdx < $pnLimit); $lnIdx ++)
		{
			$lsString .= $laWords[$lnIdx] . " ";
		}
		
		return trim($lsString) . '...';
	}

	
	/**
	 * Retorna a string com o numero de caracteres desejados, mantendo as
	 * palavras inteiras;
	 *
	 * @param string $psText
	 *        	string a ser analisada
	 * @param int $pnSize
	 *        	número de caracteres a serem retornados
	 * @return string
	 */
	public static function limitChars ($psText, $pnSize)
	{
		if (strlen($psText) > 0)
		{
			if (strlen($psText) > intval($pnSize))
			{
				$lsTxt = substr($psText, 0, $pnSize) . '...';
				
				// Evitar que corte a palavra a meio
				$lnPosition = strrpos($psText, " ");
				
				if ($lnPosition !== false)
				{
					$psText = substr($lsTxt, 0, $lnPosition);
				}
			}
		}
		
		return trim($psText);
	}

	
	/**
	 * Trata string longa, quebrando em espaços e cortando de acordo com o
	 * tamanho da linha
	 *
	 * @param string $psTexto
	 *        	Texto a ser analisado
	 * @param int $pnLineSize
	 *        	Tamanho da linha em caracteres
	 *        	$param int $pnRows Numero de linhas
	 * @return string
	 */
	public static function previa ($psTexto, $pnLineSize, $pnRows = 1)
	{
		return self::cutString(self::lineBreak($psTexto, $pnLineSize), $pnLineSize * $pnRows);
	}

	
	/**
	 * Trata texto longo
	 *
	 * @param string $psTexto
	 *        	Texto a ser analisado
	 * @param int $pnLineSize
	 *        	Tamanho da linha em caracteres
	 * @return string
	 */
	public static function cutString ($psTexto, $pnLineSize)
	{
		$lnLineSize = $pnLineSize;
		
		if (strlen($psTexto) > $pnLineSize)
		{
			while ($psTexto[$pnLineSize] != " " && $pnLineSize > 5)
			{
				$pnLineSize --;
			}
			
			if ($pnLineSize == 0)
			{
				$pnLineSize = $lnLineSize;
			}
			
			$lsTexto = substr($psTexto, 0, $pnLineSize - 4) . " ...";
		}
		else
		{
			$lsTexto = $psTexto;
		}
		
		return $lsTexto;
	}
	

	/**
	 * Trata texto longo sem espaçamento
	 *
	 * @param string $psTexto
	 *        	Texto a ser analisado
	 * @param int $pnLineSize
	 *        	Tamanho da linha em caracteres
	 * @return string
	 */
	public static function lineBreak ($psTexto, $pnLineSize)
	{
		if (strlen($psTexto) > $pnLineSize)
		{
			$lsString = "";
			
			for ($x = 0; $x <= strlen($psTexto); $x = $x + $pnLineSize)
			{
				$lsLinha = substr($psTexto, $x, $pnLineSize);
				
				if (strstr($lsLinha, ' ') == '')
				{
					$lsLinha .= " ";
				}
				
				$lsString .= $lsLinha;
			}
			
			$psTexto = $lsString;
		}
		
		return $psTexto;
	}

	
	/**
	 * 
	 * @param string $psTexto
	 * @return string
	 */
	public static function linksTexto ($psTexto)
	{
		if (empty($psTexto))
		{
			return $psTexto;
		}
		
		$lsLinhas = split("\n", $psTexto);
		
		if (strpos($psTexto, "<html>"))
		{
			return $psTexto;
		}
		
		foreach ($lsLinhas as $lsLinha)
		{
			$lsLinha = eregi_replace("([ \t]|^)www.", " http://www.", $lsLinha);
			$lsLinha = eregi_replace("([ \t]|^)ftp.", " ftp://ftp.", $lsLinha);
			$lsLinha = eregi_replace("(http://[^ )\t\r\n]+)", "<a class=\"texto\" href=\"\\\\1\" target=\"_blank\">\\\\1</a>", $lsLinha);
			$lsLinha = eregi_replace("(https://[^ )\t\r\n]+)", "<a class=\"texto\" href=\"\\\\1\" target=\"_blank\">\\\\1</a>", $lsLinha);
			$lsLinha = eregi_replace("(ftp://[^ )\t\r\n]+)", "<a class=\"texto\" href=\"\\\\1\" target=\"_blank\">\\\\1</a>", $lsLinha);
			$lsLinha = eregi_replace("(^[-a-z0-9_]+(.[_a-z0-9-]+)*@([a-z0-9-]+(.[a-z0-9-_]+))$)", "<a class=\"texto\" href=\"mailto:\\\\1\">\\\\1</a>", $lsLinha);
			$lsNovoTexto .= $lsLinha . "\n";
		}
		
		return $lsNovoTexto;
	}

	
	/**
	 * 
	 * @param number $pnValue
	 * @return string
	 */
	public static function valorPorExtenso($pnValue = 0)
	{
		$laSingular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$laPlural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	
		$laCentena = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$laDezena = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
		$laDezVinte = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
		$laUnitario = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
	
		$lnZ = 0;
		$lsRt = "";
	
		$pnValue = number_format($pnValue, 2, ".", ".");
		$laInteiro = explode(".", $pnValue);
	
		for($lnI=0; $lnI<count($laInteiro); $lnI++)
		{
			for($lnII=strlen($laInteiro[$lnI]); $lnII<3; $lnII++)
			{
				$laInteiro[$lnI] = "0" . $laInteiro[$lnI];
			}
		}
	
		// $fim identifica onde que deve se dar junção de centenas por "e" ou por ","
		$lnFim = count($laInteiro) - ($laInteiro[count($laInteiro)-1] > 0 ? 1 : 2);
	
		for ($lnI=0; $lnI<count($laInteiro); $lnI++)
		{
			$pnValue = $laInteiro[$lnI];
			$lsRc = (($pnValue > 100) && ($pnValue < 200)) ? "cento" : $laCentena[$pnValue[0]];
			$lsRd = ($pnValue[1] < 2) ? "" : $laDezena[$pnValue[1]];
			$lsRu = ($pnValue > 0) ? (($pnValue[1] == 1) ? $laDezVinte[$pnValue[2]] : $laUnitario[$pnValue[2]]) : "";
	
			$lsR = $lsRc . (($lsRc && ($lsRd || $lsRu)) ? " e " : "") . $lsRd . (($lsRd && $lsRu) ? " e " : "") . $lsRu;
			$lnT = count($laInteiro) - 1 - $lnI;
			$lsR .= $lsR ? " ".($pnValue > 1 ? $laPlural[$lnT] : $laSingular[$lnT]) : "";
	
			if ($pnValue == "000")
			{
				$lnZ++;
			}
			elseif ($lnZ > 0)
			{
				$lnZ--;
			}
	
			if (($lnT == 1) && ($lnZ > 0) && ($laInteiro[0] > 0))
			{
				$lsR .= (($lnZ>1) ? " de " : "") . $laPlural[$lnT];
			}
	
			if ($lsR)
			{
				$lsRt = $lsRt . ((($lnI > 0) && ($lnI <= $lnFim) && ($laInteiro[0] > 0) && ($lnZ < 1)) ? ( ($lnI < $lnFim) ? ", " : " e ") : " ") . $lsR;
			}
		}
	
		return ($lsRt ? $lsRt : "zero");
	}
	
	
	/**
	 * 
	 * @param number $pnValue
	 * @param number $pnDec
	 * @param boolean $pbRound
	 * @return string|NULL
	 */
	public static function currenceFormat ($pnValue, $pnDec = 2, $pbRound = false)
	{
		if (!empty($pnValue))
		{
			preg_match("/([\d\.,-]+)/", $pnValue, $laValue);
			$lsValue = preg_replace("/,/", "", $laValue[0]);
			
			if ($pbRound)
			{
				$lsValue = round($lsValue, $pnDec);
			}
			
			$laValue = explode(".", $lsValue);
			$lsInt = $laValue[0];
			$lsDec = isset($laValue[1]) ? $laValue[1] : "00";
			
			$laInt = str_split($lsInt);
			$lsSignal = "";
			
			if (isset($laInt[0]) && ($laInt[0] == "-"))
			{
				$lsSignal = $laInt[0];
				unset($laInt[0]);
			}
			
			$lsIntX = "";
			
			if (is_array($laInt))
			{
				$laInt = array_reverse($laInt);
				$lsPonto = "";
				$lnCount = 0;
				
				foreach ($laInt as $lnV)
				{
					$lsIntX .= $lsPonto . $lnV;
					$lnCount ++;
					$lsPonto = "";
					
					if ($lnCount == 3)
					{
						$lsPonto = ".";
						$lnCount = 0;
					}
				}
				
				$laValueX = str_split($lsIntX);
				$laValueX = array_reverse($laValueX);
				$lsInt = implode("", $laValueX);
				
				$lsValue = $lsSignal . $lsInt;
				
				if ($pnDec > 0)
				{
					$lsDec .= "000000000"; 
					$lsValue .= "," . substr($lsDec, 0, $pnDec);
				}
			}
			
			return $lsValue;
		}
		
		return null;
	}

	
	/**
	 * 
	 * @param number $pnValue
	 * @return number
	 */
	public static function cleanCurrence ($pnValue)
	{
		preg_match("/([\d\.,-]+)/", $pnValue, $laValue);
		$lsValue = 0;
		
		if (is_array($laValue))
		{
			$lsValue = preg_replace("/\./", "", $laValue[0]);
			$lsValue = preg_replace("/,/", ".", $lsValue);
		}
		
		return $lsValue;
	}

	
	/**
	 * 
	 * @param string $psRegistry
	 * @param number $pnType
	 * @return mixed
	 */
	public static function formatCompanyRegistry ($psRegistry, $pnType = 0)
	{
		switch ($pnType)
		{
			case 1:
				return preg_replace('/^([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})$/', '$1.$2.$3/$4-$5', $psRegistry);
				break;
			default:
				return preg_replace("/[^0-9]/", "", $psRegistry);
		}
	}
	
	
	/**
	 * 
	 * @param string $psId
	 * @param number $pnType
	 * @return mixed
	 */
	public static function formatCitizenId ($psId, $pnType = 0)
	{
		switch ($pnType)
		{
			case 1:
				return preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', '$1.$2.$3-$4', $psId);
				break;
			default:
				return preg_replace("/[^0-9]/", "", $psId);
		}
	}
	
	
	/**
	 * 
	 * @param string $psCpf
	 * @return boolean
	 */
	public static function validCPF ($psCpf = null)
	{
		$laCpfFalse = array(
			'00000000000' => false,
			'11111111111' => false,
			'22222222222' => false,
			'33333333333' => false,
			'44444444444' => false,
			'55555555555' => false,
			'66666666666' => false,
			'77777777777' => false,
			'88888888888' => false,
			'99999999999' => false,
		);
		
		// Verifica se um número foi informado
		if (empty($psCpf))
		{
			return false;
		}
	
		// Elimina possivel mascara
		$psCpf = preg_replace('/[^0-9]/', '', $psCpf);
		$psCpf = str_pad($psCpf, 11, '0', STR_PAD_LEFT);
		 
		// Verifica se o numero de digitos informados é igual a 11
		if (strlen($psCpf) != 11)
		{
			return false;
		}
		// Verifica se nenhuma das sequências invalidas abaixo foi digitada. Caso afirmativo, retorna falso
		else if (isset($laCpfFalse[$psCpf]))
		{
			return false;
		} 
		else // Calcula os digitos verificadores para verificar se o CPF é válido
		{
			for ($lnT = 9; $lnT < 11; $lnT++)
			{
				for ($lnD = 0, $lnC = 0; $lnC < $lnT; $lnC++)
				{
					$lnD += $psCpf{$lnC} * (($lnT + 1) - $lnC);
				}

				$lnD = ((10 * $lnD) % 11) % 10;
				
				if ($psCpf{$lnC} != $lnD)
				{
					return false;
				}
			}
	
			return true;
		}
	}
	
	
	/**
	 * 
	 * @param string $psPhone
	 * @param number $pnType
	 * @return mixed
	 */
	public static function formatPhone ($psPhone, $pnType = 0)
	{
		switch ($pnType)
		{
			case 1:
				$psPhone = preg_replace("/[^0-9]/", "", $psPhone);
				return preg_replace(array('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', '/^([0-9]{4,5})([0-9]{4})$/'), array('($1) $2-$3', '$1-$2'), $psPhone);
			break;
			default:
				return preg_replace("/[^0-9]/", "", $psPhone);
		}
	}
	
	
	/**
	 * Retorna um formato de data solicitado
	 *
	 * @param string $psDate
	 *        	data no formato Y/m/d (H|h):i:s ou Y-m-d (H|h):i:s
	 * @param int $pnType
	 *        	indicação de formato de retorno, padrão 0 d/m/Y - H:i:s
	 * @return string
	 */
	public static function getDateTimeFormat ($psDate, $pnType = 0)
	{
		$lnDia = (int) substr($psDate, 8, 2);
		$lnMes = (int) substr($psDate, 5, 2);
		$lnAno = substr($psDate, 0, 4);
		$lsHora = substr($psDate, 11, 8);
		
		switch ($pnType)
		{
			case 1: // d/m/Y
				return sprintf("%02d/%02d/%s", $lnDia,$lnMes,$lnAno);
				break;
			case 2: // H:i:s
				return $lsHora;
				break;
			case 3: // d de M de Y
				$lsMes = self::getMonthName($lnMes);
				return $lnDia . " de " . $lsMes . " de " . $lnAno;
				break;
			case 4: // d de m de Y
				$lsMes = self::getMonthName($lnMes, 2);
				return $lnDia . " de " . $lsMes . " de " . $lnAno;
				break;
			case 5: // d/m
				return sprintf("%02d/%02d", $lnDia, $lnMes);
				break;
			case 6: // d/m - H:i:s
				return sprintf("%02d/%02d%s", $lnDia, $lnMes, ($lsHora ? " - " . $lsHora : ""));
				break;
			case 7: // H:i
				return substr($lsHora, 0, 5);
				break;
			case 8: // Y/m/d - H:i:s
				return sprintf("%s/%02d/%02d%s", $lnAno, $lnMes, $lnDia, ($lsHora ? " - " . $lsHora : ""));
				break;
			case 9: // d/M
				$lsMes = self::getMonthName($lnMes, 1);
				return $lnDia . "/" . $lsMes; 
				break;
			case 10: // Y-m-dTH:i:s
				return sprintf("%d-%02d-%02d%s", $lnAno, $lnMes, $lnDia, ($lsHora ? "T" . $lsHora : ""));
				break;
			case 11: // d/M/Y - H:i:s
				return $lnDia . "/" . self::getMonthName($lnMes, 1) . "/" . $lnAno . ($lsHora ? " - " . $lsHora : ""); 
				break;
			case 12: // Y-m-d
				return sprintf("%s-%02d-%02d", $lnAno,$lnMes,$lnDia);
				break; 
			default: // d/m/Y - H:i:s
				return sprintf("%02d/%02d/%s%s", $lnDia, $lnMes, $lnAno, ($lsHora ? " - " . $lsHora : ""));
		}
	}

	
	/**
	 * Retorna o nome do mês em 3 formatos quando passado seu número
	 *
	 * @param int $pnMonth
	 *        	Número do mês no calendário
	 * @param int $pnType
	 *        	Formato: 0 = primeira letra; 1 = Abreviação 3 letras; 2 =
	 *        	padrão nome completo
	 *        	
	 * @return string nome do mês
	 */
	public static function getMonthName ($pnMonth, $pnType = 2)
	{
		$laMonths = array(
			array(
				"J",
				"Jan",
				"Janeiro"
			),
			array(
				"F",
				"Fev",
				"Fevereiro"
			),
			array(
				"M",
				"Mar",
				"Março"
			),
			array(
				"A",
				"Abr",
				"Abril"
			),
			array(
				"M",
				"Mai",
				"Maio"
			),
			array(
				"J",
				"Jun",
				"Junho"
			),
			array(
				"J",
				"Jul",
				"Julho"
			),
			array(
				"A",
				"Ago",
				"Agosto"
			),
			array(
				"S",
				"Set",
				"Setembro"
			),
			array(
				"O",
				"Out",
				"Outubro"
			),
			array(
				"N",
				"Nov",
				"Novembro"
			),
			array(
				"D",
				"Dez",
				"Dezembro"
			)
		);
		
		return $laMonths[$pnMonth - 1][$pnType];
	}

	
	/**
	 * Retorna o nome do mês em 3 formatos quando passado seu número
	 *
	 * @param int $pnMonth
	 *        	Número do mês no calendário
	 * @param int $pnType
	 *        	Formato: 0 = primeira letra; 1 = Abreviação 3 letras; 2 =
	 *        	padrão nome completo
	 *        	
	 * @return string nome do mês
	 */
	public static function getMonths ()
	{
		$laMonths = array(
			"1" => "Janeiro",
			"2" => "Fevereiro",
			"3" => "Março",
			"4" => "Abril",
			"5" => "Maio",
			"6" => "Junho",
			"7" => "Julho",
			"8" => "Agosto",
			"9" => "Setembro",
			"10" => "Outubro",
			"11" => "Novembro",
			"12" => "Dezembro"
		);
		
		return $laMonths;
	}
	

	/**
	 * Retorna o nome da semana em 4 formatos quando passado seu número
	 *
	 * @param int $pnWeek
	 *        	Número da semana no calendário
	 * @param int $pnType
	 *        	Formato: 0 = primeira letra; 1 = Abreviação 3 letras; 2 = nome
	 *        	simples; 3 = padrão nome completo
	 *        	
	 * @return string nome do mês
	 */
	public static function getWeekName ($pnWeek, $pnType = 3)
	{
		$laDays = array(
			array(
				"D",
				"Dom",
				"Domingo",
				"Domingo"
			),
			array(
				"S",
				"Seg",
				"Segunda",
				"Segunda-feira"
			),
			array(
				"T",
				"Ter",
				"Terça",
				"Terça-feira"
			),
			array(
				"Q",
				"Qua",
				"Quarta",
				"Quarta-feira"
			),
			array(
				"Q",
				"Qui",
				"Quinta",
				"Quinta-feira"
			),
			array(
				"S",
				"Sex",
				"Sexta",
				"Sexta-feira"
			),
			array(
				"S",
				"Sab",
				"Sábado",
				"Sábado"
			)
		);
		
		return $laDays[$pnWeek][$pnType];
	}

	
	/**
	 *
	 * @param string $psString
	 * @return string
	 */
	public static function clearStringToUrl ($psString)
	{
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ/,._!"\'@#$%&*()+=[]{}~ºª;:><|\\';
		$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                              ';
		$psString = utf8_decode($psString);
		$psString = strtr($psString, utf8_decode($a), $b);
		$psString = trim($psString);
		$psString = preg_replace('/\s\s*/', '-', $psString);
		
		return $psString = strtolower($psString);
	}

	
	/**
	 * 
	 * @param string $a
	 * @return string
	 */
	public static function cyrStrToLower ($a)
	{
		// Função não se enquadra no encode ISO-8859-1. Utilizar
		// preferencialmente a função strtolower_iso8859_1
		$offset = 32;
		$m = array();
		
		for ($lnIdx = 192; $lnIdx < 224; $lnIdx ++)
		{
			$m[chr($lnIdx)] = chr($lnIdx + $offset);
		}
		
		return strtr($a, $m);
	}

	
	/**
	 * 
	 * @param string $psString
	 * @return string
	 */
	public static function strToLowerIso8859_1 ($psString)
	{
		$lnLength = strlen($psString);
		
		while ($lnLength > 0)
		{
			-- $lnLength;
			$c = ord($psString[$lnLength]);
			
			if (($c & 0xC0) == 0xC0)
			{
				// two most significante bits on
				if (($c != 215) and ($c != 223))
				{
					// two chars OK as is to get lowercase set 3. most
					// significante bit if needed:
					$psString[$lnLength] = chr($c | 0x20);
				}
			}
		}
		
		return strtolower($psString);
	}

	
	/**
	 * 
	 * @param string $psWord
	 * @return string
	 */
	public static function clearSignals ($psWord)
	{
		$laReplace = array(
			"\"" => "",
			"'" => "",
			"$" => "",
			"\\" => "",
			"/" => "",
			"#" => "",
			"!" => "",
			"%" => "",
			"&" => "",
			"*" => "",
			"(" => "",
			")" => "",
			"-" => "",
			"_" => "",
			"=" => "",
			"`" => "",
			"[" => "",
			"]" => "",
			"{" => "",
			"}" => "",
			"^" => "",
			"~" => "",
			"+" => "",
			"<" => "",
			">" => "",
			"," => "",
			"." => "",
			";" => "",
			":" => "",
			"?" => "",
			"|" => "",
			"@" => ""
		);
		
		return strtr($psWord, $laReplace);
	}

	
	/**
	 * 
	 * @param string $psWord
	 * @return string
	 */
	public static function removeAccentuation ($psWord)
	{
		$laReplace = array(
			"à" => "a",
			"â" => "a",
			"á" => "a",
			"ä" => "a",
			"ã" => "a",
			"À" => "A",
			"Â" => "A",
			"Á" => "A",
			"Ä" => "A",
			"Ã" => "A",
			"É" => "E",
			"Ê" => "E",
			"é" => "e",
			"ê" => "e",
			"Ü" => "U",
			"Í" => "I",
			"í" => "i",
			"Ú" => "U",
			"ú" => "u",
			"ü" => "u",
			"Ó" => "O",
			"Ô" => "O",
			"Õ" => "O",
			"ó" => "o",
			"ô" => "o",
			"õ" => "o"
		);
		
		return strtr($psWord, $laReplace);
	}

	
	/**
	 *
	 * @param string $lsStr        	
	 * @return string
	 */
	public static function removeISOAccentuation ($psStr)
	{
		$lsAux = self::strToLowerIso8859_1($psStr);
		
		for ($lnIdx = 0; $lnIdx < strlen($psStr); $lnIdx ++)
		{
			if ($lsAux[$lnIdx] == "á" || $lsAux[$lnIdx] == "à" || $lsAux[$lnIdx] == "ã" || $lsAux[$lnIdx] == "â" || $lsAux[$lnIdx] == "ä")
			{
				$lsAux[$lnIdx] = "a";
			}
			elseif ($lsAux[$lnIdx] == "é" || $lsAux[$lnIdx] == "ê" || $lsAux[$lnIdx] == "è")
			{
				$lsAux[$lnIdx] = "e";
			}
			elseif ($lsAux[$lnIdx] == "í" || $lsAux[$lnIdx] == "ì")
			{
				$lsAux[$lnIdx] = "i";
			}
			elseif ($lsAux[$lnIdx] == "õ" || $lsAux[$lnIdx] == "ó" || $lsAux[$lnIdx] == "ô" || $lsAux[$lnIdx] == "ö" || $lsAux[$lnIdx] == "ò")
			{
				$lsAux[$lnIdx] = "o";
			}
			elseif ($lsAux[$lnIdx] == "ú" || $lsAux[$lnIdx] == "ü" || $lsAux[$lnIdx] == "ù")
			{
				$lsAux[$lnIdx] = "u";
			}
			elseif ($lsAux[$lnIdx] == "ç")
			{
				$lsAux[$lnIdx] = "c";
			}
			elseif ($lsAux[$lnIdx] == "ñ")
			{
				$lsAux[$lnIdx] = "n";
			}
		}
		
		return $lsAux;
	}

	
	/**
	 *
	 * @param array $paDados        	
	 * @return array
	 */
	public static function multArrayUtf8Decode ($paDados)
	{
		if (is_array($paDados))
		{
			foreach ($paDados as $lsCampo => $lsValor)
			{
				$paDados[$lsCampo] = self::multArrayUtf8Decode($lsValor);
			}
		}
		else
		{
			$lsEncode = mb_detect_encoding($paDados . 'x', 'UTF-8, ISO-8859-1');
			
			if ($lsEncode == 'UTF-8')
			{
				$paDados = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $paDados);
			}
			else
			{
				$paDados = iconv("ISO-8859-1", "ISO-8859-1//TRANSLIT", $paDados);
			}
		}
		
		return $paDados;
	}

	
	/**
	 *
	 * @param string $paDados        	
	 * @return string
	 */
	public static function limparTelefone ($psTelefone)
	{
		$psTelefone = preg_replace(array(
			"/[\s]+/",
			"/\-/",
			"/\./",
			"/\)/",
			"/\(/",
			"/\*/",
			"/\+/"
		), array(
			"",
			"",
			"",
			"",
			"",
			"",
			""
		), $psTelefone);
		
		
		return $psTelefone;
	}

	
	/**
	 * 
	 * @param string $psTelefone
	 * @return string
	 */
	public static function formatarTelefone ($psTelefone)
	{
		$psTelefone = self::limparTelefone($psTelefone);
		
		if (strlen($psTelefone) >= 12 && substr($psTelefone, 0, 2) == "55")
		{
			$psTelefone = substr($psTelefone, 2, strlen($psTelefone));
		}
		
		if (substr($psTelefone, 0, 4) == "0800")
		{
			$psTelefone = substr($psTelefone, 0, 4) . "-" . substr($psTelefone, 4, 3) . "-" . substr($psTelefone, 7, 4);
		}
		else 
			if (strlen($psTelefone) == 10)
			{
				$psTelefone = "(" . substr($psTelefone, 0, 2) . ") " . substr($psTelefone, 2, 4) . "-" . substr($psTelefone, 6, 4);
			}
		
		return $psTelefone;
	}

	
	/**
	 * Tratamento de entrada de dados para o mysql
	 *
	 * @param string $psString        	
	 */
	public static function escapeString ($psString)
	{
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
		
		return str_replace($laSearch, $laReplace, $psString);
	}

	
	/**
	 * 
	 * @param string $psEstado
	 * @return string
	 */
	public static function retornaSigla ($psEstado)
	{
		if (strlen($psEstado) == 2)
		{
			return strtoupper($psEstado);
		}
		else
		{
			$laSigla = array(
				"ACRE" => "AC",
				"ALAGOAS" => "AL",
				"AMAZONAS" => "AM",
				"AMAPA" => "AP",
				"BAHIA" => "BA",
				"CEARA" => "CE",
				"DISTRITO FEDERAL" => "DF",
				"ESPIRITO SANTO" => "ES",
				"GOIAS" => "GO",
				"MARANHAO" => "MA",
				"MATO GROSSO" => "MT",
				"MATO GROSSO DO SUL" => "MS",
				"MINAS GERAIS" => "MG",
				"PARA" => "PA",
				"PARAIBA" => "PB",
				"PARANA" => "PR",
				"PERNAMBUCO" => "PE",
				"PIAUI" => "PI",
				"RIO DE JANEIRO" => "RJ",
				"NORTH RIO GRANDE" => "RN",
				"RIO GRANDE DO NORTE" => "RN",
				"SOUTH RIO GRANDE" => "RS",
				"RIO GRANDE DO SUL" => "RS",
				"RONDONIA" => "RO",
				"RORAIMA" => "RR",
				"SANTA CATARINA" => "SC",
				"SAO PAULO" => "SP",
				"SERGIPE" => "SE",
				"TOCANTINS" => "TO"
			);
			
			return $laSigla[strtoupper($psEstado)];
		}
	}

	
	/**
	 *
	 * @return array
	 */
	public static function getEstadosBrasil ()
	{
		return array(
			"" => "Selecione um estado",
			"AC" => "Acre",
			"AL" => "Alagoas",
			"AM" => "Amazonas",
			"AP" => "Amapá",
			"BA" => "Bahia",
			"CE" => "Ceará",
			"DF" => "Distrito Federal",
			"ES" => "Espírito Santo",
			"GO" => "Goiás",
			"MA" => "Maranhão",
			"MT" => "Mato Grosso",
			"MS" => "Mato Grosso do Sul",
			"MG" => "Minas Gerais",
			"PA" => "Pará",
			"PB" => "Paraíba",
			"PR" => "Paraná",
			"PE" => "Pernambuco",
			"PI" => "Piauí",
			"RJ" => "Rio de Janeiro",
			"RN" => "Rio Grande do Norte",
			"RS" => "Rio Grande do Sul",
			"RO" => "Rondônia",
			"RR" => "Roraima",
			"SC" => "Santa Catarina",
			"SP" => "São Paulo",
			"SE" => "Sergipe",
			"TO" => "Tocantins"
		);
	}

	
	/**
	 *
	 * @return array
	 */
	public static function getBancosBrasil ()
	{
		return array(
			"000" => "Controle Interno",
			"001" => "Banco do Brasil S/A",
			"002" => "Banco Central do Brasil",
			"003" => "Banco da Amazonia S/A",
			"004" => "Banco do Nordeste do Brasil S/A",
			"008" => "Banco Santander Meridional S/A",
			"021" => "BANESTES S/A - Banco Est.Esp.Santo",
			"022" => "CREDIREAL",
			"024" => "Banco de Pernambuco S/A - BANDEPE",
			"025" => "Banco Alfa S/A",
			"027" => "Banco Estado Santa Catarina S/A",
			"028" => "BANEB",
			"029" => "Banco BANERJ S/A",
			"030" => "PARAIBAN - Banco da Paraiba S/A",
			"031" => "Banco BEG S/A",
			"033" => "Banco Est.São Paulo S/A - BANESPA",
			"034" => "Banco BEA S/A",
			"035" => "Banco do Estado do Cear  S/A - BEC",
			"036" => "Banco do Estado do Maranhão S/A",
			"037" => "Banco do Estado do Par  S/A",
			"038" => "Banco BANESTADO S/A",
			"039" => "Banco do Estado do Piaui S/A",
			"040" => "Banco Cargill S/A",
			"041" => "Banco Est. Rio Grande do Sul S/A",
			"044" => "Banco BVA S/A",
			"045" => "Banco OPPORTUNITY S/A",
			"047" => "Banco Est. de Sergipe S/A",
			"048" => "Banco BENGE S/A",
			"063" => "IBIBANK S/A - Banco Multiplo",
			"065" => "LEMON BANK Banco Multiplo S/A",
			"066" => "Banco MORGAN S. D. Witter S/A",
			"067" => "Banco BANEB S/A",
			"068" => "Banco BEA S/A",
			"070" => "BRB-Banco de Brasilia S/A",
			"104" => "Caixa Economica Federal",
			"106" => "Banco Itabanco S/A",
			"107" => "Banco BBM S/A",
			"109" => "CREDIBANCO S/A",
			"116" => "Banco BNL do Brasil S/A",
			"148" => "Bank Of America Brasil S/A(BM)",
			"151" => "Banco Nossa Caixa S/A",
			"175" => "Banco Finasa S/A",
			"184" => "Banco BBA Creditanstalt S/A",
			"204" => "BCO Inter American Express S/A",
			"208" => "Banco Pactual S/A",
			"210" => "DRESDNER Bank Lateinamerika A.",
			"212" => "Banco Matone S/A",
			"213" => "Banco ARBI S/A",
			"214" => "Banco DIBENS S/A",
			"215" => "Banco Com e Invest Sudameris",
			"216" => "Banco Regional MALCON S/A",
			"217" => "Banco JOHN DEERE S/A",
			"218" => "Banco Bonsucesso S/A",
			"219" => "Banco ZOGBI S/A",
			"222" => "BCO Credit Lyonnais Brasil S/A",
			"224" => "Banco Fibra S/A",
			"225" => "Banco Brascan S/A",
			"229" => "Banco Cruzeiro do Sul S/A",
			"230" => "Banco Bandeirantes S/A",
			"231" => "Banco Boavista interatlantico S/A",
			"233" => "Banco GE Capital S/A",
			"237" => "Banco Bradesco S/A",
			"240" => "Banco de Credito Real de M.G. S/A",
			"241" => "Banco Classico S/A",
			"243" => "Banco STOCK Maxima S/A",
			"244" => "Banco Cidade S/A",
			"246" => "Banco ABC-Brasil S/A",
			"247" => "UBS WARBURG S/A",
			"249" => "Banco Investcred UNIBANCO S/A",
			"250" => "Banco SCHAHIN S/A",
			"252" => "Banco FININVEST S/A",
			"254" => "PARANA Banco S/A",
			"263" => "Banco CACIQUE S/A",
			"265" => "Banco Fator S/A",
			"266" => "Banco Cedula S/A",
			"275" => "Banco ABN AMRO Real S/A",
			"291" => "Banco de Cred. Nacional S/A",
			"294" => "BCR",
			"300" => "Banco de LA Nacion Argentina",
			"318" => "Banco BMG S/A",
			"320" => "Banco Ind. e Com. S/A",
			"341" => "Banco Itau S/A",
			"346" => "Banco BFB",
			"347" => "Banco Sudameris Brasil S/A",
			"351" => "Banco Bozano Simonsen S/A",
			"353" => "Banco Santander S/A",
			"356" => "Banco ABN AMRO S/A",
			"366" => "Banco Societe Generale Bras. S/A",
			"370" => "Banco Westlb do Brasil S/A",
			"376" => "Banco CHASE Manhattan S/A",
			"389" => "Banco Mercantil do Brasil S/A",
			"392" => "Banco Mercantil de Sao Paulo S/A",
			"394" => "Banco BMC S/A",
			"399" => "HSBC Bank Brasil S/A (BM)",
			"409" => "Unibanco Uniao de Bancos Bras. S/A",
			"412" => "Banco Capital S/A",
			"422" => "Banco Safra S/A",
			"424" => "Banco Santander Nordeste S/A",
			"453" => "Banco Rural S/A",
			"456" => "Banco de Tokio Mitsubishi BR S/A",
			"464" => "Banco Sumitomo Mitsui Bras. S/A",
			"472" => "LLOYDS Bank PLC",
			"473" => "Banco Financial Portugues S/A",
			"477" => "Banco Citibank N/A",
			"479" => "Bankboston Banco M Ltiplo S/A",
			"487" => "Deutsche Bank S/A- Banco Alemão",
			"488" => "Morgan G. Trust Companyof NY",
			"492" => "ING Bank N. V.",
			"493" => "Banco Union - Brasil S/A",
			"494" => "Banco de La Rep. Or.Del Uruguai",
			"495" => "Banco de La Provinc.Buenos Aires",
			"496" => "Banco Uno-E Brasil S/A",
			"505" => "Banco Credit S. F. Boston S/A",
			"600" => "Banco Luso Brasileiro S/A",
			"604" => "Banco Industrial Brasileiro S/A",
			"610" => "Banco VR S/A",
			"611" => "Banco Paulista S/A",
			"612" => "Banco Guanabara S/A",
			"613" => "Banco Pecunia S/A<",
			"623" => "Banco Panamericano S/A",
			"626" => "Banco FICSA S/A",
			"630" => "Banco Intercap S/A",
			"633" => "Banco Rendimento S/A",
			"634" => "Banco Triangulo S/A",
			"637" => "Banco SOFISA S/A",
			"638" => "Banco Prosper S/A",
			"641" => "Banco Bilbao Vizc.Arg.Brasil S/A",
			"643" => "Banco Pine S/A",
			"650" => "Banco PEBB S/A",
			"652" => "Banco Frances e Brasileiro S/A",
			"653" => "Banco Indusval S/A",
			"654" => "Banco A. J. RENNER S/A",
			"655" => "Banco Votorantim S/A",
			"702" => "Banco Santos S/A",
			"707" => "Banco Daycoval S/A",
			"719" => "Banco Banif Primus S/A",
			"721" => "Banco Credibel S/A",
			"733" => "Banco das Nacoes S/A",
			"734" => "Banco Gerdau S/A",
			"735" => "Banco Pottencial S/A",
			"738" => "Banco Morada S/A",
			"739" => "Banco BGN S/A",
			"740" => "Banco BARCLAYS S/A",
			"741" => "Banco Ribeirão Preto S/A",
			"743" => "Banco Emblema S/A",
			"744" => "Bankboston N.A.",
			"745" => "Banco Citibank S/A",
			"746" => "Banco Modal S/A",
			"747" => "Banco Rabobank INT Brasil S/A",
			"748" => "Banco Coop. Sic. S/A BANSICREDI",
			"749" => "BR Banco Mercantil S/A",
			"751" => "DRESDNER Bank Brasil S/A (BM)",
			"752" => "Banco BNP Paribas Brasil S/A",
			"753" => "Banco Comercial Uruguai S/A",
			"756" => "Banco Cooperativo do Brasil S/A",
			"757" => "Banco KEB do Brasil S/A"
		);
	}

	
	/**
	 *
	 * @param string $psStr        	
	 * @return string
	 */
	public static function tagGenerator ($psStr, $pnLen = 10)
	{
		$laReplace = array(
			"\"" => "",
			"'" => "",
			"\$" => "",
			"\\" => "",
			"/" => "",
			"#" => "",
			"!" => "",
			"%" => "",
			"&" => "",
			"*" => "",
			"(" => "",
			")" => "",
			"_" => "",
			"=" => "",
			"`" => "",
			"~" => "",
			"[" => "",
			"]" => "",
			"{" => "",
			"}" => "",
			"^" => "",
			"<" => "",
			">" => "",
			"," => "",
			"." => "",
			";" => "",
			":" => "",
			"?" => "",
			"|" => "",
			"@" => "",
			" +" => " ",
			" -" => " "
		);
		
		$psStr = self::unHtmlEntities($psStr);
		$psStr = strtr($psStr, $laReplace);
		$psStr = substr($psStr, "0", $pnLen);
		$psStr = self::retiraAcento($psStr);
		$psStr = trim($psStr);
		
		$psStr = preg_replace(array(
			"/[\s]+/"
		), array(
			" "
		), $psStr);
		
		$psStr = strtoupper(str_replace(' ', '-', $psStr));
		
		return $psStr;
	}

	
	/**
	 * 
	 * @param string $psString
	 * @return string
	 */
	public static function unHtmlEntities ($psString)
	{
		// replace numeric entities
		$psString = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $psString);
		$psString = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $psString);
		
		// replace literal entities
		$laTransTbl = get_html_translation_table(HTML_ENTITIES);
		$laTransTbl = array_flip($laTransTbl);
		
		return strtr($psString, $laTransTbl);
	}

	
	/**
	 * 
	 * @param string $psString
	 * @param string $psLink
	 * @param string $psTemplate
	 * @return mixed
	 */
	public static function spellCheck ($psString, $psLink = null, $psTemplate = '<a href="#%LINK%#&q=#%QUERY%#">#%WORD%#</a>')
	{
		$psString = self::clearSignals($psString);
		$laWords = split(' ', $psString);
		
		$laSuggestions = array();
		
		foreach ($laWords as $lsValue)
		{
			$laSuggestions = pspell_suggest(pspell_new("pt"), $lsValue);
			
			if (count($laSuggestions) != 0)
			{
				foreach ($laSuggestions as $lsSuggestion)
				{
					if (! empty($lsSuggestion))
					{
						$laTmp = split(" ", $lsSuggestion);
						$lsTerm = substr($laTmp[4], 0, - 1);
						
						$lsTerm = self::removeAccentuation(strtolower($lsTerm));
						
						if (strtolower($lsValue) != strtolower($lsTerm))
						{
							$laTerm[$laTmp[1]] = substr($laTmp[4], 0, - 1);
							$lbCorrected = true;
						}
						else
						{
							$laTerm[$lsValue] = $lsValue;
						}
					}
				}
			}
		}
		
		if ($lbCorrected)
		{
			foreach ($laTerm as $lsOriginal => $lsSuggestion)
			{
				if (strtolower($lsOriginal) != strtolower($lsSuggestion))
				{
					$lsString .= "<strong>$lsSuggestion</strong> ";
				}
				else
				{
					$lsString .= "$lsSuggestion ";
				}
				
				$lsQuery .= "$lsSuggestion ";
			}
			
			$lsPattern = array(
				"/#%LINK%#/",
				"/#%QUERY%#/",
				"/#%WORD%#/"
			);
			
			$lsReplace = array(
				$psLink,
				$lsQuery,
				$lsString
			);
			
			$lsString = preg_replace($lsPattern, $lsReplace, $psTemplate);
		}
		
		return $lsString;
	}
	
	
	/**
	 * Limpa e adiciona o nono digito caso ainda não tenha
	 *
	 * @param string $psNumber
	 * @param string $psCarrier
	 * @return string|null
	 */
	public static function validPhone ($psNumber, $psCarrier = "")
	{
		$lsNumber = preg_replace("/[^0-9]/", "", $psNumber);
	
		if (strlen($lsNumber) == 11 || strlen($lsNumber) == 10)
		{
			$lsDDD = substr($lsNumber, 0, 2);
	
			$la9Digit = array(
				11 => 1,
				12 => 1,
				13 => 1,
				14 => 1,
				15 => 1,
				16 => 1,
				17 => 1,
				18 => 1,
				19 => 1,
				21 => 1,
				22 => 1,
				24 => 1,
				27 => 1,
				28 => 1,
				31 => 1,
				32 => 1,
				33 => 1,
				34 => 1,
				35 => 1,
				37 => 1,
				38 => 1,
				71 => 1,
				73 => 1,
				74 => 1,
				75 => 1,
				77 => 1,
				79 => 1,
				81 => 1,
				82 => 1,
				83 => 1,
				84 => 1,
				85 => 1,
				86 => 1,
				87 => 1,
				88 => 1,
				89 => 1,
				91 => 1,
				92 => 1,
				93 => 1,
				94 => 1,
				95 => 1,
				96 => 1,
				97 => 1,
				98 => 1,
				99 => 1
			);
	
			if (isset($la9Digit[$lsDDD]))
			{
				$lsNumber = substr($lsNumber, 2);
				$ls9 = '';
					
				if (strlen($lsNumber) == 8)
				{
					$ls9 = '9';
				}
					
				$lsNumber = $psCarrier . $lsDDD . $ls9 . $lsNumber;
			}
			else
			{
				$lsNumber = $psCarrier . $lsNumber;
			}
				
			return $lsNumber;
		}
			
		return null;
	}
}
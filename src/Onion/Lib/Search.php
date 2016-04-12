<?PHP
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
use Onion\Lib\String;
use Onion\Log\Debug;

class Search
{

	protected $_sSearchFields = "";

	protected $_nMaxLenQuery = 100;

	protected $_bUseStopWords = false;

	protected $_sOriginalQuery;

	protected $_sQuery;

	protected $_sWhere = "";

	protected $_sSearchType;

	protected $_sExpression;

	protected $_aBoolWords;

	protected $_aWithWords;

	protected $_aWithoutWords;

	protected $_aOrWords;

	protected $_aAndWords;

	protected $_nError = 0;

	protected $_nInitTime;

	protected $_nEndTime;

	protected $_nTime;

	public function set ($psVar, $pmValue)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			$this->$lsVar = $pmValue;
		}
	}

	public function get ($psVar)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			return $this->$lsVar;
		}
	}

	public function stopWords ()
	{
		if ($this->_sSearchType != "boolean")
		{
			return;
		}
		
		$laQuery = $this->_aBoolWords;
		
		if ($this->_bUseStopWords && is_array($laQuery))
		{
			$this->_oDb->connect("Erro de connect em search::stopWords()");
			
			$lbOr = false;
			$lsSql = "SELECT stWord FROM stopWords WHERE isActive='1'";
			
			foreach ($laQuery as $lsWord)
			{
				if ($lbOr == true)
				{
					$lsWhere .= " OR";
				}
				
				$lsWhere .= "stWord='" . trim($lsWord) . "'";
				$lbOr = true;
			}
			
			if (! empty($lsWhere))
			{
				$lsSql .= " AND ($lsWhere)";
			}
			
			$lrRes = $this->_oDb->query($lsSql, "Erro na query em search::stopWords()");
			
			while ($loRec = $this->_oDb->fetch_object($lrRes))
			{
				foreach ($laQuery as $lnK => $lsWord)
				{
					if ($lsWord == $loRec->stWord)
					{
						unset($laQuery[$lnK]);
					}
				}
			}
		}
		
		$this->_aBoolWords = $laQuery;
		
		return;
	}

	public function setSearchType ()
	{
		$lsQuery = $this->_sOriginalQuery;
		
		if (empty($lsQuery))
		{
			return;
		}
		
		if (preg_match('/^\\\".*\\\"$/i', $lsQuery))
		{
			$this->_sSearchType = "expression";
			$this->_sExpression = $lsQuery;
		}
		else
		{
			$laQuery = explode(" ", $lsQuery);
			
			$lsWithWordsX = "";
			$lsWithoutWords = "";
			$lsWithWords = "";
			
			foreach ($laQuery as $lsWord)
			{
				$lsSignal = substr($lsWord, 0, 1);
				
				if ($lsSignal == "-")
				{
					$lsWithoutWords .= substr($lsWord, 1) . " ";
				}
				elseif ($lsSignal == "+")
				{
					$lsWithWords .= substr($lsWord, 1) . " ";
				}
				else
				{
					if (! empty($lsWord))
					{
						$lsWithWordsX .= $lsWord . " ";
					}
				}
			}
			
			if (! empty($lsWithoutWords) || ! empty($lsWithWords))
			{
				$this->_sSearchType = "specific";
				$lsWithWords .= $lsWithWordsX;
				$this->_aWithWords = explode(" ", trim($lsWithWords));
				$this->_aWithoutWords = explode(" ", trim($lsWithoutWords));
			}
			
			if (empty($this->_sSearchType))
			{
				$laAndBoolean = explode(" AND ", $lsQuery);
				$lsAndWords = "";
				$lsOrWords = "";
				
				if (count($laAndBoolean) > 1)
				{
					$lsOrBoolean = $laAndBoolean[0];
					unset($laAndBoolean[0]);
					
					foreach ($laAndBoolean as $lsWord)
					{
						$laWords = explode(" ", $lsWord);
						$lsAndWords .= $laWords[0] . " ";
						
						foreach ($laWords as $lnKey => $lsKeyWord)
						{
							if ($lnKey != 0 && ! empty($lsKeyWord))
							{
								$lsOrWords .= $lsKeyWord . " ";
							}
						}
					}
					
					$this->_aOrWords = explode(" ", trim($lsOrWords . $lsOrBoolean));
					$this->_aAndWords = explode(" ", trim($lsAndWords));
				}
				else
				{
					$lsQuery = trim($lsQuery);
					$laWords = explode(" ", $lsQuery);
					
					if (is_array($laWords))
					{
						foreach ($laWords as $lsWord)
						{
							if (! empty($lsWord))
							{
								$this->_aOrWords[] = $lsWord;
							}
						}
					}
				}
				
				$this->_sSearchType = "boolean";
			}
		}
	}

	public function cutString ()
	{
		switch ($this->_sSearchType)
		{
			case "expression":
				if (strlen($this->_sExpression) > $this->_nMaxLenQuery)
				{
					$this->_sExpression = ltrim(substr($this->_sExpression, 0, $this->_nMaxLenQuery - 1)) . '"';
				}
			
			break;
			case "specific":
				if (is_array($this->_aWithWords))
				{
					foreach ($this->_aWithWords as $lsWord)
					{
						$lnLength = strlen($lsWord) + 2;
						
						if ($lnLength < $this->_nMaxLenQuery)
						{
							$laWithWords[] = $lsWord;
						}
					}
					
					$this->_aWithWords = $laWithWords;
				}
				
				if (is_array($this->_aWithoutWords))
				{
					foreach ($this->_aWithoutWords as $lsWord)
					{
						$lnLength = strlen($lsWord) + 2;
						
						if ($lnLength < $this->_nMaxLenQuery)
						{
							$laWithoutWords[] = $lsWord;
						}
					}
					
					$this->_aWithoutWords = $laWithoutWords;
				}
			
			break;
			case "boolean":
				if (is_array($this->_aAndWords))
				{
					foreach ($this->_aAndWords as $lsWord)
					{
						$lnLength = strlen($lsWord) + 2;
						
						if ($lnLength < $this->_nMaxLenQuery)
						{
							$laAndWords[] = $lsWord;
						}
					}
					
					$this->_aAndWords = $laAndWords;
				}
				
				if (is_array($this->_aOrWords))
				{
					foreach ($this->_aOrWords as $lsWord)
					{
						$lnLength = strlen($lsWord) + 2;
						
						if ($lnLength < $this->_nMaxLenQuery)
						{
							$laOrWords[] = $lsWord;
						}
					}
					
					$this->_aOrWords = $laOrWords;
				}
		}
	}

	public function startTime ()
	{
		$this->_nInitTime = Util::getmicrotime();
	}

	public function calcTime ()
	{
		$this->_nEndTime = Util::getmicrotime();
		$this->_nTime = $this->_nEndTime - $this->_nEndTime;
	}

	public function createRLikeTerm ()
	{
		if ($this->_sSearchType == "expression")
		{
			$this->_sQuery = String::clearSignals($this->_sExpression);
		}
		elseif ($this->_sSearchType == "specific")
		{
			if (is_array($this->_aWithWords))
			{
				foreach ($this->_aWithWords as $lsWord)
				{
					$lsWord = String::clearSignals($lsWord);
					$this->_sQuery .= "($lsWord)+";
				}
			}
			
			if (is_array($this->_aWithoutWords))
			{
				foreach ($this->_aWithoutWords as $lsWord)
				{
					$lsWord = String::clearSignals($lsWord);
					$this->_sQuery .= "($lsWord)";
				}
			}
		}
		elseif ($this->_sSearchType == "boolean")
		{
			$lsOr = "";
			
			if (is_array($this->_aAndWords))
			{
				foreach ($this->_aAndWords as $lsWord)
				{
					$lsWord = String::clearSignals($lsWord);
					$this->_sQuery .= "($lsWord)+.*";
					$lsOr = "|";
				}
			}
			
			if (is_array($this->_aOrWords))
			{
				foreach ($this->_aOrWords as $lsWord)
				{
					$lsWord = String::clearSignals($lsWord);
					$this->_sQuery .= "$lsOr($lsWord.*)";
					$lsOr = "|";
				}
			}
		}
	}

	public function createRLikeQuery ($psQuery, $psOrder = "r")
	{
		$this->startTime();
		
		$this->_sOriginalQuery = trim(String::escapeString($psQuery));
		
		$lsOrderBy = "relevance DESC, dtInsert DESC";
		
		if ($psOrder == "d")
		{
			$lsOrderBy = "dtInsert DESC, relevance DESC";
		}
		
		if (empty($this->_sOriginalQuery) && ($psOrder == "r"))
		{
			$this->_nError = '1';
			return false;
		}
		elseif (! empty($this->_sOriginalQuery))
		{
			$this->setSearchType();
			
			// $this->stopWords();
			
			$this->cutString();
			
			$this->createRLikeTerm();
			
			if ($this->_sSearchType != "expression")
			{
				$this->_sQuery = "RLIKE '{$this->_sQuery}'";
			}
			else
			{
				$this->_sQuery = "LIKE '%{$this->_sQuery}%'";
			}
			
			$lsWhere = "";
			
			if (is_array($this->_sSearchFields))
			{
				$lsOr = "";
				
				foreach ($this->_sSearchFields as $lsKey => $lsField)
				{
					$lsWhere .= $lsOr . "{$lsField} {$this->_sQuery}";
					$lsOr = " OR ";
				}
			}
			elseif (! empty($this->_sSearchFields))
			{
				$lsWhere .= "{$this->_sSearchFields} {$this->_sQuery}";
			}
		}
		else
		{
			$lsWhere = "";
		}
		
		return $this->_sWhere = $lsWhere;
	}

	/**
	 * 
	 * @param string $psQuery
	 * @param string $psOrder
	 * @return boolean|string
	 */
	public function createFullTextQuery ($psQuery, $psOrder = "r")
	{
		$this->startTime();
	
		$this->_sOriginalQuery = trim(String::escapeString($psQuery));
	
		$lsOrderBy = "relevance DESC, dtInsert DESC";
	
		if ($psOrder == "d")
		{
			$lsOrderBy = "dtInsert DESC, relevance DESC";
		}
	
		if (empty($this->_sOriginalQuery) && ($psOrder == "r"))
		{
			$this->_nError = '1';
			return false;
		}
		elseif (! empty($this->_sOriginalQuery))
		{
			$this->setSearchType();
				
			// $this->stopWords();
				
			$this->cutString();
				
			$this->createRLikeTerm();
				
			if ($this->_sSearchType != "expression")
			{
				$this->_sQuery = "'{$this->_sQuery}' IN BOOLEAN MODE";
			}
			else
			{
				$this->_sQuery = "'{$this->_sQuery}'";
			}
				
			$lsWhere = "AND MATCH ({$this->_sSearchFields}) AGAINST ({$this->_sQuery})";
			$lsRelevancia = "MATCH ({$this->_sSearchFields}) AGAINST ({$this->_sQuery})";
			
				
			if (is_array($this->_sSearchFields))
			{
				$lsOr = "";
	
				foreach ($this->_sSearchFields as $lsKey => $lsField)
				{
					$lsWhere .= $lsOr . "{$lsField} {$this->_sQuery}";
					$lsOr = " OR ";
				}
			}
			elseif (! empty($this->_sSearchFields))
			{
				$lsWhere .= "{$this->_sSearchFields} {$this->_sQuery}";
			}
		}
		else
		{
			$lsWhere = "";
		}
	
		return $this->_sWhere = $lsWhere;
	}
	/**
	 * 
	 * @param string $psQuery
	 * @param number $pnPagina
	 * @param string $psCategoria
	 * @param string $psOrdem
	 */
	public function searchDB ($psQuery, $pnPagina = 0, $psCategoria = "", $psOrdem = "r")
	{
		$this->coDb->connect("Erro de connect em class_busca::searchDB()");
		
		$this->startTime();
		
		$this->csQueryOriginal = trim(stripslashes($psQuery));
		
		$lsOrderBy = "relevancia DESC, data DESC";
		
		if ($psOrdem == "d")
		{
			$lsOrderBy = "data DESC, relevancia DESC";
		}
		
		if (empty($this->csQueryOriginal) && ($psOrdem == "r"))
		{
			$this->cnErro = '1';
			return;
		}
		elseif (! empty($this->csQueryOriginal))
		{
			$this->typeSearch();
			
			$this->removeTrash();
			
			$this->cutString();
			
			$this->termCreate();
			
			if ($this->csTipoBusca != "expressao")
			{
				$this->csQ = "'{$this->csQ}' IN BOOLEAN MODE";
			}
			else
			{
				$this->csQ = "'{$this->csQ}'";
			}
			
			$lsWhere = "AND MATCH ({$this->csCamposBusca}) AGAINST ({$this->csQ})";
			$lsRelevancia = "MATCH ({$this->csCamposBusca}) AGAINST ({$this->csQ})";
		}
		else
		{
			$lsWhere = "";
			$lsRelevancia = "(0)";
		}
		
		if (! empty($psCategoria))
		{
			$lsWhere = " AND Pagina_categoria='$psCategoria'";
		}
		
		$lsSql = "SELECT count(Pagina_id) As total
				FROM " . $psBase . "Pagina
					WHERE Pagina_ativo='1'
					$lsWhere";
		
		$lrRes = $this->coDb->query($lsSql, "Erro na query em class_busca::searchDB(1)");
		
		$loTotal = $this->coDb->fetch_object($lrRes, "Erro no fetch_object em class_busca::searchDB_texto(1)");
		
		$this->cnTotalRes = $loTotal->total;
		
		$this->cnPagina = $pnPagina * $this->cnRangePaginacao;
		
		$lsSql = "	SELECT 	Pagina_categoria AS categoria,
		Pagina_url AS url,
		Pagina_titulo AS titulo,
		Pagina_texto AS texto,
		Pagina_data AS data,
		Pagina_extra AS extra,
		Pagina_keywords AS keywords,
		Pagina_tipo AS tipo,
		$lsRelevancia AS relevancia
		FROM " . $psBase . "Pagina
		WHERE Pagina_ativo='1' $lsWhere
		ORDER BY $lsOrderBy
		LIMIT " . $this->cnPagina . ", " . $this->cnResultadosPg;
		
		$prRes = $this->coDb->query($lsSql, "Erro na query em class_busca::searchDB(2)");
		
		$lnOrdem = $this->cnPagina;
		
		while ($laReg = $this->coDb->fetch_assoc($prRes))
		{
			$laReg['size'] = strlen($laReg['url']) + strlen($laReg['titulo']) + strlen($laReg['texto']) + strlen($laReg['categoria']) + strlen($laReg['data']) + strlen($laReg['extra']) +
					 strlen($laReg['keywords']) + strlen($laReg['tipo']);
			$laReg['grupo'] = "";
			$laReg['match'] = array();
			
			$this->caPagResultado[++ $lnOrdem] = $laReg;
		}
	}
}
?>
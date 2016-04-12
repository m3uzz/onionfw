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

namespace Onion\Paginator;
use Onion\Log\Debug;

class Pagination
{

	/**
	 * Url para o link de paginação
	 * 
	 * @var string
	 */
	protected $_sUri = "?";

	/**
	 * Nome da variável de paginação
	 * 
	 * @var string
	 */
	protected $_sVarName = "p";

	/**
	 * Índice da primeira página, 0 ou 1
	 * 
	 * @var int
	 */
	protected $_nFirstPageIndex = 0;

	/**
	 * Intervalo para o índice de paginação
	 * 
	 * @var int
	 */
	protected $_nRange = 1;

	/**
	 * Quantidade de registros a serem exibidos em uma página
	 * 
	 * @var int
	 */
	protected $_nResPerPage = 10;

	/**
	 * Quantida de links de páginas a serem exibidos
	 * ex.: se 5 e o número de páginas for 10, será exibido 1 2 3 4 5 > >> ou <<
	 * < 4 5 6 7 8 > >>
	 * 
	 * @var int
	 */
	protected $_nPaginatorLimit = 5;

	/**
	 * Retorno de setPaginator, array com todas as informações para a paginação
	 * 
	 * @var array
	 */
	protected $_aData = array();

	/**
	 * Endereço para um arquivo de template ou um array com os elementos para
	 * renderizar a paginação
	 * 
	 * @var string array
	 */
	protected $_aTemplate = "";

	/**
	 * Nome do template a ser utilizado
	 * 
	 * @var string
	 */
	protected $_aTemplateName = "";

	/**
	 * Seta a propriedade $_aTemplate com os valores padroes;
	 */
	public function __construct ()
	{
		$this->_aTemplate['pg_prev'] = '<li><a href="#%LINK%#" data-act="#%L%#" data-page="#%P%#">&laquo;</a></li>' . "\n";
		$this->_aTemplate['pg_next'] = '<li><a href="#%LINK%#" data-act="#%L%#" data-page="#%P%#">&raquo;</a></li>' . "\n";
		$this->_aTemplate['pg_current'] = '<li class="active"><a href="#">#%PG%# <span class="sr-only">(current)</span></a></li>' . "\n";
		$this->_aTemplate['pg_link'] = '<li><a href="#%LINK%#" data-act="#%L%#" data-page="#%P%#">#%PG%#</a></li>' . "\n";
		$this->_aTemplate['pagination'] = '<ul class="pagination">#%PAGINATION%#</ul>';
		
		$this->_aData = array(
			'prev' => 0,
			'next' => 0,
			'prev_url' => '',
			'next_url' => '',
			'current' => 0,
			'until' => 0,
			'total' => 0,
			'count_pages' => 0,
			'pg_current' => 0,
			'index' => array(),
			'urls' => array()
		);		
		
		return $this;
	}

	public function set($psVar, $pmValue)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			$this->$lsVar = $pmValue;
		}	
	}

	public function get($psVar)
	{
		$lsVar = "_" . $psVar;
		
		if (property_exists($this, $lsVar))
		{
			return $this->$lsVar;
		}	
	}

	/**
	 * Gera um array com as informações para a paginação
	 * 
	 * @param int $pnCountRes
	 *        	- total de registros
	 * @param int $pnCurrent
	 *        	- índice da página atual
	 * @return array
	 */
	public function setPaginator ($pnCountRes, $pnCurrent = 0, $paParams = null)
	{
		// Verifica se o indice para a página atual está setada,
		// caso contrário seta com o valor de First Page.
		if (! isset($pnCurrent))
		{
			$pnCurrent = $this->_nFirstPageIndex;
		}
		
		// O índice da primeira página pode ser 0 ou 1
		// caso a primeira página seja 0, o índice a ser exibido inicia em 1.
		$lnIncrement = 1;
		
		// Caso o índice seja 1 o incremento é igual a 0
		if ($this->_nFirstPageIndex == 1)
		{
			$lnIncrement = 0;
		}
		
		// calculando o índice para a página agual
		$lnPgCurrent = ($pnCurrent / ($this->_nResPerPage / $this->_nRange)) + $lnIncrement;
		
		// calculando o número de páginas
		$lnCountPageInit = $pnCountRes / $this->_nResPerPage;
		$lnCountPages = (int) $lnCountPageInit;
		
		// Se o número de páginas for fracionado, é somada mais uma página
		if ($lnCountPages < $lnCountPageInit)
		{
			$lnCountPages = $lnCountPages + 1;
		}
		
		// Se o número de páginas for menor ou igual ao número de índices a
		// serem exibidos, então o índice inicial começa em 1
		if ($lnCountPages <= $this->_nPaginatorLimit)
		{
			$lnInit = 1;
			$i = 1;
		}
		// Senão o índici inicial deve ser calculado
		else
		{
			// Movendo o índice inicial para exibir sempre a mesma quantidade de
			// índices
			$lnNewInit = $lnPgCurrent + (int) ($this->_nPaginatorLimit / 2); // para range 10
			
			if ($lnNewInit <= $lnCountPages)
			{
				$i = $lnNewInit - $this->_nPaginatorLimit;
				
				if ($i < 1)
				{
					$i = 1;
				}
			}
			else
			{
				$i = $lnCountPages - ($this->_nPaginatorLimit - 1);
			}
		}
		
		$laIndex = array();
		$laUrls = array();
		
		for ($x = 0; $x < $this->_nPaginatorLimit; $x ++)
		{
			// Criando os índices para a páginas a serem exibidas
			if (($i + $x) <= $lnCountPages)
			{
				$nn = (($i + $x - 1) * ($this->_nResPerPage / $this->_nRange)) + $this->_nFirstPageIndex;
				
				$laIndex[$i + $x] = $nn;
				$laUrls[$i + $x] = $this->_sUri . $this->_sVarName . "=" . $nn;
				
				// verifica se possui parametros a serem concatenados
				if (sizeof($paParams) > 0)
				{
					foreach ($paParams as $lmKey => $lmValue)
					{
						$laUrls[$i + $x] .= '&' . $lmKey . '=' . $lmValue;
					}
				}
			}
		}
		
		$lnUntil = ($pnCurrent * $this->_nRange) + $this->_nResPerPage;
		
		if ($lnUntil > $pnCountRes)
		{
			$lnUntil = $pnCountRes;
		}
		
		$lnPrev = 0;
		$lsPrevUrl = '';
		
		if ($lnPgCurrent > 1)
		{
			$lnPrev = $pnCurrent - ($this->_nResPerPage / $this->_nRange);
			$lsPrevUrl = $this->_sUri . $this->_sVarName . "=" . $lnPrev;
			
			// verifica se possui parametros a serem concatenados
			if (sizeof($paParams) > 0)
			{
				foreach ($paParams as $lmKey => $lmValue)
				{
					$lsPrevUrl .= '&' . $lmKey . '=' . $lmValue;
				}
			}
		}
		
		$lnNext = 0;
		$lsNextUrl = '';
		
		if ($lnPgCurrent < $lnCountPages)
		{
			$lnNext = $pnCurrent + ($this->_nResPerPage / $this->_nRange);
			$lsNextUrl = $this->_sUri . $this->_sVarName . "=" . $lnNext;
			
			if (sizeof($paParams) > 0)
			{
				foreach ($paParams as $lmKey => $lmValue)
				{
					$lsNextUrl .= '&' . $lmKey . '=' . $lmValue;
				}
			}
		}
		
		$laData = array(
			'prev' => $lnPrev,
			'next' => $lnNext,
			'prev_url' => $lsPrevUrl,
			'next_url' => $lsNextUrl,
			'current' => (int) ($pnCurrent), // + $lnIncrement,
			'until' => (int) $lnUntil,
			'total' => (int) $pnCountRes,
			'count_pages' => (int) $lnCountPages,
			'pg_current' => (int) $lnPgCurrent
		);

		$laData['index'] = $laIndex;
		$laData['urls'] = $laUrls;
		
		$this->_aData = $laData;
		
		return $this;
	}

	/**
	 * Renderiza a paginação devolvendo o código html
	 * 
	 * @param string $psQuery
	 *        	- query adicional para a url
	 * @return string
	 */
	public function renderPagination ($psQuery = "")
	{
		if (strtr($psQuery, 0, 1) != '&' and ! empty($psQuery))
		{
			$psQuery = '&' . $psQuery;
		}
		
		$lsPagination = "";
		
		if ($this->_aData['count_pages'] > 1)
		{
			if (is_array($this->_aData['index']))
			{
				if (! empty($this->_aData['prev_url']))
				{
					$lsPattern = array(
						"/#%LINK%#/", 
						"/#%L%#/", 
						"/#%P%#/"
					);
					
					$lsReplace = array(
						$this->_aData['prev_url'] . $psQuery,
						$this->_sUri,
						$this->_aData['prev'],
					);
					
					$lsPagination = preg_replace($lsPattern, $lsReplace, $this->_aTemplate['pg_prev']);
				}
				
				foreach ($this->_aData['urls'] as $lnKey => $lsUrlPg)
				{
					if ($lnKey != 0)
					{
						if ($this->_aData['pg_current'] == $lnKey)
						{
							$lsPattern = "/#%PG%#/";
							$lsReplace = $lnKey;
							$lsPagination .= preg_replace($lsPattern, $lsReplace, $this->_aTemplate['pg_current']);
						}
						else
						{
							$lsPattern = array(
								"/#%LINK%#/",
								"/#%PG%#/",
								"/#%L%#/",
								"/#%P%#/"
							);
							
							$lsReplace = array(
								$lsUrlPg . $psQuery,
								$lnKey,
								$this->_sUri,
								$this->_aData['index'][$lnKey]
							);
							
							$lsPagination .= preg_replace($lsPattern, $lsReplace, $this->_aTemplate['pg_link']);
						}
					}
				}
				
				if (! empty($this->_aData['next_url']))
				{
					$lsPattern = array(
						"/#%LINK%#/",
						"/#%L%#/",
						"/#%P%#/"
					);
						
					$lsReplace = array(
						$this->_aData['next_url'] . $psQuery,
						$this->_sUri,
						$this->_aData['next'],
					);
						
					$lsPagination .= preg_replace($lsPattern, $lsReplace, $this->_aTemplate['pg_next']);
				}
			}
		}
		
		$lsPattern = "/#%PAGINATION%#/";
		$lsReplace = $lsPagination;
		$lsPagination = preg_replace($lsPattern, $lsReplace, $this->_aTemplate['pagination']);
		
		return $lsPagination;
	}
}

/**
 * Exemplo do arquivo template
 * 	<!--PAGINATION_PREV-->
 * 		 <a href="#%LINK%#">Previous</a>&nbsp;&nbsp;
 * 	<!--/PAGINATION_PREV-->
 * 	
 * 	<!--PAGINATION_NEXT-->
 * 		 <a href="#%LINK%#">Next</a>
 * 	<!--/PAGINATION_NEXT-->
 * 	
 * 	<!--PAGINATION_CURRENT-->
 * 		&nbsp;#%PG%#&nbsp;
 * 	<!--/PAGINATION_CURRENT-->
 * 	
 * 	<!--PAGINATION_PAGE-->
 * 		<a href="#%LINK%#">#%PG%#</a>&nbsp;
 * 	<!--/PAGINATION_PAGE-->
 * 	
 * 	<!--PAGINATION-->
 * 		<div>
 * 			#%PAGINATION%#
 * 		</div>
 * 	<!--/PAGINATION-->
 */	

/**
 * Exemplo da montagem da array que será passada como parametro
 * $laPatination['pg_prev'] 	= '<a href="#%LINK%#">Previous</a>&nbsp;&nbsp;'."\n";
 * $laPatination['pg_next'] 	= '<a href="#%LINK%#">Next</a>'."\n";
 * $laPatination['pg_current'] 	= '&nbsp;#%PG%#&nbsp;'."\n";
 * $laPatination['pg_link'] 	= '<a href="#%LINK%#">#%PG%#</a>&nbsp;'."\n";
 * $laPatination['pagination'] 	= '<div>#%PAGINATION%#</div>';
 */
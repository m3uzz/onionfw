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

namespace Onion\ORM\Query;
use \Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\AST\Functions\FunctionNode;


/**
 * @example by https://gist.github.com/1234419 Jérémy Hubert
 * "MATCH_AGAINST" "(" {StateFieldPathExpression ","}* InParameter {Literal}? ")"
 */
class MatchAgainst extends FunctionNode 
{
	/**
	 * 
	 * @var array
	 */
	protected $_aColumns = array();
	
	/**
	 * 
	 * @var obejct
	 */
	protected $_oNeedle;
	
	/**
	 * 
	 * @var object
	 */
	protected $_oMode;

	
	/**
	 * (non-PHPdoc)
	 * @see \Doctrine\ORM\Query\AST\Functions\FunctionNode::parse()
	 */
	public function parse(\Doctrine\ORM\Query\Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		do {
			$this->_aColumns[] = $parser->StateFieldPathExpression();
			$parser->match(Lexer::T_COMMA);
		}
		while ($parser->getLexer()->isNextToken(Lexer::T_IDENTIFIER));

		$this->_oNeedle = $parser->InParameter();

		while ($parser->getLexer()->isNextToken(Lexer::T_STRING)) {
			$this->_oMode = $parser->Literal();
		}

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Doctrine\ORM\Query\AST\Functions\FunctionNode::getSql()
	 */
	public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
	{
		$haystack = null;

		$lbFirst = true;
		
		foreach ($this->_aColumns as $column)
		{
			$lbFirst ? $lbFirst = false : $haystack .= ', ';
			$haystack .= $column->dispatch($sqlWalker);
		}

		$lsQuery = "MATCH(" . $haystack . ") AGAINST (" . $this->_oNeedle->dispatch($sqlWalker);

		if($this->_oMode)
		{
			$lsQuery .= " " . $this->_oMode->dispatch($sqlWalker) . " )";
		}
		else
		{
			$lsQuery .= " )";
		}

		return $lsQuery;
	}
}
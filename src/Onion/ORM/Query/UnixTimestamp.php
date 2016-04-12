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
 * Allows Doctrine 2.0 Query Language to execute a MySQL UNIX_FORMAT function
 * You must boostrap this function in your ORM as a DQLFunction.
 * UNIX_TIMESTAMP(TIMESTAMP)
 */
class UnixTimestamp extends FunctionNode
{
	/**
	 * holds the timestamp of the UNIX_TIMESTAMP DQL statement
	 * @var mixed
	 */
	protected $_sDateExpression;

	/**
	 * getSql - allows ORM to inject a UNIX_TIMESTAMP() statement into an SQL
	 * string being constructed
	 * 
	 * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
	 * @return void
	 */
	public function getSql (\Doctrine\ORM\Query\SqlWalker $sqlWalker)
	{
		return 'UNIX_TIMESTAMP(' . $sqlWalker->walkArithmeticExpression($this->_sDateExpression) . ')';
	}

	/**
	 * parse - allows DQL to breakdown the DQL string into a processable
	 * structure
	 * 
	 * @param \Doctrine\ORM\Query\Parser $parser
	 */
	public function parse (\Doctrine\ORM\Query\Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		
		$this->_sDateExpression = $parser->ArithmeticExpression();
		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}
}
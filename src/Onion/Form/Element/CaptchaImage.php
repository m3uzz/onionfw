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

namespace Onion\Form\Element;
use \Zend\Captcha as Zend;

class CaptchaImage extends Zend\Image
{

	public function __construct ()
	{
		$laFonts = array(
			'Starjout.ttf',
			'Sofia-Regular.ttf'
		);
		
		$lnFont = mt_rand(0, count($laFonts) - 1);
		
		$laBg = array(
			'nature.png'
		);
		
		$lnBg = mt_rand(0, count($laBg) - 1);
		$lsLayoutDir = BASE_DIR . DS . 'layout' . DS . 'theme' . DS . 'captcha';
		
		$laOptions = array(
			'font' => $lsLayoutDir . DS . 'font' . DS . $laFonts[$lnFont],
			'width' => 200,
			'height' => 100,
			'dotNoiseLevel' => 80,
			'lineNoiseLevel' => 5,
			'imgDir' => CLIENT_DIR . DS . 'data' . DS . 'temp',
			'imgUrl' => '/data/temp',
			'startImage' => $lsLayoutDir . DS . 'img' . DS . $laBg[$lnBg]
		);
		
		parent::__construct($laOptions);
	}
}

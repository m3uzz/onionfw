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

namespace Onion\InputFilter;

use Countable;
use Traversable;

interface InputFilterInterface extends Countable
{
    const VALIDATE_ALL = 'INPUT_FILTER_ALL';

    /**
     * Add an input to the input filter
     *
     * @param  InputInterface|InputFilterInterface|array $input
     * @param  null|string $name Name used to retrieve this input
     * @return InputFilterInterface
     */
    public function add($input, $name = null);

    /**
     * Retrieve a named input
     *
     * @param  string $name
     * @return InputInterface|InputFilterInterface
     */
    public function get($name);

    /**
     * Test if an input or input filter by the given name is attached
     *
     * @param  string $name
     * @return bool
     */
    public function has($name);

    /**
     * Remove a named input
     *
     * @param  string $name
     * @return InputFilterInterface
     */
    public function remove($name);

    /**
     * Set data to use when validating and filtering
     *
     * @param  array|Traversable $data
     * @return InputFilterInterface
     */
    public function setData($data);

    /**
     * Is the data set valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Provide a list of one or more elements indicating the complete set to validate
     *
     * When provided, calls to {@link isValid()} will only validate the provided set.
     *
     * If the initial value is {@link VALIDATE_ALL}, the current validation group, if
     * any, should be cleared.
     *
     * Implementations should allow passing a single array value, or multiple arguments,
     * each specifying a single input.
     *
     * @param  mixed $name
     * @return InputFilterInterface
     */
    public function setValidationGroup($name);

    /**
     * Return a list of inputs that were invalid.
     *
     * Implementations should return an associative array of name/input pairs
     * that failed validation.
     *
     * @return InputInterface[]
     */
    public function getInvalidInput();

    /**
     * Return a list of inputs that were valid.
     *
     * Implementations should return an associative array of name/input pairs
     * that passed validation.
     *
     * @return InputInterface[]
     */
    public function getValidInput();

    /**
     * Retrieve a value from a named input
     *
     * @param  string $name
     * @return mixed
     */
    public function getValue($name);

    /**
     * Return a list of filtered values
     *
     * List should be an associative array, with the values filtered. If
     * validation failed, this should raise an exception.
     *
     * @return array
     */
    public function getValues();

    /**
     * Retrieve a raw (unfiltered) value from a named input
     *
     * @param  string $name
     * @return mixed
     */
    public function getRawValue($name);

    /**
     * Return a list of unfiltered values
     *
     * List should be an associative array of named input/value pairs,
     * with the values unfiltered.
     *
     * @return array
     */
    public function getRawValues();

    /**
     * Return a list of validation failure messages
     *
     * Should return an associative array of named input/message list pairs.
     * Pairs should only be returned for inputs that failed validation.
     *
     * @return array
     */
    public function getMessages();
}
<?php
/*
 * The MIT License
 *
 * Copyright 2015 Anthony Maudry <anthony.maudry@thuata.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Thuata\ListenerBundle\Exception;

/**
 * <b>ListnerFileNotFoundException</b><br>
 *
 *
 * @package thuata\listenerbundle\Exception
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
class ListenerFileNotFoundException extends \Exception
{
    const FORMAT_MESSAGE = 'Unable to find file for %s listener. Looked in %s dir.';
    const ERROR_CODE = 500;

    /**
     * ListenerFileNotFoundException constructor.
     *
     * @param string $listenerKey
     * @param string $path
     */
    public function __construct(string $listenerKey, string $path)
    {
        parent::__construct(sprintf(self::FORMAT_MESSAGE, $listenerKey, $path), self::ERROR_CODE);
    }
}
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

namespace Thuata\ListenerBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\VarDumper\VarDumper;
use Thuata\ListenerBundle\Component\Listener;
use Thuata\ListenerBundle\Exception\InvalidListenerLoadedException;
use Thuata\ListenerBundle\Exception\ListenerFileNotFoundException;

/**
 * <b>ListenerService</b><br>
 *
 *
 * @package Thuata\ListenerBundle\Service
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
class ListenerService
{
    const FORMAT_LISTENER_PATH = '%s/../%s/%s.listener';
    const FORMAT_LISTENER_DIR = '%s/../%s';

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $listenerRegisterPath;

    /**
     * ListenerService constructor.
     *
     * @param \Symfony\Component\HttpKernel\Kernel $kernel
     * @param string                               $listenerRegisterPath
     */
    public function __construct(Kernel $kernel, string $listenerRegisterPath)
    {
        $this->rootDir = $kernel->getRootDir();
        $this->listenerRegisterPath = $listenerRegisterPath;
    }

    /**
     * Loads a listner by it key
     *
     * @param string $key
     * @param bool   $silent
     *
     * @return \Thuata\ListenerBundle\Component\Listener
     * @throws \Thuata\ListenerBundle\Exception\InvalidListenerLoadedException
     * @throws \Thuata\ListenerBundle\Exception\ListenerFileNotFoundException
     */
    public function loadListener(string $key, bool $silent = false) : ?Listener
    {
        $filePath = $this->getFilePath($key);

        if (!file_exists($filePath)) {
            if (!$silent) {
                throw new ListenerFileNotFoundException($key, $this->listenerRegisterPath);
            }

            return null;
        }

        $serialized = file_get_contents($filePath);

        $listener = unserialize($serialized);

        if (!($listener instanceof Listener)) {
            throw new InvalidListenerLoadedException($key, $listener);
        }

        return $listener;
    }

    /**
     * Saves a listener
     *
     * @param \Thuata\ListenerBundle\Component\Listener $listener
     *
     * @return string
     */
    public function saveListener(Listener $listener) : string
    {
        $key = $listener->getName();

        $filePath = $this->getFilePath($key);

        $dir = dirname($filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0644, true);
        }

        $serialized = serialize($listener);

        file_put_contents($filePath, $serialized);

        return $key;
    }

    /**
     * Saves a listener
     *
     * @param \Thuata\ListenerBundle\Component\Listener $listener
     */
    public function forgetListener(Listener $listener)
    {
        $key = $listener->getName();

        $filePath = $this->getFilePath($key);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Gets a file path
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFilePath(string $key) : string
    {
        return sprintf(
            self::FORMAT_LISTENER_PATH,
            $this->rootDir,
            $this->listenerRegisterPath,
            $key
        );
    }
}
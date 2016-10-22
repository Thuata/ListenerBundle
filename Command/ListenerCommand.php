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

namespace Thuata\ListenerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Thuata\ComponentBundle\Command\ThuataCommandTrait;
use Thuata\ListenerBundle\Component\Listener;
use Thuata\ListenerBundle\Exception\ListenerFileNotFoundException;
use Thuata\ListenerBundle\Exception\UnableToGuessListeningPortException;

/**
 * <b>ListenerCommand</b><br>
 *
 * @package Thuata\ComponentBundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class ListenerCommand extends ContainerAwareCommand
{
    use ThuataCommandTrait;

    const CMD_STOP = 'T_CMD_STOP';

    protected function getKey()
    {
        return str_replace(':', '--', $this->getRunListeningCommandName());
    }

    /**
     * Gets the listener for that command<br>
     *
     *
     * @param int $port
     *
     * @return \Thuata\ListenerBundle\Component\Listener
     * @throws \Thuata\ListenerBundle\Exception\UnableToGuessListeningPortException
     */
    protected function getListener(int &$port) : Listener
    {
        try {
            $listener = $this->getContainer()->get('thuata_listener.listener.service')->loadListener($this->getKey());
        } catch (ListenerFileNotFoundException $e) {
            if ($port === 0) {
                throw new UnableToGuessListeningPortException($this->getRunListeningCommandName(), $e);
            } else {
                $listener = new Listener($this->getRunListeningCommandName());
            }
        }

        if ($port === 0) {
            $port = $listener->getListenerPort();
        }

        if ($port === 0) {
            throw new UnableToGuessListeningPortException($this->getRunListeningCommandName());
        }

        return $listener;
    }

    /**
     * Gets the command name
     *
     * @return string
     */
    abstract public function getCommandName() : string;

    /**
     * Gets the command description
     *
     * @return string
     */
    abstract public function getCommandDescription() : string;

    /**
     * Gets the run listener command name
     *
     * @return string
     */
    abstract protected function getRunListeningCommandName() : string;

    /**
     * Gets the help for the command
     *
     * @return string
     */
    abstract protected function getCommandHelp() : string;

    /**
     * Gets the stop command message
     *
     * @return string
     */
    abstract protected function getStopCommandMessage() : string;

    /**
     * Gets the default port to listen
     *
     * @return int
     */
    abstract protected function getDefaultPort(): int;
}
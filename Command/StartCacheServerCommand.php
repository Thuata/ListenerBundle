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

/**
 * <b>StartCacheServerCommand</b><br>
 *
 *
 * @package Thuata\ListenerBundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
class StartCacheServerCommand extends StartListenerCommand
{
    const COMMAND_NAME = 'thuata:cache-server:start';
    const COMMAND_DESC = 'Starts the cache server.';
    const COMMAND_HELP = '';

    /**
     * Gets the command name
     *
     * @return string
     */
    public function getCommandName() : string
    {
        return self::COMMAND_NAME;
    }

    /**
     * Gets the command description
     *
     * @return string
     */
    public function getCommandDescription() : string
    {
        return self::COMMAND_DESC;
    }

    /**
     * Gets the run listener command name
     *
     * @return string
     */
    protected function getRunListeningCommandName() : string
    {
        return RunCacheServerCommand::COMMAND_NAME;
    }

    /**
     * Gets the help for the command
     *
     * @return string
     */
    protected function getCommandHelp() : string
    {
        return self::COMMAND_HELP;
    }

    /**
     * Gets the stop command message
     *
     * @return string
     */
    protected function getStopCommandMessage() : string
    {
        return RunCacheServerCommand::CMD_STOP;
    }

    /**
     * Gets the default port to listen
     *
     * @return int
     */
    protected function getDefaultPort(): int
    {
        return RunCacheServerCommand::DEFAULT_PORT;
    }
}
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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thuata\ListenerBundle\Component\Listener;

/**
 * <b>StopListenerCommand</b><br>
 *
 *
 * @package Thuata\ListenerBundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class StopListenerCommand extends ListenerCommand
{
    const COMMAND_NAME = 'thuata:cache-server:status';
    const COMMAND_DESC = 'Starts the cache server.';
    const COMMAND_HELP = '';
    const OPTION_PORT = 'port';
    const OPTION_SHORTCUT_PORT = 'p';
    const OPTION_DESC_PORT = 'The port to send to. If no port is provided, will guess the right port if the send command is run on the same application as the run command.';
    const OPTION_DEFAULT_PORT = 0;
    const OPTION_HOST = 'host';
    const OPTION_SHORTCUT_HOST = 'l';
    const OPTION_DESC_HOST = 'The host to send to.';
    const OPTION_DEFAULT_HOST = '127.0.0.1';
    const FORMAT_ERROR_NO_LISTENER = '<error>Could no find a listener for %s command.</>';
    const FORMAT_STOPPED_LISTENING = '<info>Stopped listener for %s command witch was listening on %s:%d.</>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addOption(self::OPTION_HOST, self::OPTION_SHORTCUT_HOST, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_HOST, self::OPTION_DEFAULT_HOST)
            ->addOption(self::OPTION_PORT, self::OPTION_SHORTCUT_PORT, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_PORT, static::OPTION_DEFAULT_PORT)
            ->setHelp(sprintf($this->getCommandHelp(), get_class($this)));
    }

    /**
     * Method to overload to execute the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption(self::OPTION_HOST);
        $port = (int)$input->getOption(self::OPTION_PORT);

        $listener = $this->getListener($port);

        $listener->send($this->getStopCommandMessage(), $host, $port);

        return 0;
    }
}
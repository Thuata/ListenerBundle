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

/**
 * <b>ListenerCommand</b><br>
 *
 *
 * @package thuata\componentbundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class SendListenerCommand extends ListenerCommand
{
    const OPTION_MESSAGE = 'message';
    const OPTION_SHORTCUT_MESSAGE = 'm';
    const OPTION_DESC_MESSAGE = 'The message to send to the %s listening command.';
    const OPTION_STOP = 'stop-listener';
    const OPTION_SHORTCUT_STOP = 's';
    const OPTION_DESC_STOP = 'Sends the stop command message to the %s listening command.';
    const OPTION_PORT = 'port';
    const OPTION_SHORTCUT_PORT = 'p';
    const OPTION_DESC_PORT = 'The port to send to. If no port is provided, will guess the right port if the send command is run on the same application as the run command.';
    const OPTION_DEFAULT_PORT = 0;
    const OPTION_HOST = 'host';
    const OPTION_SHORTCUT_HOST = 'l';
    const OPTION_DESC_HOST = 'The host to send to.';
    const OPTION_DEFAULT_HOST = '127.0.0.1';
    const WELCOME_SEND = 'Will <fg=yellow>send</> message <fg=cyan>%s</> to <fg=red>%s</> command';
    const MESSAGE_STOP = 'Received <fg=red>STOP</> command.';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addOption(self::OPTION_MESSAGE, self::OPTION_SHORTCUT_MESSAGE, InputOption::VALUE_REQUIRED, sprintf(self::OPTION_DESC_MESSAGE, $this->getRunListeningCommandName()))
            ->addOption(self::OPTION_STOP, self::OPTION_SHORTCUT_STOP, InputOption::VALUE_NONE, sprintf(self::OPTION_DESC_STOP, $this->getRunListeningCommandName()))
            ->addOption(self::OPTION_HOST, self::OPTION_SHORTCUT_HOST, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_HOST, self::OPTION_DEFAULT_HOST)
            ->addOption(self::OPTION_PORT, self::OPTION_SHORTCUT_PORT, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_PORT, static::OPTION_DEFAULT_PORT)
            ->setHelp($this->getCommandHelp());
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(InputInterface $input, OutputInterface $output) : int
    {
        if ($input->getOption(self::OPTION_STOP)) {
            $message = $this->getStopCommandMessage();
        } else {
            $message = $input->getOption(self::OPTION_MESSAGE);
        }

        $output->writeln(sprintf(self::WELCOME_SEND, $message, $this->getRunListeningCommandName()));

        $this->send($message, $input);

        return 0;
    }

    /**
     * Sends a message to the socket
     *
     * @param string                                          $message
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Thuata\ListenerBundle\Exception\UnableToGuessListeningPortException
     */
    protected function send(string $message, InputInterface $input)
    {
        $host = $input->getOption(self::OPTION_HOST);
        $port = (int)$input->getOption(self::OPTION_PORT);

        $listener = $this->getListener($port);

        $listener->send($message, $host, $port);
    }
}
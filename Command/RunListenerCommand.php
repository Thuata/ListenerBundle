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
use Symfony\Component\VarDumper\VarDumper;
use Thuata\ListenerBundle\Component\Listener;
use Thuata\ListenerBundle\Exception\TreatMessageNotImplementedException;

/**
 * <b>ListenerCommand</b><br>
 *
 *
 * @package thuata\componentbundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class RunListenerCommand extends ListenerCommand
{
    const OPTION_PORT = 'port';
    const OPTION_SHORTCUT_PORT = 'p';
    const OPTION_DESC_PORT = 'The port to listen. If no port is provided, will listen to a random port.';
    const OPTION_HOST = 'host';
    const OPTION_SHORTCUT_HOST = 'l';
    const OPTION_DESC_HOST = 'The host to listen. 0.0.0.0 for all hosts.';
    const WELCOME_LISTEN = 'Will <fg=yellow>listen</> for incoming message.';
    const START_LISTEN = 'Start listening on  <fg=cyan>%s</><fg=yellow>:</><fg=cyan>%s</>';
    const WELCOME_SEND = 'Will <fg=yellow>send</> message <fg=cyan>%s</> to <fg=red>%s</> command';
    const DEFAULT_HOST = '0.0.0.0';
    const MESSAGE_STOP = 'Received <fg=red>STOP</> command.';
    const HOWTO_STOP = 'To stop listening send the message <fg=white;bg=blue>%s</>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addOption(self::OPTION_HOST, self::OPTION_SHORTCUT_HOST, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_HOST, self::DEFAULT_HOST)
            ->addOption(self::OPTION_PORT, self::OPTION_SHORTCUT_PORT, InputOption::VALUE_OPTIONAL, self::OPTION_DESC_PORT, $this->getDefaultPort())
            ->setHelp(sprintf($this->getCommandHelp(), get_class($this)));
    }

    /**
     * Execute listening mode (without send option)
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function doExecute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln(self::WELCOME_LISTEN);

        $port = (int)$input->getOption(self::OPTION_PORT);
        $host = $input->getOption(self::OPTION_HOST);

        $listener = new Listener($this->getKey());

        $listener->bind($host, $port);

        $this->getContainer()->get('thuata_listener.listener.service')->saveListener($listener);

        $output->writeln(sprintf(self::START_LISTEN, $host, (string)$listener->getListenerPort()));
        $output->writeln(sprintf(self::HOWTO_STOP, $this->getStopCommandMessage()));


        $this->beforeListen($listener, $input, $output);

        $this->runListener($listener, $input, $output);

        $this->afterListen($listener, $input, $output);

        $listener->close();

        $this->afterClose($input, $output);

        $this->getContainer()->get('thuata_listener.listener.service')->forgetListener($listener);

        return 0;
    }

    /**
     * Treats message
     *
     * @param mixed                                             $message
     * @param string                                            $remoteAddress
     * @param string                                            $remotePort
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws TreatMessageNotImplementedException
     */
    protected function treatMessage($message, string $remoteAddress, string $remotePort, OutputInterface $output)
    {
        throw new TreatMessageNotImplementedException(get_class($this));
    }

    /**
     * Runs the listener
     *
     * @param Listener        $listener
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function runListener(Listener $listener, InputInterface $input, OutputInterface $output)
    {
        $stopCommandMessage = $this->getStopCommandMessage();
        $scope = $this;

        $listener->listen(function ($message, $name, $port) use ($output, $stopCommandMessage, $scope) {
            if ($message === $this->getStopCommandMessage()) {
                $output->writeln(self::MESSAGE_STOP);

                return Listener::RESULT_STOP_LISTEN;
            }
            $scope->treatMessage($message, $name, $port, $output);

            return Listener::RESULT_OK;
        });
    }

    /**
     * Called before listen
     *
     * @param Listener        $listener
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function beforeListen(Listener $listener, InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Called after listen
     *
     * @param Listener        $listener
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function afterListen(Listener $listener, InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Called after close
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function afterClose(InputInterface $input, OutputInterface $output)
    {
    }
}
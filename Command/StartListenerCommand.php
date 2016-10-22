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
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\VarDumper;
use Thuata\ListenerBundle\Component\Listener;

/**
 * <b>StartListenerCommand</b><br>
 *
 *
 * @package thuata\listenerbundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class StartListenerCommand extends ListenerCommand
{
    const OPTION_PORT = 'port';
    const OPTION_SHORTCUT_PORT = 'p';
    const OPTION_DESC_PORT = 'The port to listen. If no port is provided, will listen to a random port.';
    const OPTION_HOST = 'host';
    const OPTION_SHORTCUT_HOST = 'l';
    const OPTION_DESC_HOST = 'The host to listen. 0.0.0.0 for all hosts.';
    const DEFAULT_PORT = 8080;
    const DEFAULT_HOST = '0.0.0.0';
    const WELCOME_START = 'Will start %s listner';
    const FORMAT_INPUT_COMMAND = '--%s=%s';
    const MAX_WAIT_SECONDS = 5;
    const FORMAT_ERROR_NO_LISTENER = '<error>Could no launch %s command.</>';
    const FORMAT_LISTENER_RUNNING = '<info>Launched %s command. listening %s:%d</>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addOption(static::OPTION_HOST, static::OPTION_SHORTCUT_HOST, InputOption::VALUE_OPTIONAL, static::OPTION_DESC_HOST, self::DEFAULT_HOST)
            ->addOption(static::OPTION_PORT, static::OPTION_SHORTCUT_PORT, InputOption::VALUE_OPTIONAL, static::OPTION_DESC_PORT, static::DEFAULT_PORT)
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
        $output->writeln(sprintf(self::WELCOME_START, $this->getRunListeningCommandName()));

        $processCommand = sprintf('php bin/console %s', $this->getRunListeningCommandName());

        if ($input->hasOption(static::OPTION_HOST)) {
            $processCommand .= ' --' . static::OPTION_HOST . '=\'' . $input->getOption(static::OPTION_HOST) . '\'';
        }

        if ($input->hasOption(static::OPTION_PORT)) {
            $processCommand .= ' --' . static::OPTION_PORT . '=' . $input->getOption(static::OPTION_PORT);
        }

        $process = new Process($processCommand);
        $process->start();

        $time = time();

        $listener = $this->getContainer()->get('thuata_listener.listener.service')->loadListener($this->getKey(), true);

        while ($listener === null) {
            usleep(1000);

            if (time() - $time > self::MAX_WAIT_SECONDS) {
                break;
            }
            $listener = $this->getContainer()->get('thuata_listener.listener.service')->loadListener($this->getKey(), true);
        }

        if (!$listener instanceof Listener) {
            $output->writeln(sprintf(self::FORMAT_ERROR_NO_LISTENER, $this->getRunListeningCommandName()));
        } else {
            $output->writeln(sprintf(self::FORMAT_LISTENER_RUNNING, $this->getRunListeningCommandName(), $listener->getListenerHost(), $listener->getListenerPort()));
        }

        return 0;
    }
}
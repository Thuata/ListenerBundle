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
use Symfony\Component\Console\Output\OutputInterface;
use Thuata\ListenerBundle\Component\Listener;

/**
 * <b>GetStatusListenerCommand</b><br>
 *
 *
 * @package Thuata\ListenerBundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
abstract class GetStatusListenerCommand extends ListenerCommand
{
    const FORMAT_NOT_LISTENING = '<error>%s is not listening</>';
    const FORMAT_LISTENING = '<info>%s is listening %s:%d</>';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
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
        $listener = $this->getContainer()->get('thuata_listener.listener.service')->loadListener($this->getKey(), true);

        if (!$listener instanceof Listener) {
            $output->writeln(sprintf(self::FORMAT_NOT_LISTENING, $this->getRunListeningCommandName()));
        } else {
            $output->writeln(sprintf(self::FORMAT_LISTENING, $this->getRunListeningCommandName(), $listener->getListenerHost(), $listener->getListenerPort()));
        }

        return 0;
    }
}
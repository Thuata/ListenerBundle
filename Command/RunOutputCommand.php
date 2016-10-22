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

use Symfony\Component\Console\Output\OutputInterface;

/**
 * <b>OutputCommand</b><br>
 *
 *
 * @package Thuata\ComponentBundle\Command
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
class RunOutputCommand extends RunListenerCommand
{
    const COMMAND_NAME = 'thuata:output:run';
    const COMMAND_DESCRIPTION = 'Opens an output socket. Messages sent to that socket will be written in the bash console.';
    const FORMAT_RECEIVED_MESSAGE = '<fg=green>Received</> message from <fg=cyan>%s</><fg=yellow>:</><fg=cyan>%d</> :';
    const FORMAT_MESSAGE = '<fg=white;bg=blue>%s</>';
    const COMMAND_HELP = <<<EOL
This command launches a socket listning to an host and port. Every message sent to that socket will be printed in the console
Usage :

<fg=green>$</> php bin/console <fg=yellow>%s</>

Will start to listen for any host on a random port.
The port number is registered on a file so if the send command is run on the same application it will be able to guess the used port.
The listened host and port are printed when the %s command is ran, so you can use them as arguments on the send command.

<fg=green>$</> php bin/console <fg=yellow>%s</> --%s=<fg=magenta>'92.156.41.14'</>

Will start to listen to any message comming from the <fg=magenta>92.156.41.14</> host.

<fg=green>$</> php bin/console <fg=yellow>%s</> --%s=<fg=cyan>8181</>

Will start to listen to any message comming to the <fg=cyan>8181</> port.

If the <fg=yellow>%s</> listening command receives the <bg=blue;fg=white>%s</> message it will stop listening and the command will stop. 
EOL;
    const DEFAULT_PORT = 8080;


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
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
    }

    /**
     * Treats message
     *
     * @param mixed                                             $message
     * @param string                                            $remoteAddress
     * @param string                                            $remotePort
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function treatMessage($message, string $remoteAddress, string $remotePort, OutputInterface $output)
    {
        $output->writeln(sprintf(static::FORMAT_RECEIVED_MESSAGE, $remoteAddress, $remotePort));
        $output->writeln(sprintf(static::FORMAT_MESSAGE, $message));
    }

    /**
     * Gets the message that launches the stop command
     *
     * @return string
     */
    protected function getStopCommandMessage() : string
    {
        return self::CMD_STOP;
    }

    /**
     * Gets the command description
     *
     * @return string
     */
    public function getCommandDescription() : string
    {
        return self::COMMAND_DESCRIPTION;
    }

    /**
     * Gets the run listener command name
     *
     * @return string
     */
    protected function getRunListeningCommandName() : string
    {
        return self::COMMAND_NAME;
    }

    /**
     * Gets the help for the command
     *
     * @return string
     */
    protected function getCommandHelp() : string
    {
        return sprintf(
            self::COMMAND_HELP,
            $this->getName(),
            $this->getName(),
            $this->getName(),
            self::OPTION_HOST,
            $this->getName(),
            self::OPTION_PORT,
            $this->getName(),
            $this->getStopCommandMessage()
        );
    }

    /**
     * Gets the default port to listen
     *
     * @return int
     */
    protected function getDefaultPort(): int
    {
        return self::DEFAULT_PORT;
    }
}
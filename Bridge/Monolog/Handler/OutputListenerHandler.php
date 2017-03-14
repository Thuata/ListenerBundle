<?php
/**
 * Created by Enjoy Your Business.
 * Date: 16/11/2016
 * Time: 13:50
 * Copyright 2014 Enjoy Your Business - RCS Bourges B 800 159 295 ©
 */

namespace Thuata\ListenerBundle\Bridge\Monolog\handler;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Thuata\ListenerBundle\Command\RunOutputCommand;
use Thuata\ListenerBundle\Component\Listener;


/**
 * Class OutputListenerHandler
 *
 * @package   Thuata\ListenerBundle\Bridge\Monolog\handler
 *
 * @author    Emmanuel Derrien <emmanuel.derrien@enjoyyourbusiness.fr>
 * @author    Anthony Maudry <anthony.maudry@enjoyyourbusiness.fr>
 * @author    Loic Broc <loic.broc@enjoyyourbusiness.fr>
 * @author    Rémy Mantéi <remy.mantei@enjoyyourbusiness.fr>
 * @author    Lucien Bruneau <lucien.bruneau@enjoyyourbusiness.fr>
 * @author    Matthieu Prieur <matthieu.prieur@enjoyyourbusiness.fr>
 * @copyright 2014 Enjoy Your Business - RCS Bourges B 800 159 295 ©
 */
class OutputListenerHandler extends AbstractProcessingHandler
{
    const DEFAULT_HOST = '127.0.0.1';
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * OutputListenerHandler constructor.
     *
     * @param string $host
     * @param int    $port
     * @param int    $level
     * @param bool   $bubble
     */
    public function __construct(string $host = self::DEFAULT_HOST, int $port = RunOutputCommand::DEFAULT_PORT, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        $listener = new Listener(RunOutputCommand::COMMAND_NAME);

        $listener->send((string) $record['formatted'], $this->host, $this->port);
    }
}
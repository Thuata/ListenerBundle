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

namespace Thuata\ListenerBundle\Component;

/**
 * <b>SocketDefinition</b><br>
 *
 *
 * @package thuata\listenerbundle\Component
 *
 * @author  Anthony Maudry <anthony.maudry@thuata.com>
 */
class Listener
{
    const RESULT_STOP_LISTEN = 1;
    const RESULT_OK = 0;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @var int
     */
    private $domain;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $protocol;

    /**
     * @var bool
     */
    private $binded;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $listenerPort;

    /**
     * @var string
     */
    private $listenerHost;

    /**
     * Listener constructor.
     *
     * @param string $name
     * @param int    $domain
     * @param int    $type
     * @param int    $protocol
     */
    public function __construct(string $name, int $domain = AF_INET, int $type = SOCK_DGRAM, int $protocol = SOL_UDP)
    {
        $this->name = $name;
        $this->domain = $domain;
        $this->type = $type;
        $this->protocol = $protocol;

        $this->createSocket();
    }

    /**
     * Binds the socket to an host and port
     *
     * @param string $host
     * @param int    $port
     *
     * @return bool
     */
    public function bind(string &$host, int &$port)
    {
        $res = socket_bind($this->socket, $host, $port);

        $this->listenerHost = $host;
        $this->listenerPort = $port;

        $this->binded = $res;

        return $res;
    }

    /**
     * Closes the socket
     */
    public function close()
    {
        socket_close($this->socket);
        $this->binded = false;
    }

    /**
     * Listens the binded socket and apply a closure to each incoming message
     *
     * @param \Closure $onMessage
     *
     * @return bool
     */
    public function listen(\Closure $onMessage)
    {
        if (!$this->binded) {
            return false;
        }

        while (true) {
            socket_recvfrom($this->socket, $message, 512, 0, $name, $port);
            $onMessageResult = $onMessage($message, $name, $port);

            if ($onMessageResult === self::RESULT_STOP_LISTEN) {
                break;
            }
        }

        return true;
    }

    /**
     * Sends a message to a host and port
     *
     * @param        $message
     * @param string $host
     * @param int    $port
     * @param int    $flags
     */
    public function send($message, string $host = '127.0.0.1', int $port = 0, int $flags = 0)
    {
        if ($port === 0) {
            $port = $this->getListenerPort();
        }
        socket_sendto($this->socket, $message, strlen($message), $flags, $host, $port);
    }

    /**
     * Binds and listen to a socket
     *
     * @param string   $host
     * @param int      $port
     * @param \Closure $onMessage
     */
    public function bindListen(string &$host, int &$port, \Closure $onMessage)
    {
        $this->bind($host, $port);

        $res = $this->listen($onMessage);

        if ($res) {
            $this->close();
        }
    }

    /**
     * Gets Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets ListenerPort
     *
     * @return int
     */
    public function getListenerPort(): int
    {
        return (int)$this->listenerPort;
    }

    /**
     * Gets ListenerHost
     *
     * @return string
     */
    public function getListenerHost(): string
    {
        return $this->listenerHost;
    }

    /**
     * Creates the socket
     */
    protected function createSocket()
    {
        $this->socket = socket_create($this->domain, $this->type, $this->protocol);
    }

    /**
     * Prepares the object to be serialized and returns properties to save
     *
     * @return array
     */
    public function __sleep()
    {
        return [
            'domain',
            'type',
            'protocol',
            'listenerHost',
            'listenerPort'
        ];
    }

    /**
     * Wakes up the entity
     */
    public function __wakeup()
    {
        $this->createSocket();
    }
}
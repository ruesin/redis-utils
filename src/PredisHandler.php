<?php

namespace Ruesin\Utils;

use Swover\Pool\ConnectorInterface;

class PredisHandler implements ConnectorInterface
{
    private $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        $parameters = [
            'host' => $this->config['host'],
            'port' => $this->config['port'] ?? 6379,
        ];

        $options = [];
        if (isset($this->config['options'])) {
            $options = $this->config['options'];
        }

        if (isset($this->config['prefix'])) {
            $options['prefix'] = $this->config['prefix'];
        }

        if (isset($this->config['database'])) {
            $options['parameters']['database'] = $this->config['database'];
        }

        if (isset($this->config['password']) && $this->config['password']) {
            $options['parameters']['password'] = $this->config['password'];
        }

        return new \Predis\Client($parameters, $options);
    }

    public function disconnect($connection)
    {
        // TODO: Implement disconnect() method.
    }

    public function reset($connection)
    {
        // TODO: Implement reset() method.
    }
}
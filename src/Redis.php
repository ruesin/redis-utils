<?php

namespace Ruesin\Utils;

/**
 * Class Redis
 * @see \Predis\Client
 */
class Redis
{
    /**
     * All Configs
     *
     * @var array
     */
    private static $configs = [];

    /**
     * All instance
     * @var array
     */
    private static $instances = null;

    /**
     * Connection name
     * @var null
     */
    private $name = null;

    /**
     * @var \Predis\Client
     */
    private $connection = null;

    /**
     * Connection config
     * @var array
     */
    private $config = [];

    private function __construct($name, $config)
    {
        $this->name = $name;
        $this->config = $config ?: self::getConfig($this->name);
        $this->connection = $this->connect($this->config);
    }

    private function __clone()
    {
    }

    /**
     * @param $name
     * @param array $config
     * @return Redis | \Predis\Client
     */
    public static function getInstance($name, $config = [])
    {
        if (!isset(self::$instances[$name]) || !self::$instances[$name]) {
            self::$instances[$name] = self::createInstance($name, $config);
        }
        return self::$instances[$name];
    }

    /**
     * @param $name
     * @param array $config
     * @return Redis | \Predis\Client
     */
    public static function createInstance($name, $config = [])
    {
        return new self($name, $config);
    }

    private function connect($config)
    {
        $parameters = [
            'host' => $config['host'],
            'port' => isset($config['port']) && $config['port'] ? $config['port'] : 6379,
        ];

        $options = [];
        if (isset($config['options'])) {
            $options = $config['options'];
        }

        if (isset($config['prefix'])) {
            $options['prefix'] = $config['prefix'];
        }

        if (isset($config['database'])) {
            $options['parameters']['database'] = $config['database'];
        }

        if (isset($config['password']) && $config['password']) {
            $options['parameters']['password'] = $config['password'];
        }

        return new \Predis\Client($parameters, $options);
    }

    /**
     * @param string $name
     * @param array $config
     * @param bool $rewrite
     */
    public static function setConfig($name, array $config, $rewrite = true)
    {
        if (array_key_exists($name, self::$configs) && $rewrite !== true) return;
        self::$configs[$name] = $config;
    }

    /**
     * @param $name
     * @return array
     */
    public static function getConfig($name)
    {
        return array_key_exists($name, self::$configs) ? self::$configs[$name] : [];
    }

    /**
     * @param $name
     */
    public static function delConfig($name)
    {
        self::$configs[$name] = null;
        unset(self::$configs[$name]);
    }

    public static function clear()
    {
        foreach (self::$instances as $name => $instance) {
            self::close($name);
        }
    }

    public static function close($name)
    {
        if (!isset(self::$instances[$name])) return true;
        self::$instances[$name]->disconnect();
        self::$instances[$name] = null;
        unset(self::$instances[$name]);
        return true;
    }

    public function __call($name, $arguments)
    {
        if (empty($this->connection) || !$this->connection instanceof \Predis\ClientInterface) {
            return false;
        }
        /*if (!method_exists($this->connection, $name)) {
            return false;
        }*/
        return call_user_func_array([$this->connection, $name], $arguments);
    }
}


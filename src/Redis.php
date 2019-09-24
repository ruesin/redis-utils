<?php

namespace Ruesin\Utils;

use Swover\Pool\PoolFactory;

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
     * @var PoolFactory|null
     */
    private $pool = null;

    private function __construct($name)
    {
        $config = self::getConfig($name);
        $this->pool = new PoolFactory($config, new PredisHandler($config));
    }

    private function __clone()
    {
    }

    /**
     * @param $name
     * @param array $config
     * @return Redis | PoolFactory | \Predis\Client
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
     * @return Redis | PoolFactory | \Predis\Client
     */
    public static function createInstance($name, $config = [])
    {
        if (!empty($config)) {
            self::setConfig($name, $config);
        }
        return new self($name);
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

    public static function clear($name = null)
    {
        $instances = $name === null ? self::$instances :
            (isset(self::$instances[$name]) ? [$name => self::$instances[$name]] : []);
        foreach ($instances as $name => $instance) {
            self::$instances[$name] = null;
            unset(self::$instances[$name]);
        }
        return true;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pool, $name], $arguments);
    }
}


<?php

namespace Ruesin\Utils;

/**
 * Class Redis
 * @see \Predis\Client
 */
class Redis
{
    private static $_instance = null;

    /**
     * @var \Predis\ClientInterface
     */
    private $connect = null;

    private function __construct($config)
    {
        $this->connect = $this->connect($config);
    }

    private function __clone()
    {
    }

    /**
     * @return \Predis\ClientInterface | bool | self
     */
    public static function getInstance($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);
        if (empty($config)) {
            return false;
        }
        $name = self::geInstancetName($key, $config);

        if (!isset(self::$_instance[$name])) {
            self::$_instance[$name] = new self($config);
        }
        return self::$_instance[$name];
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

    public static function closeAll()
    {
        foreach (self::$_instance as $name => $val) {
            self::clearInstance($name);
        }
    }

    public static function close($key = '', $config = [])
    {
        return self::clearInstance(self::geInstancetName($key, self::getConfig($key, $config)));
    }

    private static function clearInstance($name)
    {
        if (!isset(self::$_instance[$name])) return true;
        self::$_instance[$name]->disconnect();
        self::$_instance[$name] = null;
        unset(self::$_instance[$name]);
        return true;
    }

    private static function getConfig($key, $config)
    {
        if (!empty($config)) {
            return $config;
        }

        if ($key) {
            return Config::get('redis.'.$key);
        }

        $redisConfig = Config::get('redis', []);
        if (empty($redisConfig)) return [];

        if (array_key_exists($key, $redisConfig)) {
            return $redisConfig[$key];
        }

        if (count($redisConfig) == count($redisConfig, COUNT_RECURSIVE) && array_key_exists('host', $redisConfig)) {
            return $redisConfig;
        }

        while (!empty($redisConfig)) {
            $tempConfig = array_shift($redisConfig);
            if (is_array($tempConfig) && array_key_exists('host', $tempConfig)) {
                return $tempConfig;
            }
        }
        return [];
    }

    private static function geInstancetName($key, $config)
    {
        return $key ? : self::configToName($config);
    }

    private static function configToName($config)
    {
        return md5(json_encode($config));
    }

    public function __call($name, $arguments)
    {
        if (empty($this->connect) || ! $this->connect instanceof \Predis\ClientInterface) {
            return false;
        }
        return call_user_func_array([$this->connect, $name], $arguments);
    }
}


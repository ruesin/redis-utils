<?php

namespace Ruesin\Utils;

/**
 * Class Redis
 * @see \Predis\Client
 */
class Redis
{
    private static $_instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return \Predis\ClientInterface | bool
     */
    public static function getInstance($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);

        if (empty($config)) {
            return false;
        }

        $name = self::configToName($config);

        if (!isset(self::$_instance[$name])) {
            self::$_instance[$name] = self::connect($config);
        }

        return self::$_instance[$name];
    }

    private static function connect($config)
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

    public static function close($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);
        $name = self::configToName($config);

        if (isset(self::$_instance[$name])) {
            self::$_instance[$name]->quit();
            self::$_instance[$name] = null;
            unset(self::$_instance[$name]);
        }
        return true;
    }

    private static function getConfig($key, $config)
    {
        if (!$key) {
            if (!empty($config)) {
                return $config;
            }
        }
        $redisConfig = Config::get('redis', []);
        if (empty($redisConfig)) return [];

        if (count($redisConfig) == count($redisConfig, COUNT_RECURSIVE) && array_key_exists('host', $redisConfig)) {
            return $redisConfig;
        } else {
            while (!empty($redisConfig)) {
                $tempConfig = array_shift($redisConfig);
                if (is_array($tempConfig) && array_key_exists('host', $tempConfig)) {
                    return $tempConfig;
                }
            }
        }
        return [];
    }

    private static function configToName($config)
    {
        return md5(json_encode($config));
    }
}


<?php
define('ROOT_DIR', __DIR__ . '/');
require_once ROOT_DIR . '/../vendor/autoload.php';

//加载配置文件
\Ruesin\Utils\Config::loadPath(ROOT_DIR.'config/');

//从配置项中获取配置文件
$redis = \Ruesin\Utils\Redis::getInstance('default');
$redis->set('name', 'ruesin');
echo $redis->get('name').PHP_EOL;
//关闭连接
\Ruesin\Utils\Redis::close('default');

//指定配置的实例
$redis = \Ruesin\Utils\Redis::getInstance('', \Ruesin\Utils\Config::get('redis.web'));
$redis->set('name', 'ruesin Liu');
echo $redis->get('name').PHP_EOL;

//不指定配置的实例
$redis = \Ruesin\Utils\Redis::getInstance();
$redis->set('no_config_name', 'ruesin');
echo $redis->get('no_config_name').PHP_EOL;

\Ruesin\Utils\Redis::getInstance()->set('normal_key', 'normal_value');
echo \Ruesin\Utils\Redis::getInstance()->get('normal_key');

//关闭所有连接
\Ruesin\Utils\Redis::closeAll();

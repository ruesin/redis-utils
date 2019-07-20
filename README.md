# Redis-Utils
基于[predis](https://github.com/nrk/predis)的单例类，没有对`Predis`做任何修改，仅为了方便使用而加了层单例壳。

1. 使用`setConfig($name, $config)`加载配置到静态属性`$configs`中，`$name`为连接名，`$config`为连接选项参数
2. 使用`getInstance($name)`获取指定连接名`$name`的`\Predis\Client`实例
3. 获取的即为`\Predis\Client`实例，直接[使用](https://github.com/nrk/predis)即可

```php
$configs = [
    'default' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'database' => '0',
        'username' => '',
        'password' => '',
        'prefix' => 'default:'
    ],
    'web' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'database' => '0',
        'username' => '',
        'password' => '',
        'prefix' => 'web:'
    ]
];
//加载配置到静态属性
foreach ($configs as $key => $config) {
    \Ruesin\Utils\Redis::setConfig($key, $config);
}

//获取实例
$redis = \Ruesin\Utils\Redis::getInstance('default');
$redis->set('name', 'ruesin');
echo $redis->get('name') . PHP_EOL;

//关闭连接
\Ruesin\Utils\Redis::close('default');

//清理所有连接
\Ruesin\Utils\Redis::clear();
```
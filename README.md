# Redis-Utils
基于[predis](https://github.com/nrk/predis)的Redis工具类。

## 依赖
- [predis/predis](https://github.com/nrk/predis)
- [ruesin/utils](https://github.com/ruesin/utils)

## 使用
使用`\Ruesin\Utils\Redis::getInstance($key,$config)`获取`\Predis\Client`实例。
- 参数`$key`可选，如果有值，则获取Config配置项中`redis.$key`的配置。
- 参数`$config`可选，如果有值，优先使用此配置。
- 如果`$key`和`$config`都没有值，则会从`Config::get('redis')`中获取第一个一维数组。即`redis`如果是一维数组则直接用`redis`配置，否则取第一个数组。 


使用`\Ruesin\Utils\Redis::close($key,$config)`关闭指定连接。

使用`\Ruesin\Utils\Redis::closeAll()`关闭全部连接。
<?php

namespace Volador\Db;

/**
* 数据库配置
* @since [version> [<description>]
* @author [name] <[<email address>]>
*/
class DBConfig
{
    /**
     * [$serv config]
     * @var [type]
     */
    static protected $_dt;

    static public function addServer($key, $options)
    {
        // 连接超时(ms)
        $conf['conn_timeout'] = 1000;

        $conf['retry'] = 2;         // 连接重试次数(实际连接次数 = retry x 服务器数量)
        $conf['retry_time'] = 200;  // 连接重试间隔时间

        $conf['port'] = 3306;       // 默认端口
        $conf['charset'] = 'UTF8';  // 默认连接字符集

        foreach ($options as $k => $v) {
            $conf[$k] = $v;
        }

        self::$_dt[$key] = $conf;
    }

    /**
     * [get description]
     * @param  [type] $k1 [description]
     * @param  [type] $k2 [description]
     * @return [type]     [description]
     */
    static public function get($k1, $k2=null)
    {
        if (array_key_exists($k1, self::$_dt)) {
            $r = self::$_dt[$k1];

            // 如果指定了k2
            if (!is_null($k2)) {
                return array_key_exists($k2, $r) ? $r[$k2] : null;
            }

            return $r;
        }

        return null;
    }
}
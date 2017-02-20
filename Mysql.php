<?php

namespace Volador\Db;

use Volador\Db\DBConfig;
use Volador\Log\Logger;

/**
* Mysql数据库处理类
* @author [name] <[<email address>]>
* @since [version> [<description>]
*/
class Mysql
{
    /**
     * Connect Pool
     * @var [type]
     */
    static protected $pool;

    /**
     * [Connect description]
     * @param [type]  $key    [description]
     * @param boolean $master [description]
     */
    static public function Connect($key, $master = false)
    {
        $hosts = null;
        if ($master == true) {
            $hosts = DBConfig::get($key, 'master');
        } else {
            $hosts = DBConfig::get($key, 'slaves');
        }

        // 不是主从配置, 获取单数据库配置
        if (is_null($hosts)) {
            $hosts = DBConfig::get($key, 'host');
        }

        // 尝试连接次数
        $retry      = DBConfig::get($key, 'retry');;
        // 连接间隔时间
        $retry_time = DBConfig::get($key, 'retry_time');

        for ($i=0; $i < $retry; $i++) {
            shuffle($hosts); // 随机服务器连接顺序
            foreach ($hosts as $idx => $host) {
                // 获取连接信息
                $dbname     = DBConfig::get($key, 'name');
                $dbport     = DBConfig::get($key, 'port');
                $username   = DBConfig::get($key, 'username');
                $password   = DBConfig::get($key, 'password');

                $dsn = "mysql:dbname={$dbname};host={$host};port={$dbport}";

                // 设置连接属性
                $options = array(
                    // 连接数据库的超时秒数
                    \PDO::ATTR_TIMEOUT => intval(DBConfig::get($key, 'conn_timeout') / 1000),
                    // 设置连接字符集
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                    );

                // connect
                echo "{$i}: {$dsn}\n";
                $con = self::_connect($dsn, $username, $password, $options);
                if ($con) {
                    return $con;
                }

                usleep($retry_time);
            }
        }

        // 没有可用的连接
        Logger::fatal("Mysql Can not connect to any of the database services");
        return null;
    }

    static public function _connect($dsn, $username, $password, $options = array())
    {
        try {
            $dbh = new \PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            $message = $e->getMessage();
            // Logger::warning("Mysql Connect failed: {$message}, {$dsn}.");
        }

        return null;
    }
}
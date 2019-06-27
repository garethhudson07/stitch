<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Connection;


class Stitch
{
    /**
     * @var Connection
     */
    protected static $connection;

    public static function setDatabaseDriver(string $driver)
    {
        Connection::setDriver($driver);
    }

    public static function setDatabaseHost(string $host)
    {
        Connection::setHost($host);
    }

    public static function connect(string $database, string $username, string $password)
    {
        static::$connection = new Connection(
            $database,
            $username,
            $password
        );
    }

    public static function getConnection()
    {
        return static::$connection;
    }

    public static function disconnect()
    {
        if (static::$connection) {
            static::$connection->disconnect();
        }
    }

    public static function register(string $name, Closure $callback)
    {
        Registry::add($name, $callback);
    }

    public static function make(Closure $callback)
    {
        return Factory::model($callback);
    }

    public static function resolve(string $name)
    {
        return Registry::get($name);
    }

    public static function __callStatic($name, $arguments)
    {
        return static::resolve($name);
    }
}
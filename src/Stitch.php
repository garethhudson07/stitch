<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Connection;

/**
 * Class Stitch
 * @package Stitch
 */
class Stitch
{
    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @param string $driver
     */
    public static function setDatabaseDriver(string $driver)
    {
        Connection::setDriver($driver);
    }

    /**
     * @param string $host
     */
    public static function setDatabaseHost(string $host)
    {
        Connection::setHost($host);
    }

    /**
     * @param string $database
     * @param string $username
     * @param string $password
     */
    public static function connect(string $database, string $username, string $password)
    {
        static::$connection = new Connection(
            $database,
            $username,
            $password
        );
    }

    /**
     * @return Connection
     */
    public static function getConnection()
    {
        return static::$connection;
    }

    /**
     * @return void
     */
    public static function disconnect()
    {
        if (static::$connection) {
            static::$connection->disconnect();
        }
    }

    /**
     * @param string $name
     * @param Closure $callback
     */
    public static function register(string $name, Closure $callback)
    {
        Registry::add($name, $callback);
    }

    /**
     * @param Closure $callback
     * @return Model
     */
    public static function make(Closure $callback)
    {
        return Factory::model($callback);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($name, $arguments)
    {
        return static::resolve($name);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function resolve(string $name)
    {
        return Registry::get($name);
    }
}
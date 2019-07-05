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
    protected static $connections = [];

    /**
     * @param Closure $callback
     */
    public static function addConnection(Closure $callback)
    {
        $connection = new Connection();

        $callback($connection);

        static::$connections[$connection->getName()] = $connection;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function getConnection(string $name)
    {
        return static::$connections[$name];
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
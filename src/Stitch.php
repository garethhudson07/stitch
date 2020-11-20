<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Connection;
use Stitch\DBAL\Schema\Table;

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
     * @param string $name
     * @param $item
     */
    public static function register(string $name, $item)
    {
        Registry::add($name, $item);
    }

    /**
     * @param Closure $callback
     * @return Model
     */
    public static function make(Closure $callback)
    {
        $table = new Table();

        $callback($table);

        return new Model($table);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($name, $arguments)
    {
        return static::resolve($name, $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|null
     */
    public static function resolve(string $name, array $arguments = [])
    {
        return Registry::get($name, $arguments);
    }
}

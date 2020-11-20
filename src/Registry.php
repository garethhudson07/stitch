<?php

namespace Stitch;

use Closure;

/**
 * Class Registry
 * @package Stitch
 */
class Registry
{
    /**
     * @var array
     */
    protected static $items = [];

    /**
     * @param string $name
     * @param $item
     */
    public static function add(string $name, $item)
    {
        static::$items[$name] = $item;
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed|null
     */
    public static function get($name, array $arguments = [])
    {
        return static::has($name) ? static::resolve($name, $arguments) : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function has(string $name)
    {
        return array_key_exists($name, static::$items);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function resolve(string $name, array $arguments = [])
    {
        $item = static::$items[$name];

        if ($item instanceof Closure) {
            $item = $item(...$arguments);

            if (!$arguments) {
                static::$items[$name] = $item;
            }
        }

        return $item;
    }
}
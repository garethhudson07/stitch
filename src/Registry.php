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
     * @return mixed|null
     */
    public static function get($name)
    {
        return static::has($name) ? static::resolve($name) : null;
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
     * @return mixed
     */
    public static function resolve(string $name)
    {
        $item = static::$items[$name];

        if ($item instanceof Closure) {
            $item = $item();
            static::$items[$name] = $item;
        }

        return $item;
    }
}
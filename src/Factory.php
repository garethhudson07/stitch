<?php

namespace Stitch;

use Closure;
use Stitch\Schema\Table;

/**
 * Class Factory
 * @package Stitch
 */
class Factory
{
    /**
     * @param Closure $callback
     * @return Model
     */
    public static function model(Closure $callback)
    {
        return new Model(static::table($callback));
    }

    /**
     * @param Closure $callback
     * @return Table
     */
    public static function table(Closure $callback)
    {
        $table = new Table();

        $callback($table);

        return $table;
    }
}
<?php

namespace Stitch;

use Closure;
use Stitch\Schema\Table;

class Factory
{
    /**
     * @param string $table
     * @param Closure $callback
     * @return Model
     */
    public static function model(Closure $callback)
    {
        return new Model(static::table($callback));
    }

    /**
     * @param string $name
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
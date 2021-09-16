<?php

namespace Stitch\Queries;

use Stitch\Aggregate\Set;

class Path extends Set
{
    /**
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @param string $path
     * @return Path
     */
    public static function make(string $path = ''): Path
    {
        return (new static())->fill(
            array_filter(explode(static::$delimiter, $path))
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public function to(string $path): string
    {
        return implode(
            static::$delimiter,
            array_merge($this->items, [$path])
        );
    }

    /**
     * @return bool
     */
    public function isRelation(): bool
    {
        return ($this->count() > 0);
    }

    /**
     * @return String
     */
    public function relation(): String
    {
        return implode(static::$delimiter, $this->items);
    }
}

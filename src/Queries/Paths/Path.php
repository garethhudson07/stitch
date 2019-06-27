<?php

namespace Stitch\Queries\Paths;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class Path
 * @package Stitch\Queries\Paths
 */
class Path implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @var array
     */
    protected $pieces;

    /**
     * Path constructor.
     * @param array $pieces
     */
    public function __construct(array $pieces = [])
    {
        $this->pieces = $pieces;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        return $this->pieces[0] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->pieces[count($this->pieces) - 1] ?? null;
    }

    /**
     * @param int $index
     * @return static
     */
    public function before(int $index)
    {
        return new static(array_slice($this->pieces, 0, $index));
    }

    /**
     * @param int $index
     * @return static
     */
    public function after(int $index)
    {
        return new static(array_slice($this->pieces, $index + 1));
    }

    /**
     * Count the number of pieces in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->pieces);
    }

    /**
     * Get an iterator for the pieces.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pieces);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->implode();
    }

    /**
     * @return string
     */
    public function implode(): string
    {
        return implode(static::$delimiter, $this->pieces);
    }
}

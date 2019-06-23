<?php

namespace Stitch\Queries;

use ArrayIterator;
use IteratorAggregate;
use Countable;
use Stitch\Relations\Collection as Relations;

class Path implements IteratorAggregate, Countable {

    protected static $delimiter = '.';

    protected $pieces;

    protected $column;

    protected $relation;

    /**
     * Path constructor.
     * @param array $pieces
     */
    public function __construct(array $pieces = [])
    {
        $this->pieces = $pieces;
    }

    /**
     * @param string $input
     * @return static
     */
    public static function from(string $input)
    {
        return new static(explode(static::$delimiter, $input));
    }

    /**
     * @param Relations $relations
     * @return $this
     */
    public function split(Relations $relations)
    {
        $relation = null;
        $index = 0;

        foreach ($this->pieces as $key => $piece) {
            if (!$relation = $relations->get($piece)) {
                $index = $key;
                break;
            }

            $relations = $relation->getForeignModel()->getRelations();
        }

        $this->column = $this->after($index - 1);
        $this->relation = $this->before($index);

        return $this;
    }

    /**
     * @return mixed
     */
    public function relation()
    {
        return $this->relation;
    }

    /**
     * @return mixed
     */
    public function column()
    {
        return $this->column;
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
     * @param null|string $delimiter
     * @return string
     */
    public function implode(?string $delimiter = null): string
    {
        return implode($delimiter ?: static::$delimiter, $this->pieces);
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
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pieces);
    }
}

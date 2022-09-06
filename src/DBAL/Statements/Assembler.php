<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\HasBindings;

/**
 * Class Assembler
 * @package Stitch\DBAL\Statements
 */
class Assembler implements HasBindings
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $glue = ' ';

    /**
     * Assembler constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param $item
     * @return $this
     */
    public function push($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param string $glue
     * @return $this
     */
    public function glue(string $glue)
    {
        $this->glue = $glue;

        return $this;
    }

    /**
     * @return array
     */
    public function bindings(): array
    {
        if (!$this->items) {
            return [];
        }

        return array_merge(...array_map(function ($item)
        {
            return $item instanceof HasBindings ? $item->bindings() : [];
        }, $this->items));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->implode();
    }

    /**
     * @return string
     */
    public function implode()
    {
        return implode($this->glue, $this->items);
    }
}

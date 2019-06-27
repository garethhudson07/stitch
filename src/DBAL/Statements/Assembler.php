<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

/**
 * Class Assembler
 * @package Stitch\DBAL\Statements
 */
class Assembler implements Assemblable
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
     * @param Assemblable $item
     * @return $this
     */
    public function unshift(Assemblable $item)
    {
        array_unshift($this->items, $item);

        return $this;
    }

    /**
     * @param Assemblable $item
     * @return $this
     */
    public function push(Assemblable $item)
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
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        if (!$this->items) {
            return [];
        }

        return array_merge(...array_map(function (Assemblable $item) {
            return $item->getBindings();
        }, $this->items));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->resolve();
    }

    /**
     * @return string
     */
    public function resolve()
    {
        return implode($this->glue, $this->items);
    }
}
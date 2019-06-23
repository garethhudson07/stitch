<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

class Assembler implements Assemblable
{
    protected $items;

    protected $glue = ' ';

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function unshift(Assemblable $item)
    {
        array_unshift($this->items, $item);

        return $this;
    }

    public function push(Assemblable $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function count()
    {
        return count($this->items);
    }

    public function glue(string $glue)
    {
        $this->glue = $glue;

        return $this;
    }


    public function getItems()
    {
        return $this->items;
    }

    public function resolve()
    {
        return implode($this->glue, $this->items);
    }

    public function getBindings(): array
    {
        if (!$this->items) {
            return [];
        }

        return array_merge(...array_map(function(Assemblable $item) {
            return $item->getBindings();
        }, $this->items));
    }

    public function __toString(): string
    {
        return $this->resolve();
    }
}
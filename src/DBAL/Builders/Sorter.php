<?php

namespace Stitch\DBAL\Builders;

class Sorter
{
    protected $columns = [];

    public function add(string $name, string $direction)
    {
        $index = $this->indexOf($name);

        if ($index !== false) {
            $this->columns[$index]['direction'] = $direction;
        } else {
            $this->columns[] = [
                'name' => $name,
                'direction' => $direction
            ];
        }

        return $this;
    }

    public function indexOf(string $name)
    {
        foreach ($this->columns as $key => $column) {
            if ($column->name === 'name') {
                return $key;
            }
        }

        return false;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function count()
    {
        return count($this->columns);
    }
}
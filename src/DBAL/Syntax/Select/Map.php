<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\Collection;
use Stitch\DBAL\Builders\Table as Builder;

class Map extends Collection
{
    protected $locations = [];

    /**
     * @param Builder $builder
     * @param Table $table
     * @return $this
     */
    public function add(Builder $builder, Table $table)
    {
        $this->push($table);

        $this->locations

        return $this;
    }

    /**
     * @param Builder $builder
     * @return null
     */
    public function match(Builder $builder)
    {
        foreach ($this->items as $item) {
            if ($item->getBuilder() === $builder) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param Builder $builder
     * @return int
     */
    public function occurrences(Builder $builder)
    {
        $count = 0;
        $schema = $builder->getSchema();

        foreach ($this->items as $item) {
            if ($item->getSchema() === $schema) {
                $count++;
            }
        }

        return $count;
    }
}

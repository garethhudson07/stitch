<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Schema\Table as Schema;

class Tables
{
    protected $items = [];

    protected $keys;

    /**
     * @param Builder $builder
     * @return mixed
     */
    public static function make(Builder $builder)
    {
        return (new static())->push($builder);
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function push(Builder $builder)
    {
        $table = new Table($builder);

        if ($occurrences = $this->occurrences($builder)) {
            $table->suffix('_' . ($occurrences + 1));
        }

        $this->items[] = $table;

        foreach ($builder->getJoins() as $join) {
            $this->push($join);
        }

        return $this;
    }

    /**
     * @param Builder $builder
     * @return null
     */
    public function find(Builder $builder)
    {
        foreach ($this->items as $item) {
            if ($item->getBuilder() === $builder) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param Builder $schema
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

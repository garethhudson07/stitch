<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;

class Resolver
{
    protected $tables = [];

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @param TableBuilder $builder
     * @return mixed|Table
     */
    public function table(TableBuilder $builder)
    {
        foreach ($this->tables as $table) {
            if ($builder === $table->getBuilder()) {
                return $table;
            }
        }

        $table = new Table($builder, $this);

        $this->tables[] = $table;

        return $table;
    }

    /**
     * @param ColumnBuilder $builder
     * @return mixed
     */
    public function column(ColumnBuilder $builder)
    {
        return $this->table($builder->getTable())->column(
            $builder->getSchema()
        );
    }

    /**
     * @return null
     */
    public function rootTable()
    {
        foreach ($this->tables as $table) {
            if ($table->getBuilder() instanceof QueryBuilder) {
                return $table;
            }
        }

        return null;
    }

    /**
     * @return null
     */
    public function rootConnection()
    {
        if ($table = $this->rootTable()) {
            return $table->getBuilder()->getSchema()->getConnection();
        }

        return null;
    }


    /**
     * @param Table $table
     * @return bool
     */
    public function conflict(Table $table)
    {
        $schema = $table->getBuilder()->getSchema();

        foreach ($this->tables as $val) {
            if ($val === $table) {
                continue;
            }

            if ($val->getBuilder()->getSchema() === $schema) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Table $table
     * @return int
     */
    public function uniqueId(Table $table)
    {
        $id = 1;
        $schema = $table->getBuilder()->getSchema();

        foreach ($this->tables as $val) {
            if ($val === $table) {
                return $id;
            }

            if ($val->getBuilder()->getSchema() === $schema) {
                $id++;
            }
        }

        return $id;
    }
}
<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Builders\Column;
use Stitch\DBAL\Builders\Query;
use Stitch\DBAL\Builders\Table;

class Context
{
    protected $crossDatabase = false;

    protected $crossTable = false;

    /**
     * @param Query $query
     * @return $this
     */
    public function analyse(Query $query)
    {
        return $this->analyseDatabases($query)
            ->analyseTables($query);
    }

    /**
     * @param Table $builder
     * @return $this
     */
    public function analyseDatabases(Table $builder)
    {
        $database = $builder->getSchema()->getConnection()->getDatabase();

        foreach ($builder->getJoins() as $join) {
            if ($builder->getSchema()->getConnection()->getDatabase() !== $database) {
                $this->crossDatabase = true;

                return $this;
            }

            $this->analyseDatabases($join);
        }

        return $this;
    }

    /**
     * @param Query $builder
     * @return $this
     */
    public function analyseTables(Query $builder)
    {
        if (count($builder->getJoins())) {
            $this->crossTable = true;
        }

        return $this;
    }

    /**
     * @param Column $builder
     * @return array
     */
    public function columnPath(Column $builder)
    {
        $pieces = [];
        $schema = $builder->getSchema();

        if ($this->crossTable) {
            $table = $schema->getTable();

            if ($this->crossDatabase) {
                $pieces[] = $table->getConnection()->getDatabase();
            }

            $pieces[] = $table->getName();
        }

        $pieces[] = $schema->getName();

        return $pieces;
    }

    /**
     * @return bool
     */
    public function crossDatabase()
    {
        return $this->crossDatabase;
    }

    /**
     * @return bool
     */
    public function crossTable()
    {
        return $this->crossTable;
    }
}

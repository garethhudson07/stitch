<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Schema\Column as ColumnSchema;

class Table extends AliasablePath
{
    protected $resolver;

    protected $builder;

    protected $components = [];

    protected $columns = [];


    public function __construct(Builder $builder, Resolver $resolver)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->resolver = $resolver;
    }

    /**
     *
     */
    protected function evaluate(): void
    {
        $schema = $this->builder->getSchema();
        $db = $schema->getDatabase();

        if ($db !== $this->resolver->rootTable()->getBuilder()->getSchema()->getDatabase()) {
            $this->qualifiedName->push(
                $db->getName()
            );
        }

        $this->qualifiedName->push($schema->getName());

        if ($this->conflict()) {
            $this->alias->push(
                $this->resolver->uniqueId($this)
            );
        }
    }

    /**
     * @return bool
     */
    public function conflict(): bool
    {
        return $this->resolver->conflict($this);
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param ColumnSchema $schema
     * @return mixed
     */
    public function column(ColumnSchema $schema)
    {
        $name = $schema->getName();

        if (!array_key_exists($name, $this->columns)) {
            $this->columns[$name] = new Column($schema, $this, $this->resolver);
        }

        return $this->columns[$name];
    }

    /**
     * @return mixed
     */
    public function primaryKey()
    {
        return $this->column(
            $this->builder->getSchema()->getPrimaryKey()
        );
    }
}

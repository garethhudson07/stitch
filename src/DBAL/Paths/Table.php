<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Schema\Column as ColumnSchema;

class Table extends Path
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

    protected function build(): void
    {
        $schema = $this->builder->getSchema();

        if ($schema->getConnection()->getName() !== $this->resolver->rootConnection()->getName()) {
            $this->qualifiedName->push(
                $schema->getConnection()->getDatabase()
            );
        }

        $this->qualifiedName->push($schema->getName());

        if ($this->conflict()) {
            $this->alias->push(
                "_{$this->resolver->uniqueId($this)}"
            );
        }
    }

//    /**
//     * @return array
//     */
//    public function components(): array
//    {
//        if (!$this->components) {
//            $schema = $this->builder->getSchema();
//            $suffix = $this->conflict() ? "_{$this->resolver->uniqueId($this)}" : '';
//
//
//            if ($schema->getConnection()->getName() !== $this->resolver->rootConnection()->getName()) {
//               $this->components[] = $schema->getConnection()->getDatabase();
//            }
//
//            $this->components[] = "{$schema->getName()}$suffix";
//        }
//
//        return $this->components;
//    }

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

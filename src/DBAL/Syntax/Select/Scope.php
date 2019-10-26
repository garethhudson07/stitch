<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Builders\Table as Builder;

class Scope
{
    protected $context;

    protected $builder;

    protected $table;

    protected $columns;

    public function __construct(Context $context, Builder $builder)
    {
        $this->context = $context;
        $this->builder = $builder;

        $this->table = new Table($context, $builder->getSchema());
    }



    /**
     * @return Table
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * @param TableSchema $schema
     * @return string
     */
    public function primaryKeyAlias(TableSchema $schema)
    {
        return $this->columnAlias(
            $schema->getPrimaryKey()
        );
    }

    /**
     * @param $schema
     * @return mixed
     */
    public function column($schema)
    {
        $name = $schema->getName();

        if (!array_key_exists($name, $this->columns)) {
            $this->columns[$name] = new Column($this->context, $schema, $this->table);
        }

        return $this->columns[$name];
    }
}
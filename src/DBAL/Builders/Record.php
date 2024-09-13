<?php

namespace Stitch\DBAL\Builders;

use Stitch\DBAL\Schema\Table as Schema;
use Stitch\DBAL\Schema\Column;

/**
 * Class Record
 * @package Stitch\DBAL\Builders
 */
class Record
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * Record constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function column(string $name, $value)
    {
        if ($this->schema->hasColumn($name)) {
            $this->columns[$name] = $this->schema->getColumn($name)->encode($value);
        }

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function fill(array $columns)
    {
        foreach ($columns as $name => $value) {
            $this->column($name, $value);
        }

        return $this;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getMutatableColumns(): array
    {
        $columns = $this->schema->getColumns();
        $mutatable = [];

        foreach ($this->columns as $name => $value) {
            $column = current(array_filter($columns, fn (Column $column) => $column->getName() === $name));

            // If the column is writeable or temp writeable, add it to the mutatable columns
            // Temp writeable columns are set when the column is specifically set by setAttribute
            if ($column && ($column->isWriteable() || $column->isTempWriteable())) {
                $mutatable[$name] = $value;
            }
        }

        return $mutatable;
    }
}

<?php

namespace Stitch\DBAL\Builders;

use Stitch\DBAL\Schema\Column as Schema;

/**
 * Class Column
 * @package Stitch\Select\Paths
 */
class Column
{
    /**
     * @var Schema
     */
    protected $schema;

    protected $table;

    protected $jsonPath;

    /**
     * Column constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param Table $table
     * @return $this
     */
    public function table(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param JsonPath $path
     * @return $this
     */
    public function jsonPath(JsonPath $path)
    {
        $this->jsonPath = $path;

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
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getJsonPath()
    {
        return $this->jsonPath;
    }

    /**
     * @param Column $column
     * @return bool
     */
    public function matches(Column $column)
    {
        return ($this->schema === $column->getSchema() && $this->table === $column->getTable());
    }
}

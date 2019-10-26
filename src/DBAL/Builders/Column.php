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
     * @return mixed
     */
    public function getJsonPath()
    {
        return $this->jsonPath;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param Column $column
     * @return bool
     */
    public function matches(Column $column)
    {
        $schema = $column->getSchema();

        return (
            $this->schema->getName() === $schema->getName()
            && $schema->getTable()->getName() === $this->schema->getTable()->getName()
        );
    }
}

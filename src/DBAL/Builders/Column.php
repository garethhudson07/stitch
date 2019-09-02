<?php

namespace Stitch\DBAL\Builders;

use Stitch\Schema\Column as Schema;

/**
 * Class Column
 * @package Stitch\Queries\Paths
 */
class Column
{
    /**
     * @var Schema
     */
    protected $schema;

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
     * @param JsonPath $path
     * @return $this
     */
    public function setJsonPath(JsonPath $path)
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

//    /**
//     * @return string
//     */
//    public function resolve(): string
//    {
//        if ($this->jsonPath && $this->schema->getType() === 'json') {
//            return "{$this->schema->getName()} -> '{$this->jsonPath->resolve()}'";
//        }
//
//        return $this->schema->getName();
//    }
}

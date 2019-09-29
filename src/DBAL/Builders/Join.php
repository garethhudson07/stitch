<?php

namespace Stitch\DBAL\Builders;

use Stitch\Schema\Table as Schema;
use Stitch\Schema\Column as ColumnSchema;

/**
 * Class Join
 * @package Stitch\DBAL\Builders
 */
class Join extends Table
{
    /**
     * @var string
     */
    protected $type;

    protected $localKey;

    protected $foreignKey;

    /**
     * @var Expression
     */
    protected $conditions;

    /**
     * Join constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema);

        $this->conditions = new Expression();
    }

    /**
     * @param string $type
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param ColumnSchema $column
     * @return $this
     */
    public function localKey(ColumnSchema $column)
    {
        $this->localKey = $column;

        return $this;
    }

    /**
     * @param ColumnSchema $column
     * @return $this
     */
    public function foreignKey(ColumnSchema $column)
    {
        $this->foreignKey = $column;

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function on(...$arguments)
    {
        $this->conditions->and(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function onRaw(...$arguments)
    {
        $this->conditions->andRaw(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orOn(...$arguments)
    {
        $this->conditions->or(...$arguments);

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return $this
     */
    public function orOnRaw(...$arguments)
    {
        $this->conditions->orRaw(...$arguments);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @return mixed
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return Expression
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}

<?php

namespace Stitch\DBAL\Statements\Persistence;

use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as Builder;

/**
 * Class Update
 * @package Stitch\DBAL\Statements\Persistence
 */
class Update extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Insert constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push("UPDATE {$this->builder->getTable()} SET")
            ->push(
                $this->component($this->assignments())->bindMany($this->assignmentValues())
            )->push('WHERE')
            ->push(
                $this->component($this->condition())->bind($this->conditionValue())
            );
    }

    /**
     * @return string
     */
    protected function assignments()
    {
        $primaryKey = $this->builder->getPrimaryKey();
        $assignments = [];

        foreach ($this->builder->getAttributes() as $name => $value) {
            if ($name !== $primaryKey) {
                $assignments[] = "$name = ?";
            }
        }

        return implode(', ', $assignments);
    }

    /**
     * @return array
     */
    protected function assignmentValues()
    {
        $primaryKey = $this->builder->getPrimaryKey();
        $values = [];

        foreach ($this->builder->getAttributes() as $name => $value) {
            if ($name !== $primaryKey) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @return string
     */
    protected function condition()
    {
        return "{$this->builder->getPrimaryKey()} = ?";
    }

    /**
     * @return mixed
     */
    protected function conditionValue()
    {
        return $this->builder->getAttributes()[$this->builder->getPrimaryKey()];
    }
}
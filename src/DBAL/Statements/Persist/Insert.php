<?php

namespace Stitch\DBAL\Statements\Persist;

use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as Builder;

/**
 * Class Insert
 * @package Stitch\DBAL\Statements\Persist
 */
class Insert extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Insert constructor.
     * @param Builder builder
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
        $this->push(
            'INSERT INTO ' . $this->builder->getTable()
        )->push(
            $this->columns()
        )->push(
            'VALUES'
        )->push(
            $this->component($this->placeholders())->bindMany($this->values())
        );
    }

    /**
     * @return string
     */
    protected function columns()
    {
        $columns = array_keys($this->builder->getAttributes());

        return '(' . implode(', ', $columns) . ')';
    }

    /**
     * @return string
     */
    protected function placeholders()
    {
        $placeholders = array_fill(0, count($this->builder->getAttributes()), '?');

        return '(' . implode(', ', $placeholders) . ')';
    }

    /**
     * @return array
     */
    protected function values()
    {
        return array_values($this->builder->getAttributes());
    }
}
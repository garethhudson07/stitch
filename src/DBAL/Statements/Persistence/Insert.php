<?php

namespace Stitch\DBAL\Statements\Persistence;

use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as RecordBuilder;

/**
 * Class Insert
 * @package Stitch\DBAL\Statements\Persistence
 */
class Insert extends Statement
{
    /**
     * @var RecordBuilder
     */
    protected $recordBuilder;

    /**
     * Insert constructor.
     * @param RecordBuilder $recordBuilder
     */
    public function __construct(RecordBuilder $recordBuilder)
    {
        $this->recordBuilder = $recordBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $this->assembler->push(
            new Component('INSERT INTO ' . $this->recordBuilder->getTable())
        )->push(
            new Component($this->columns())
        )->push(
            new Component('VALUES')
        )->push(
            (new Component($this->placeholders()))->bindMany($this->values())
        );
    }

    /**
     * @return string
     */
    protected function columns()
    {
        $columns = array_keys($this->recordBuilder->getAttributes());

        return '(' . implode(', ', $columns) . ')';
    }

    /**
     * @return string
     */
    protected function placeholders()
    {
        $placeholders = array_fill(0, count($this->recordBuilder->getAttributes()), '?');

        return '(' . implode(', ', $placeholders) . ')';
    }

    /**
     * @return array
     */
    protected function values()
    {
        return array_values($this->recordBuilder->getAttributes());
    }
}
<?php

namespace Stitch\DBAL\Statements\Persistence;

use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as RecordBuilder;

/**
 * Class Update
 * @package Stitch\DBAL\Statements\Persistence
 */
class Update extends Statement
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
     *
     */
    protected function evaluate()
    {
        $this->assembler->push(
            new Component('UPDATE ' . $this->recordBuilder->getTable())
        )->push(
            new Component('SET')
        )->push(
            (new Component($this->assignments()))->bindMany($this->assignmentValues())
        )->push(
            new Component('WHERE')
        )->push(
            (new Component($this->condition()))->bind($this->conditionValue())
        );
    }

    /**
     * @return string
     */
    protected function assignments()
    {
        $primaryKey = $this->recordBuilder->getPrimaryKey();
        $assignments = [];

        foreach ($this->recordBuilder->getAttributes() as $name => $value) {
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
        $primaryKey = $this->recordBuilder->getPrimaryKey();
        $values = [];

        foreach ($this->recordBuilder->getAttributes() as $name => $value) {
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
        return "{$this->recordBuilder->getPrimaryKey()} = ?";
    }

    /**
     * @return mixed
     */
    protected function conditionValue()
    {
        return $this->recordBuilder->getAttributes()[$this->recordBuilder->getPrimaryKey()];
    }
}
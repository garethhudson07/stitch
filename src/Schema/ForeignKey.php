<?php

namespace Stitch\Schema;

/**
 * Class ForeignKey
 * @package Stitch\Schema
 */
class ForeignKey
{
    /**
     * @var Column
     */
    protected $localColumn;

    /**
     * @var string
     */
    protected $referenceColumnName;

    /**
     * @var string
     */
    protected $referenceTableName;

    /**
     * ForeignKey constructor.
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->localColumn = $column;
    }

    /**
     * @param string $column
     */
    public function references(string $column)
    {
        $this->referenceColumnName = $column;
    }

    /**
     * @param string $table
     */
    public function on(string $table)
    {
        $this->referenceTableName = $table;
    }

    /**
     * @return Column
     */
    public function getLocalColumn()
    {
        return $this->localColumn;
    }

    /**
     * @return mixed
     */
    public function getReferenceColumnName()
    {
        return $this->referenceColumnName;
    }

    /**
     * @return mixed
     */
    public function getReferenceTableName()
    {
        return $this->referenceTableName;
    }
}
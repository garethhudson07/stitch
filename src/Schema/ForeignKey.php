<?php

namespace Stitch\Schema;

class ForeignKey
{
    protected $localColumn;

    protected $referenceColumnName;

    protected $referenceTableName;

    public function __construct(Column $column)
    {
        $this->localColumn = $column;
    }

    public function references(string $column)
    {
        $this->referenceColumnName = $column;
    }

    public function on(string $table)
    {
        $this->referenceTableName = $table;
    }

    public function getLocalColumn()
    {
        return $this->localColumn;
    }

    public function getReferenceColumnName()
    {
        return $this->referenceColumnName;
    }

    public function getReferenceTableName()
    {
        return $this->referenceTableName;
    }
}
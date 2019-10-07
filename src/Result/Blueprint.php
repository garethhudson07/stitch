<?php

namespace Stitch\Result;

use Stitch\DBAL\Builders\Selection;
use Stitch\Queries\Joins\BelongsTo;
use Stitch\Queries\Joins\HasOne;
use Stitch\DBAL\Syntax\Select as SelectSyntax;

class Blueprint
{
    protected $query;

    protected $columnMap = [];

    protected $relations = [];

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @param Selection $selection
     * @param SelectSyntax $syntax
     * @return Blueprint
     */
    public function map(Selection $selection, SelectSyntax $syntax)
    {
        $table = $this->query->getModel()->getTable();

        foreach ($selection->getColumns() as $column)
        {
            $schema = $column->getSchema();

            if ($table === $schema->getTable()) {
                $item = [
                    'alias' => $syntax->columnAlias($schema),
                    'schema' => $schema
                ];

                $schema->isPrimary() ? $this->columnMap['primary'] = $item : $this->columnMap[] = $item;
            }
        }

        return $this->mapRelations($selection, $syntax);
    }

    /**
     * @param Selection $selection
     * @param SelectSyntax $syntax
     * @return $this
     */
    protected function mapRelations(Selection $selection, SelectSyntax $syntax)
    {
        foreach ($this->query->getJoins()->all() as $key => $join) {
            $this->relations[$key] = (new static($join))->map($selection, $syntax);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function relations()
    {
        return $this->relations;
    }

    /**
     * @return array
     */
    public function columnMap()
    {
        return $this->columnMap;
    }

    /**
     * @return mixed
     */
    public function primaryKeyMap()
    {
        return $this->columnMap['primary'];
    }


    /**
     * @return Record|Set
     */
    public function newResult()
    {
        if ($this->query instanceof HasOne || $this->query instanceof BelongsTo) {
            return $this->newRecord();
        }

        return $this->newSet();
    }

    /**
     * @return Set
     */
    public function newSet()
    {
        return new Set($this);
    }

    /**
     * @return Record
     */
    public function newRecord()
    {
        return new Record($this);
    }
}

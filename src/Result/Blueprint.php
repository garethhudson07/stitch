<?php
/**
 * Created by PhpStorm.
 * User: garethhudson
 * Date: 01/09/2019
 * Time: 13:27
 */

namespace Stitch\Result;


use Stitch\Queries\Joins\Collection as Joins;
use Stitch\Schema\Table;

class Blueprint
{
    protected $table;

    protected $joins;

    protected $relations = [];

    public function __construct(Table $table, Joins $joins)
    {
        $this->table = $table;
        $this->joins = $joins;
    }

    public function map($selection)
    {
        foreach ($selection->resolveolumns() as $column)
        {
            if ($this->table === $column->getSchema()->getTable()) {
                $this->map[] = Grammer::column()->inlcude(['path', 'alias'])->extract($column);
            }
        }

        foreach ($this->joins->all() as $key => $join) {
            $this->relations[$key] = (new static(
                $join->getModel()->getTable(),
                $join->getJoins()
            ))->map($selection);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}

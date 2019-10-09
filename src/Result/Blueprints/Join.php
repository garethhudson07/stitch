<?php

namespace Stitch\Result\Blueprints;

use Stitch\Queries\Query;

class Join extends Blueprint
{
    protected $query;

    /**
     * Table constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return \Stitch\DBAL\Schema\Table
     */
    public function getSchema()
    {
        return $this->query->getModel()->getTable();
    }

    /**
     * @return array|\Stitch\Queries\Joins\Collection
     */
    public function getJoins()
    {
        return $this->query->getJoins();
    }

    /**
     * @return \Stitch\Result\Set
     */
    public function result()
    {
        return $this->resultSet();
    }

    public function activeRecord()
    {

    }

    public function activeRecordCollection()
    {

    }
}
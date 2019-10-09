<?php

namespace Stitch\Result\Blueprints;

use Stitch\Relations\Relation;
use Stitch\Result\Record;
use Stitch\Result\Set;

class Factory
{
    protected $blueprint;

    protected $recordFactory;

    public function __construct(Blueprint $blueprint, $recordFactory)
    {
        $this->blueprint = $blueprint;
        $this->recordFactory = $recordFactory;
    }

    public function result()
    {
        if ($this->recordFactory instanceof Relation) {
            return $this->recordFactory->associatesOne() ? $this->resultRecord() : $this->resultSet();
        }

        return $this->resultSet();
    }

    /**
     * @return Set
     */
    public function resultSet()
    {
        return new Set($this->blueprint);
    }

    /**
     * @return Record
     */
    public function resultRecord()
    {
        return new Record($this->blueprint);
    }

    /**
     * @return mixed
     */
    public function activeRecord()
    {
        return $this->recordFactory->record();
    }

    /**
     * @return mixed
     */
    public function activeRecordCollection()
    {
        return $this->recordFactory->collection();
    }
}
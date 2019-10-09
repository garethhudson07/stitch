<?php
/**
 * Created by PhpStorm.
 * User: Gareth
 * Date: 09/10/2019
 * Time: 14:25
 */

namespace Stitch\Result\Blueprints;

use Stitch\Relations\Relation;
use Stitch\Result\Set;

class Factory
{
    protected $recordFactory;

    public function __construct($recordFactory)
    {
        $this->recordFactory = $recordFactory;
    }

    public function result()
    {
        if ($this->recordFactory instanceof Relation) {
            return $this->recordFactory->associatesOne() ? $this->resultRecord() : $this->resultSet()
        }

        return $this->resultSet();
    }

    /**
     * @return Set
     */
    public function resultSet()
    {
        return new Set($this);
    }

    /**
     * @return Record
     */
    public function resultRecord()
    {
        return new Record($this);
    }
}
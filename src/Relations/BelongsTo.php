<?php

namespace Stitch\Relations;

use Stitch\Queries\Joins\BelongsTo as Join;
use Stitch\Records\Relations\Collection as RecordCollection;
use Stitch\DBAL\Schema\ForeignKey;

/**
 * Class Has
 * @package Stitch\Relations
 */
class BelongsTo extends Relation
{
    /**
     * @return $this
     */
    public function pullKeys()
    {
        if (!$this->foreignKey) {
            $this->foreignKey = $this->getForeignModel()->getTable()->getPrimaryKey();
        }

        if (!$this->localKey) {
            $this->localKey = $this->localModel->getTable()->getForeignKeyFor(
                $this->foreignKey
            )->getLocalColumn();
        }

        return $this;
    }

    /**
     * @return mixed|Join
     */
    public function join()
    {
        return new Join($this->getForeignModel(), $this->joinBuilder(), $this);
    }

    /**
     * @return mixed|RecordCollection
     */
    public function make()
    {
    }

    /**
     * @param array $attributes
     * @return BelongsTo
     */
    public function record(array $attributes = [])
    {
    }
}
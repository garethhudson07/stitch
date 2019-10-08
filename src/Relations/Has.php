<?php

namespace Stitch\Relations;

use Stitch\Queries\Joins\Has as Join;
use Stitch\Records\Relations\Collection as RecordCollection;
use Stitch\Records\Relations\BelongsTo;
use Stitch\DBAL\Schema\ForeignKey;

/**
 * Class Has
 * @package Stitch\Relations
 */
class Has extends Relation
{
    /**
     * @return $this
     */
    public function pullKeys()
    {
        if (!$this->localKey) {
            $this->localKey = $this->localModel->getTable()->getPrimaryKey();
        }

        if (!$this->foreignKey) {
            $this->foreignKey = $this->getForeignModel()->getTable()->getForeignKeyFor(
                $this->localKey
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
        return new RecordCollection($this->getForeignModel());
    }

    /**
     * @param array $attributes
     * @return BelongsTo
     */
    public function record(array $attributes = [])
    {
        return (new BelongsTo($this->getForeignModel(), $this))->fill($attributes);
    }
}

<?php

namespace Stitch\Relations;

use Stitch\Records\Relations\BelongsTo;

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
     * @param array $attributes
     * @return BelongsTo
     */
    public function record(array $attributes = [])
    {
        return (new BelongsTo(
            $this->getForeignModel(),
            $this
        ))->fill($attributes);
    }
}

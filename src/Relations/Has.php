<?php

namespace Stitch\Relations;

use Stitch\Records\Relations\BelongsTo as Record;

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
            if ($this->foreignKey) {
                $this->localKey = $this->localModel->getTable()->getColumn(
                    $this->getForeignModel()->getTable()->getForeignKeyFrom($this->foreignKey)->getReferenceColumnName()
                );
            } else {
                $this->localKey = $this->localModel->getTable()->getPrimaryKey();
            }
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
     * @return Record
     */
    public function record(array $attributes = [])
    {
        return (new Record(
            $this->getForeignModel(),
            $this
        ))->fill($attributes);
    }
}

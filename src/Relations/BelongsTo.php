<?php

namespace Stitch\Relations;

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
}

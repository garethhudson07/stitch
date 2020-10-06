<?php

namespace Stitch\Relations;

/**
 * Class Has
 * @package Stitch\Relations
 */
class BelongsTo extends Relation
{
    protected $associate = 'one';

    /**
     * @return $this
     */
    public function pullKeys()
    {
        if (!$this->foreignKey) {
            if ($this->localKey) {
                $this->foreignKey = $this->getForeignModel()->getTable()->getColumn(
                    $this->localModel->getTable()->getForeignKeyFrom($this->localKey)->getReferenceColumnName()
                );
            } else {
                $this->foreignKey = $this->getForeignModel()->getTable()->getPrimaryKey();
            }
        }

        if (!$this->localKey) {
            $this->localKey = $this->localModel->getTable()->getForeignKeyFor(
                $this->foreignKey
            )->getLocalColumn();
        }

        return $this;
    }
}

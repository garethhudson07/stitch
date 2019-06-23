<?php

namespace Stitch\Schema;

class KeyChain
{
    protected $primary;

    protected $foreign = [];

    public function setPrimary(Column $column)
    {
        $this->primary = $column;

        return $this;
    }

    public function addForeign(ForeignKey $foreign)
    {
        $this->foreign[$foreign->getLocalColumn()->getName()] = $foreign;

        return $this;
    }

    public function getPrimary(): ?Column
    {
        return $this->primary;
    }

    public function getForeignFrom(string $column): ?ForeignKey
    {
        if (array_key_exists($column, $this->foreign)) {
            return $this->foreign[$column];
        }

        return null;
    }

    public function getForeignFor(string $table, string $column): ?ForeignKey
    {
        foreach ($this->foreign as $foreign) {
            if ($foreign->getReferenceTableName() === $table && $foreign->getReferenceColumnName() === $column) {
                return $foreign;
            }
        }

        return null;
    }
}
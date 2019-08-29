<?php

namespace Stitch\Schema;

/**
 * Class KeyChain
 * @package Stitch\Schema
 */
class KeyChain
{
    /**
     * @var Column
     */
    protected $primary;

    /**
     * @var array
     */
    protected $foreign = [];

    /**
     * @param ForeignKey $foreign
     * @return $this
     */
    public function addForeign(ForeignKey $foreign)
    {
        $this->foreign[$foreign->getLocalColumn()->getName()] = $foreign;

        return $this;
    }

    /**
     * @return Column|null
     */
    public function getPrimary(): ?Column
    {
        return $this->primary;
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function setPrimary(Column $column)
    {
        $this->primary = $column;

        return $this;
    }

    /**
     * @param string $column
     * @return ForeignKey|null
     */
    public function getForeignFrom(Column $column): ?ForeignKey
    {
        $key = $column->getName();

        if (array_key_exists($key, $this->foreign)) {
            return $this->foreign[$key];
        }

        return null;
    }

    /**
     * @param string $table
     * @param string $column
     * @return ForeignKey|null
     */
    public function getForeignFor(Column $column): ?ForeignKey
    {
        $table = $column->getTable()->getName();
        $name = $column->getName();

        foreach ($this->foreign as $foreign) {
            if ($foreign->getReferenceTableName() === $table && $foreign->getReferenceColumnName() === $name) {
                return $foreign;
            }
        }

        return null;
    }
}
<?php

namespace Stitch\DBAL\Schema;

/**
 * Class Column
 * @package Stitch\DBAL\Schema
 */
class Column
{
    /**
     * @var KeyChain
     */
    protected $table;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $increments = false;

    /**
     * Column constructor.
     * @param Table $table
     * @param string $name
     * @param string $type
     */
    public function __construct(Table $table, string $name, string $type)
    {
        $this->table = $table;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return $this
     */
    public function increments()
    {
        $this->increments = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function primary()
    {
        $this->table->getkeyChain()->setPrimary($this);

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->table->getPrimaryKey() === $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function references(string $column)
    {
        $this->foreignKey()->references($column);

        return $this;
    }

    /**
     * @return ForeignKey|null
     */
    protected function foreignKey()
    {
        $keyChain = $this->table->getkeyChain();

        if (!$foreignKey = $keyChain->getForeignFrom($this)) {
            $foreignKey = new ForeignKey($this);
            $keyChain->addForeign($foreignKey);
        }

        return $foreignKey;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function on(string $table)
    {
        $this->foreignKey()->on($table);

        return $this;
    }

    /**
     * @return KeyChain|Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function incrementing()
    {
        return $this->increments;
    }

    /**
     * @param $value
     * @return int|mixed
     */
    public function cast($value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->type) {
            case 'integer':
                return (int)$value;

            case 'json':
                return json_decode($value, true);

            default:
                return $value;
        }
    }
}

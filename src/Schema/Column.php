<?php

namespace Stitch\Schema;

/**
 * Class Column
 * @package Stitch\Schema
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
    protected $autoIncrement = false;

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
    public function autoIncrement()
    {
        $this->autoIncrement = true;

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

        if (!$foreignKey = $keyChain->getForeignFrom($this->name)) {
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
    public function getPath()
    {
        return "{$this->table->getName()}.{$this->name}";
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return "{$this->table->getName()}_{$this->name}";
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
    public function autoIncrements()
    {
        return $this->autoIncrement;
    }

    /**
     * @param $value
     * @return int|mixed
     */
    public function cast($value)
    {
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

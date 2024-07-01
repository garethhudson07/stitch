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
     * @var bool
     */
    protected $readonly = false;

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

    public function rename(string $name)
    {
        $this->table->removeColumn($this->name);

        $this->name = $name;

        $this->table->pushColumn($this);

        return $this;
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
        $this->readonly();

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
     * @return bool
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @return $this
     */
    public function readonly()
    {
        $this->readonly = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function writeable()
    {
        $this->readonly = false;

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
                return intval($value);

            case 'boolean':
                return boolval($value);

            case 'json':
                return json_decode($value, true);

            default:
                return $value;
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function encode($value)
    {
        switch ($this->type) {
            case 'boolean':
                return $value ? 1 : 0;

            case 'json':
                return json_encode($value);
        }

        return $value;
    }
}

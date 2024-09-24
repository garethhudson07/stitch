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
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var bool
     */
    protected $tempWriteable = false;

    /**
     * @var int
     */
    protected $precision = 2;

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
     * @return bool
     */
    public function isWriteable()
    {
        return !$this->readonly;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return !$this->hidden;
    }

    /**
     * @return bool
     */
    public function isTempWriteable()
    {
        return $this->tempWriteable;
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
     * @return $this
     */
    public function hidden()
    {
        $this->hidden = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function visible()
    {
        $this->hidden = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function tempWriteable()
    {
        $this->tempWriteable = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function precision(int $precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return $this
     */
    public function resetTempWriteable()
    {
        $this->tempWriteable = false;

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

            case 'decimal':
                return round($value, $this->precision);

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
            case 'integer':
                return $value === null ? null : intval($value);

            case 'decimal':
                return $value === null ? null : round($value, $this->precision);

            case 'boolean':
                return $value ? 1 : 0;

            case 'json':
                return $value === null ? null : json_encode($value);
        }

        return $value;
    }
}

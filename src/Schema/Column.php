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
    protected $keyChain;

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
     * @param KeyChain $keyChain
     * @param string $name
     * @param string $type
     */
    public function __construct(KeyChain $keyChain, string $name, string $type)
    {
        $this->keyChain = $keyChain;
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
        $this->keyChain->setPrimary($this);

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
        if (!$foreignKey = $this->keyChain->getForeignFrom($this->name)) {
            $foreignKey = new ForeignKey($this);
            $this->keyChain->addForeign($foreignKey);
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

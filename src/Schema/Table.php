<?php

namespace Stitch\Schema;
use Stitch\DBAL\Connection;
use Stitch\Stitch;

/**
 * Class Table
 * @package Stitch\Schema
 */
class Table
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var KeyChain
     */
    protected $keyChain;

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->keyChain = new KeyChain();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function connection(string $name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * @return void
     */
    public function timestamps(): void
    {
        $this->addColumn('timestamp', 'created_at');
        $this->addColumn('timestamp', 'updated_at');
    }

    /**
     * @param $type
     * @param $name
     * @return Column
     */
    protected function addColumn($type, $name): Column
    {
        $column = new Column($this, $name, $type);

        $this->columns[$name] = $column;

        return $column;
    }

    /**
     * @return Column
     */
    public function softDeletes(): Column
    {
        return $this->addColumn('timestamp', 'deleted_at');
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return Column
     */
    public function __call(string $method, array $arguments)
    {
        return $this->addColumn($method, $arguments[0]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return Stitch::getConnection($this->connection);
    }

    /**
     * @return KeyChain
     */
    public function getKeyChain()
    {
        return $this->keyChain;
    }

    /**
     * @param string $name
     * @return Column
     */
    public function getColumn(string $name): Column
    {
        return array_key_exists($name, $this->columns) ? $this->columns[$name] : null;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Column|null
     */
    public function getPrimaryKey(): ?Column
    {
        return $this->keyChain->getPrimary();
    }

    /**
     * @param Column $column
     * @return ForeignKey|null
     */
    public function getForeignKeyFrom(Column $column): ?ForeignKey
    {
        return $this->keyChain->getForeignFrom($column);
    }

    /**
     * @param Column $column
     * @return ForeignKey|null
     */
    public function getForeignKeyFor(Column $column): ?ForeignKey
    {
        return $this->keyChain->getForeignFor($column);
    }
}

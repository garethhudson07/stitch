<?php

namespace Stitch\DBAL\Schema;
use Stitch\DBAL\Connection;
use Stitch\Stitch;

/**
 * Class Table
 * @package Stitch\DBAL\Schema
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
    protected $database;

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
     * @param string $path
     * @return $this|Table
     */
    public function database(string $path)
    {
        $pieces = explode('.', $path);

        $this->database = array_pop($pieces);

        if ($pieces) {
            return $this->connection($pieces[0]);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function connection(string $name)
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

        $this->pushColumn($column);

        return $column;
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function pushColumn(Column $column)
    {
        $this->columns[$column->getName()] = $column;

        return $this;
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
     * @return Database
     */
    public function getDatabase(): Database
    {
        return Stitch::getConnection($this->connection)->getDatabase($this->database);
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
        if (!$this->hasColumn($name)) {
            throw new Exception("Column [$name] not found on the [{$this->name}] table");
        }

        return  $this->columns[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
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

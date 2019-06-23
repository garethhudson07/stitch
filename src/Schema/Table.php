<?php

namespace Stitch\Schema;

use Exception;

class Table
{
    protected $name;

    protected $columns = [];

    protected $keyChain;

    public function __construct()
    {
        $this->keyChain = new KeyChain();
    }

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function timestamps(): void
    {
        $this->addColumn('timestamp', 'created_at');
        $this->addColumn('timestamp', 'updated_at');
    }

    public function softDeletes(): Column
    {
        return $this->addColumn('timestamp', 'deleted_at');
    }

    public function __call(string $method, array $arguments)
    {
        return $this->addColumn($method, $arguments[0]);
    }

    protected function addColumn($type, $name): Column
    {
        $column = new Column(
            $this->keyChain,
            $name,
            $type
        );

        $this->columns[$name] = $column;

        return $column;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumn(string $name): Column
    {
        return array_key_exists($name, $this->columns) ? $this->columns[$name] : null;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPrimaryKey(): ?Column
    {
        return $this->keyChain->getPrimary();
    }

    public function getForeignKeyFrom(string $column): ?ForeignKey
    {
        return $this->keyChain->getForeignFrom($column);
    }

    public function getForeignKeyFor(string $table, string $column): ?ForeignKey
    {
        return $this->keyChain->getForeignFor($table, $column);
    }
}

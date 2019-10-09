<?php

namespace Stitch\Result\Blueprints;

use Stitch\DBAL\Builders\Selection;
use Stitch\DBAL\Syntax\Select as SelectSyntax;

class ColumnMap
{
    protected $table;

    protected $items = [];

    /**
     * ColumnMap constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param Selection $selection
     * @param SelectSyntax $syntax
     * @return $this
     */
    public function build(Selection $selection, SelectSyntax $syntax)
    {
        foreach ($selection->getColumns() as $column)
        {
            $schema = $column->getSchema();

            if ($this->table === $schema->getTable()) {
                $item = [
                    'alias' => $syntax->columnAlias($schema),
                    'schema' => $schema
                ];

                $schema->isPrimary() ? $this->items['primary'] = $item : $this->items[] = $item;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function primaryKey()
    {
        return $this->items['primary'] ?? null;
    }
}

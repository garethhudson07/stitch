<?php

namespace Stitch\Records\Relations;

use Stitch\Collection;

class Has extends Collection
{
    protected $record;

    protected $blueprint;

    public function __construct($record, $blueprint)
    {
        $this->record = $record;
        $this->blueprint = $blueprint;
    }

    public function make(array $attributes)
    {
        return $this->associateScope($this->blueprint->getForeignModel()->make($attributes));
    }

    public function new(array $attributes)
    {
        return $this->push($this->make($attributes));
    }

    public function save()
    {
        // Save collection
    }

    public function associateScope($record)
    {
        $foreignKey = $this->blueprint->getForeignKey();

        $record->{$foreignKey->getLocalColumn()->getName()} = $this->record->{$foreignKey->getReferenceColumnName()};

        return $record;
    }
}
<?php

namespace Stitch\Records\Relations;

use Stitch\Collection as BaseCollection;

class Collection extends BaseCollection
{
    protected $record;

    protected $blueprint;

    public function __construct($blueprint)
    {
        $this->blueprint = $blueprint;
    }

    public function make(array $attributes)
    {
        return $this->blueprint->getForeignModel()->make($attributes);
    }

    public function new(array $attributes)
    {
        return $this->push($this->make($attributes));
    }

    public function save()
    {
        foreach ($this->items as $item) {
            $item->associate();
        }
    }
}

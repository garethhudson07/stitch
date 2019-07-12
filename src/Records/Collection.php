<?php

namespace Stitch\Records;

use Stitch\Collection as BaseCollection;

class Collection extends BaseCollection
{
    protected $factory;

    /**
     * Collection constructor.
     * @param $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $attributes
     * @return Record
     */
    public function make(array $attributes = [])
    {
        return $this->factory->make($attributes);
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function new(array $attributes)
    {
        return $this->push($this->make($attributes));
    }

    public function save()
    {
        foreach ($this->items as $item) {
            $item->save();
        }
    }
}

<?php

namespace Stitch\Records;

use Stitch\Collection as BaseCollection;
use Stitch\Model;

class Collection extends BaseCollection
{
    protected $model;

    /**
     * Collection constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return Record
     */
    public function make(array $attributes = [])
    {
        return $this->model->make($attributes);
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function new(array $attributes)
    {
        return $this->push($this->make($attributes));
    }

    /**
     * @return $this
     */
    public function save()
    {
        foreach ($this->items as $item) {
            $item->save();
        }

        return $this;
    }
}

<?php

namespace Stitch\Records;

use Stitch\Aggregate\Set;
use Stitch\Model;

class Aggregate extends Set
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
    public function record(array $attributes = [])
    {
        return $this->model->record($attributes);
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

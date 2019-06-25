<?php

namespace Stitch\Result;

use Stitch\Model;
use Stitch\Collection;

class Hydrator
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $result
     * @return Collection|Record
     */
    public function hydrate($result)
    {
        return $result instanceof Set ? $this->many($result) : $this->one($result);
    }

    /**
     * @param Record $record
     * @return Record
     */
    public function one(Record $record)
    {
        $instance = $this->model->make($record->getData(), true);

        foreach ($record->getRelations() as $key => $relation) {
            $instance->setRelation(
                $key,
                (new static($this->model->getRelation($key)->getForeignModel()))->hydrate($relation)
            );
        }

        return $instance;
    }

    /**
     * @param Set $result
     * @return Collection
     */
    public function many(Set $set)
    {
        $items = new Collection();

        foreach ($set as $item) {
            $items->push($this->one($item));
        }

        return $items;
    }
}
<?php

namespace Stitch\Result;

use Stitch\Collection;
use Stitch\Model;

/**
 * Class Hydrator
 * @package Stitch\Result
 */
class Hydrator
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Hydrator constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $result
     * @return Collection|\Stitch\Record
     */
    public function hydrate($result)
    {
        return $result instanceof Set ? $this->many($result) : $this->one($result);
    }

    /**
     * @param Set $set
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

    /**
     * @param Record $record
     * @return \Stitch\Record
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
}
<?php

namespace Stitch\Result;

use Stitch\Collection;
use Stitch\Queries\Query;

/**
 * Class Hydrator
 * @package Stitch\Result
 */
class Hydrator
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * Hydrator constructor.
     * @param query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
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
        $instance = $this->query->getModel()->make($record->getData())->exists();

        foreach ($record->getRelations() as $key => $relation) {
            $relatedQuery = $relation->getQuery();

            $instance->setRelation(
                $key,
                $relatedQuery->getBlueprint()->make()->fill(
                    (new static($relatedQuery))->hydrate($relation)
                )
            );
        }

        return $instance;
    }
}
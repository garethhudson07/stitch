<?php

namespace Stitch\Result;

use Stitch\Result\Record as ResultRecord;
use Stitch\Records\Record as ActiveRecord;
use Stitch\Records\Collection;

/**
 * Class Hydrator
 * @package Stitch\Result
 */
class Hydrator
{
    /**
     * @param $instance
     * @param $result
     * @return Collection|ActiveRecord
     */
    public static function hydrate($instance, $result)
    {
        return $result instanceof Set ? static::many($instance, $result) : static::one($instance, $result);
    }

    /**
     * @param Collection $collection
     * @param Set $set
     * @return Collection
     */
    public static function many(Collection $collection, Set $set)
    {
        foreach ($set as $item) {
            $collection->push(
                static::one($collection->make(), $item)
            );
        }

        return $collection;
    }

    /**
     * @param ActiveRecord $activeRecord
     * @param Record $resultRecord
     * @return ActiveRecord
     */
    public static function one(ActiveRecord $activeRecord, ResultRecord $resultRecord)
    {
        $activeRecord->fill($resultRecord->getData())->exists();

        foreach ($resultRecord->getRelations() as $key => $relation) {
            $relatedQuery = $relation->getQuery();

            $activeRecord->setRelation(
                $key,
                static::hydrate($relatedQuery->getBlueprint()->make(), $relation)->associate($activeRecord)
            );
        }

        return $activeRecord;
    }
}
<?php

namespace Stitch\Queries\Paths;

use Stitch\Queries\Query;
use Stitch\Queries\Relations\Relation;

/**
 * Class Factory
 * @package Stitch\Queries\Paths
 */
class Factory
{
    /**
     * @param string $path
     * @return Path
     */
    public static function split(string $path): Path
    {
        return new Path(static::explode($path));
    }

    /**
     * @param string $path
     * @return array
     */
    protected static function explode(string $path): array
    {
        return explode('.', $path);
    }

    /**
     * @param Query $query
     * @param string $path
     * @return Bag
     */
    public static function divide(Query $query, string $path): Bag
    {
        $bag = new Bag();
        $pieces = static::explode($path);

        if (count($pieces) === 1) {
            return $bag->setColumn(
                new Column(
                    $query->getModel()->getTable()->getColumn($pieces[0]),
                    $pieces
                )
            );
        }

        $relation = null;
        $relations = $query->getRelations();

        foreach ($pieces as $key => $piece) {
            /** @var Relation $relation */

            if (!array_key_exists($piece, $relations)) {
                return $bag->setRelation(
                    new Path(array_slice($pieces, 0, $key))
                )->setColumn(
                    new Column(
                        $relation->getBlueprint()->getForeignModel()->getTable()->getColumn($piece),
                        array_slice($pieces, $key)
                    )
                );
            }

            $relation = $relations[$piece];
            $relations = $relation->getRelations();
        }

        return $bag;
    }
}
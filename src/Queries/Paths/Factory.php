<?php

namespace Stitch\Queries\Paths;

use Stitch\Model;
use Stitch\Relations\Relation;

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
     * @param Model $model
     * @param string $path
     * @return array
     */
    public static function divide(Model $model, string $path): array
    {
        $pieces = static::explode($path);
        $relation = null;
        $relations = $model->getRelations();

        foreach ($pieces as $key => $piece) {
            if (!$relations->has($piece)) {

                /** @var Relation $relation */

                return [
                    'relation' => new Path(array_slice($pieces, 0, $key)),
                    'column' => new Column(
                        $relation->getForeignModel()->getTable()->getColumn($piece),
                        array_slice($pieces, $key)
                    )
                ];
            }

            $relation = $relations->get($piece);
            $relations = $relation->getForeignModel()->getRelations();
        }
    }
}
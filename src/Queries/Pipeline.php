<?php

namespace Stitch\Queries;

use Stitch\Aggregate\Set;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\JsonPath as JsonPathBuilder;

/**
 * Class Path
 * @package Stitch\Select\Paths
 */
class Pipeline extends Set
{
    /**
     * @param Query $query
     * @param string $path
     * @return static
     */
    public static function build(Query $query, string $path)
    {
        $joinable = $query;
        $model = $query->getModel();
        $instance = new static();
        $path = Path::make($path);

        foreach ($path as $key => $piece) {
            $relations = $model->getRelations();

            if (!$relations->has($piece)) {
                $columnPieces = $path->slice($key)->toArray();

                $columnBuilder = (new ColumnBuilder(
                    $model->getTable()->getColumn(array_shift($columnPieces))
                ))->table(
                    $joinable->getBuilder()
                );

                if (count($columnPieces) > 0) {
                    $columnBuilder->jsonPath(
                        (new JsonPathBuilder())->merge($columnPieces)
                    );
                }

                $instance->push($columnBuilder);

                break;
            }

            $relation = $relations->get($piece);
            $model = $relation->getForeignModel();

            if (!$join = $joinable->getJoins()->get($relation->getName())) {
                $join = $relation->join($path->before($key + 1));
                $join->apply($joinable->getBuilder());

                // Add this join to our query
                $joinable->getJoins()->set($relation->getName(), $join);
            }

            $instance->push($join);
            $joinable = $join;
        }

        return $instance;
    }
}

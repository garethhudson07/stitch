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
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @param Query $query
     * @param string $pipeline
     * @return static
     */
    public static function build(Query $query, string $pipeline)
    {
        $joinable = $query;
        $model = $query->getModel();
        $instance = new static();
        $pieces = explode(static::$delimiter, $pipeline);

        foreach ($pieces as $key => $piece) {
            $relations = $model->getRelations();

            if (!$relations->has($piece)) {
                $columnPieces = array_slice($pieces, $key);

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
                $join = $relation->join();
                $join->apply($joinable->getBuilder());
                $joinable->getJoins()->set($relation->getName(), $join);
            }

            $instance->push($join);
            $joinable = $join;
        }

        return $instance;
    }
}

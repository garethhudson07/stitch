<?php

namespace Stitch\Queries\Paths;

use Stitch\Model;

/**
 * Class Factory
 * @package Stitch\Queries\Paths
 */
class Factory
{
    protected $model;

    /**
     * Factory constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $path
     * @return Path
     */
    public function explode(string $path): Path
    {
        return Path::explode($path);
    }

    /**
     * @param string $path
     * @return Bag
     */
    public function split(string $path): Bag
    {
        $bag = new Bag();
        $pieces = Path::explode($path);
        $relation = null;
        $relations = $this->model->getRelations();

        if ($pieces->count() === 1) {
            return $bag->setColumn(
                new Column($this->model->getTable()->getColumn($pieces->first()), $pieces->all())
            );
        }

        foreach ($pieces as $key => $piece) {
            if (!$relations->has($piece)) {
                return $bag->setRelation($pieces->before($key))->setColumn(
                    new Column($relation->getForeignModel()->getTable()->getColumn($piece), $pieces->slice($key)->all())
                );
            }

            $relation = $relations->get($piece);
            $relations = $relation->getLocalModel()->getRelations();
        }

        return $bag;
    }
}
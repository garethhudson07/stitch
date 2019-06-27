<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\Queries\Query;
use Stitch\Relations\Collection as Relations;
use Stitch\Relations\Has;
use Stitch\Relations\HasOne;
use Stitch\Relations\ManyToMany;
use Stitch\Relations\Relation;
use Stitch\Schema\Table;

/**
 * Class Model
 * @package Stitch
 */
class Model
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Relations
     */
    protected $relations;

    /**
     * Model constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
        $this->relations = new Relations();
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param array $attributes
     * @param bool $exists
     * @return Record
     */
    public function make(array $attributes = [], $exists = false)
    {
        return new Record($this, $attributes, $exists);
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function hasMany(...$arguments)
    {
        return $this->addRelation(array_merge([Has::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function hasOne(...$arguments)
    {
        return $this->addRelation(array_merge([HasOne::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function manyToMany(...$arguments)
    {
        return $this->addRelation(array_merge([ManyToMany::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    protected function addRelation(array $arguments)
    {
        $class = $arguments[0];
        $name = $arguments[1];

        if ($arguments[2] instanceof Closure) {
            $this->relations->register(
                $name,
                function () use ($class, $arguments) {
                    $relation = new $class($this);
                    $this->bootRelation($relation, $arguments[2]);

                    return $relation;
                }
            );
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            $relation = (new $class($this))->foreignModel($arguments[2]);
            $this->bootRelation($relation, $arguments[3] ?? null);
            $this->relations->add($name, $relation);
        }

        return $this;
    }

    /**
     * @param Relation $relation
     * @param Closure|null $callback
     * @return $this
     */
    protected function bootRelation(Relation $relation, ?Closure $callback = null)
    {
        if ($callback) {
            $callback($relation);
        }

        $relation->boot();

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function hasOne(...$arguments)
    {
        return $this->addRelation(...array_merge([HasOne::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function manyToMany(...$arguments)
    {
        return $this->addRelation(...array_merge([ManyToMany::class], $arguments));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelation(string $name)
    {
        return $this->relations->has($name);
    }

    /**
     * @return Relations
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param string $name
     * @return Relation|null
     */
    public function getRelation(string $name): ?Relation
    {
        return $this->relations->get($name);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->query()->{$method}(...$arguments);
    }

    /**
     * @return Query
     */
    public function query()
    {
        return (new Query(
            $this,
            new QueryBuilder(
                $this->table->getName(),
                $this->table->getPrimaryKey()->getName()
            )
        ));
    }
}
<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\Events\Emitter;
use Stitch\Events\Event;
use Stitch\Queries\Query;
use Stitch\Relations\BelongsTo;
use Stitch\Relations\Aggregate as Relations;
use Stitch\Relations\Has;
use Stitch\Relations\HasOne;
use Stitch\Relations\ManyToMany;
use Stitch\Relations\Relation;
use Stitch\DBAL\Schema\Table;
use Stitch\Records\Record;
use Stitch\Records\Aggregate as RecordAggregate;

/**
 * Class Model
 * @package Stitch
 * @method Query where(...$arguments)
 * @method Query with(...$pipelines)
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

    protected $eventEmitter;

    /**
     * Model constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
        $this->relations = new Relations();
        $this->eventEmitter =  new Emitter();
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
     * @return Record
     */
    public function record(array $attributes = [])
    {
        return (new Record($this))->fill($attributes);
    }

    /**
     * @return RecordAggregate
     */
    public function collection()
    {
        return new RecordAggregate($this);
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function hasMany(...$arguments)
    {
        return $this->includeRelation(array_merge([Has::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function belongsTo(...$arguments)
    {
        return $this->includeRelation(array_merge([BelongsTo::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return $this
     */
    protected function includeRelation(array $arguments)
    {
        $class = $arguments[0];
        $name = $arguments[1];

        if (!array_key_exists(2, $arguments) || $arguments[2] instanceof Closure) {
            $this->registerRelation(
                $name,
                function () use ($class, $name, $arguments) {
                    $relation = new $class($name, $this);
                    $this->bootRelation($relation, $arguments[2] ?? null);

                    return $relation;
                }
            );
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            $relation = (new $class($name, $this))->foreignModel($arguments[2]);

            $this->bootRelation($relation, $arguments[3] ?? null)
                ->addRelation($relation);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param Closure $callback
     * @return $this
     */
    public function registerRelation(string $name, Closure $callback)
    {
        $this->relations->register($name, $callback);

        return $this;
    }

    /**
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(Relation $relation)
    {
        $this->relations->add($relation);

        return $this;
    }

    /**
     * @param Relation $relation
     * @param Closure|null $callback
     * @return $this
     */
    protected function bootRelation(Relation $relation, ?Closure $callback = null)
    {
        if (!$relation->getForeignModel() && !$relation->getBinding()) {
            $relation->bind($relation->getName());
        }

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
        return $this->includeRelation(array_merge([HasOne::class], $arguments));
    }

    /**
     * @param array ...$arguments
     * @return Model
     */
    public function manyToMany(...$arguments)
    {
        return $this->includeRelation(array_merge([ManyToMany::class], $arguments));
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
     * @param string $id
     * @return null|\Stitch\Result\Record
     */
    public function find(string $id)
    {
        return $this->query()->where(
            $this->table->getPrimaryKey()->getName(),
            $id
        )->first();
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
    public function query(): Query
    {
        return Query::make($this, new QueryBuilder($this->table));
    }

    /**
     * @param Closure $listener
     * @return $this
     */
    public function listen(Closure $listener): self
    {
        $this->eventEmitter->listen($listener);

        return $this;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function makeEvent(string $name): Event
    {
        return $this->eventEmitter->makeEvent($name);
    }
}

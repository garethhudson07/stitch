<?php

namespace Stitch;

use Closure;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Connection;
use Stitch\Queries\Query;
use Stitch\Relations\Collection as Relations;
use Stitch\Relations\Has;
use Stitch\Relations\HasOne;
use Stitch\Relations\ManyToMany;
use Stitch\Relations\Relation;
use Stitch\Schema\Table;
use Stitch\Records\Record;
use Stitch\Records\Collection as RecordCollection;

/**
 * Class Model
 * @package Stitch
 */
class Model
{
    /**
     * @var string
     */
    protected $connection = 'default';

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
     * @return Record
     */
    public function make(array $attributes = [])
    {
        return (new Record($this))->fill($attributes);
    }

    /**
     * @return RecordCollection
     */
    public function collection()
    {
        return new RecordCollection($this);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function connection(string $name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return Stitch::getConnection($this->connection);
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
     * @return null|Record
     */
    public function find(string $id)
    {
        return $this->query()->where($this->table->getPrimaryKey()->getName(), $id)->first();
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
        return new Query($this, new QueryBuilder($this->table));
    }
}
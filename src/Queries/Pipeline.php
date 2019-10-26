<?php

namespace Stitch\Queries;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Stitch\Model;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\JsonPath as JsonPathBuilder;
use Stitch\Relations\Relation;

/**
 * Class Path
 * @package Stitch\Select\Paths
 */
class Pipeline implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    protected static $delimiter = '.';

    /**
     * @var array
     */
    protected $pipes;

    /**
     * Path constructor.
     * @param array $pipes
     */
    public function __construct(array $pipes = [])
    {
        $this->pipes = $pipes;
    }

    /**
     * @param Model $model
     * @param string $pipeline
     * @return static
     */
    public static function parse(Model $model, string $pipeline)
    {
        $instance = new static();
        $pieces = explode(static::$delimiter, $pipeline);

        foreach ($pieces as $key => $piece) {
            $relations = $model->getRelations();

            if (!$relations->has($piece)) {
                $instance->push(
                    array_slice($pieces, $key)
                );

                break;
            }

            $relation = $relations->get($piece);
            $model = $relation->getForeignModel();

            $instance->push($relation);
        }

        return $instance;
    }

    /**
     * @param Query $query
     * @return ColumnBuilder
     */
    public function resolve(Query $query)
    {
        $columnPieces = $this->last();

        if ($this->count() > 1) {
            $model = $this->penultimate()->getForeignModel();
            $join = $query->getJoins()->resolve($this->before($this->count() - 1));
            $tableBuilder = $join->getBuilder();
        } else {
            $model = $query->getModel();
            $tableBuilder = $query->getBuilder();
        }

        $columnBuilder = (new ColumnBuilder(
            $model->getTable()->getColumn(array_shift($columnPieces))
        ))->table($tableBuilder);

        if (count($columnPieces) > 0) {
            $columnBuilder->jsonPath(
                (new JsonPathBuilder())->merge($columnPieces)
            );
        }

        return $columnBuilder;
    }

    /**
     * @param $pipe
     * @return $this
     */
    public function push($pipe)
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->pipes;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        return $this->pipes[0] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->pipes[count($this->pipes) - 1] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function penultimate()
    {
        return $this->pipes[count($this->pipes) - 2] ?? null;
    }

    /**
     * @param int $index
     * @return static
     */
    public function before(int $index)
    {
        return new static(array_slice($this->pipes, 0, $index));
    }

    /**
     * @param int $index
     * @return static
     */
    public function after(int $index)
    {
        return new static(array_slice($this->pipes, $index + 1));
    }

    /**
     * Count the number of pipes in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->pipes);
    }

    /**
     * Get an iterator for the pipes.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->pipes);
    }
}

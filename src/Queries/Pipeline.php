<?php

namespace Stitch\Queries;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Stitch\Model;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\JsonPath as JsonPathBuilder;

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
                $columnBuilder = new ColumnBuilder(
                    $model->getTable()->getColumn($piece)
                );

                if (count($pieces) > ($key + 1)) {
                    $columnBuilder->setJsonPath(
                        (new JsonPathBuilder())->merge(
                            array_slice($pieces, $key + 1)
                        )
                    );
                }

                $instance->push($columnBuilder);

                break;
            }

            $relation = $relations->get($piece);
            $model = $relation->getForeignModel();

            $instance->push($relation);
        }

        return $instance;
    }

    /**
     * @param $pipe
     */
    public function push($pipe)
    {
        $this->pipes[] = $pipe;
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

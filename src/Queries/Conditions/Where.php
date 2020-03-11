<?php

namespace Stitch\Queries\Conditions;

use Closure;
use Stitch\DBAL\Builders\Expression;
use Stitch\Queries\Query;

/**
 * Class Where
 * @package Stitch\Select
 */
class Where
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Expression
     */
    protected $expression;

    /**
     * Where constructor.
     * @param Query $query
     * @param Expression $expression
     */
    public function __construct(Query $query, Expression $expression)
    {
        $this->query = $query;
        $this->expression = $expression;
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = new Expression();

            $arguments[0](
                new static($this->query, $expression)
            );

            $this->expression->{$method}($expression);

            return $this;
        }

        $arguments[0] = $this->query->buildPipeline($arguments[0])->last();

        $this->expression->{$method}(...$arguments);

        return $this;
    }
}


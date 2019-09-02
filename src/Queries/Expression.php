<?php

namespace Stitch\Queries;

use Closure;
use Stitch\DBAL\Builders\Expression as Builder;

/**
 * Class Expression
 * @package Stitch\Queries
 */
class Expression
{
    /**
     * @var Query
     */
    protected $query;

    protected $builder;

    /**
     * Expression constructor.
     * @param Query $query
     */
    public function __construct(Query $query, Builder $builder)
    {
        $this->query = $query;
        $this->builder = $builder;
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $builder = new Builder();
            $expression = new static($this->query, $builder);

            $arguments[0]($expression);

            $this->builder->{$method}($builder);

            return $this;
        }

        $arguments[0] = $this->query->parsePipeline($arguments[0])->last();

        $this->builder->{$method}(...$arguments);

        return $this;
    }
}

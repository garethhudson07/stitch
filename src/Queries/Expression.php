<?php

namespace Stitch\Queries;

use Stitch\DBAL\Builders\Expression as BaseExpression;
use Stitch\DBAL\Builders\Condition;

class Expression extends BaseExpression
{
    protected $query;

    /**
     * Expression constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param array ...$arguments
     * @return Condition
     */
    protected function condition(...$arguments)
    {
        $arguments[0] = $this->query->translatePath($arguments[0]);

        return new Condition(...$arguments);
    }

    /**
     * @return static
     */
    protected function newInstance()
    {
        return new static($this->query);
    }
}
<?php

namespace Stitch\Queries\Conditions;

use Closure;
use Stitch\DBAL\Builders\Expression;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Join as Builder;

/**
 * Class Where
 * @package Stitch\Select
 */
class On
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $expression;

    /**
     * @var array
     */
    protected $aliases = [
        'on' => 'and',
        'orOn' => 'or'
    ];

    /**
     * On constructor.
     * @param Builder $builder
     * @param Expression $expression
     */
    public function __construct(Builder $builder, Expression $expression)
    {
        $this->builder = $builder;
        $this->expression = $expression;
    }

    /**
     * @param Builder $builder
     * @return static
     */
    public static function make(Builder $builder)
    {
        $expression = new Expression();
        $builder->getConditions()->and($expression);


        return new static($builder, $expression);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return $this
     */
    protected function apply(string $method, array $arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = new Expression();

            $arguments[0](
                new static($this->builder, $expression)
            );

            $this->expression->{$method}($expression);

            return $this;
        }

        if (!$arguments[0] instanceof ColumnBuilder) {
            $arguments[0] = (new ColumnBuilder(
                $this->builder->getSchema()->getColumn($arguments[0])
            ))->table($this->builder);
        }

        $this->expression->{$method}(...$arguments);

        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     */
    public function __call(string $method, array $arguments)
    {
        if ($this->aliases[$method] ?? false) {
            $method = $this->aliases[$method];
        }

        return $this->apply($method, $arguments);
    }
}

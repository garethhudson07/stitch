<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Builders\JsonPath as Builder;
use Stitch\DBAL\Syntax\Grammar;

class Json extends Path
{
    protected $builder;

    protected $column;

    public function __construct(Builder $builder, Column $column)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->column = $column;
        $this->qualifiedName->glue(Grammar::jsonAccessor());
    }

    /**
     *
     */
    public function evaluate(): void
    {
        $this->qualifiedName->push(
            $this->column->qualifiedName()
        )->push(
            '"' . implode(Grammar::qualifier(), array_merge([Grammar::jsonPrefix()], $this->builder->all())) . '"'
        );
    }
}

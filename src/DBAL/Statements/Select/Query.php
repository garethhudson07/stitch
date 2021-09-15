<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;

/**
 * Class Query
 * @package Stitch\DBAL\Statements\Select
 */
class Query extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    /**
     * Query constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder, PathResolver $paths)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->paths = $paths;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            $this->builder->hasLimit() || $this->builder->hasOffset() ?
                new Limited($this->builder, $this->paths) :
                new Unlimited($this->builder, $this->paths)
        );
    }
}

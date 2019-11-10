<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Statements\Select\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Join
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class Join extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    /**
     * Join constructor.
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
            Syntax::join(
                $this->builder->getType(),
                $this->paths->table($this->builder)
            )
        );

        $conditions = $this->builder->getConditions();

        if ($conditions->count()) {
            $this->push(
                new Expression($conditions, $this->paths)
            );
        }

        foreach ($this->builder->getJoins() as $join) {
            $this->push(
                new static($join, $this->paths)
            );
        }
    }
}

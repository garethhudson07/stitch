<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Select\Operations\Join;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Select\Operations\Select;
use Stitch\DBAL\Statements\Select\Operations\Where;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Unlimited
 * @package Stitch\DBAL\Statements\Select
 */
class Unlimited extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    /**
     * Unlimited constructor.
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
        $table = $this->paths->table($this->builder);

        $this->push(
            new Select($this->builder, $this->paths)
        )->push(
            Syntax::from()
        )->push(
            $table->qualifiedName()
        );

        if ($table->conflict()) {
            $this->push(
                Syntax::alias($table->alias())
            );
        }

        foreach ($this->builder->getJoins() as $join) {
            $this->push(
                new Join($join, $this->paths)
            );
        }

        $this->push(
            new Where($this->builder->getConditions(), $this->paths)
        );
    }
}

<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Expression as Builder;
use Stitch\DBAL\Statements\Select\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Where
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class Where extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Where constructor.
     * @param Builder $builder
     */
    public function __construct(Syntax $syntax, Builder $builder)
    {
        parent::__construct($syntax);

        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        if ($this->builder->count()) {
            $this->push(
                $this->syntax->where()
            )->push(
                new Expression($this->syntax, $this->builder)
            );
        }
    }
}

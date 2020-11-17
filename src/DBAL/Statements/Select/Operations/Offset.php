<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Limit
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class Offset extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Limit constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        parent::__construct();

        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $offset = $this->builder->getOffset();

        if ($offset) {
            $this->push(
                Syntax::offset($offset)
            );
        }
    }
}
<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;

/**
 * Class Limit
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class Limit extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Limit constructor.
     * @param Builder $builder
     */
    public function __construct(Syntax $limit, Builder $builder)
    {
        parent::__construct($limit);

        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $limit = $this->builder->getLimit();

        if ($limit) {
            $this->push(
                $this->syntax->limit($limit)
            );
        }
    }
}
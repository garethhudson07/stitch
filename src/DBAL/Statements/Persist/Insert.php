<?php

namespace Stitch\DBAL\Statements\Persist;

use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as Builder;
use Stitch\DBAL\Syntax\Insert as Syntax;

/**
 * Class Insert
 * @package Stitch\DBAL\Statements\Persist
 */
class Insert extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Insert constructor.
     * @param Builder builder
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
        $columns = $this->builder->getMutatableColumns();
        $values = array_values($columns);

        $this->push(
            Syntax::into(
                $this->builder->getSchema()
            )
        )->push(
            Syntax::columns(
                array_keys($columns)
            )
        )->push(
            (new Binder(
                Syntax::values($values)
            ))->many($values)
        );
    }
}

<?php

namespace Stitch\DBAL\Statements\Delete;

use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as Builder;
use Stitch\DBAL\Syntax\Delete as Syntax;

/**
 * Class Delete
 * @package Stitch\DBAL\Statements\Persist
 */
class Delete extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Insert constructor.
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
        $schema = $this->builder->getSchema();
        $primaryKey = $schema->getPrimaryKey();

        $this->push(
            Syntax::table($schema)
        )->push(
            Syntax::where()
        )->push(
            (new Binder(
                Syntax::scope($primaryKey)
            ))->one(
                $this->builder->getcolumns()[$primaryKey->getName()]
            )
        );
    }
}

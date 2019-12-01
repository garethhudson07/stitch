<?php

namespace Stitch\DBAL\Statements\Persist;

use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Builders\Record as Builder;
use Stitch\DBAL\Syntax\Update as Syntax;

/**
 * Class Update
 * @package Stitch\DBAL\Statements\Persist
 */
class Update extends Statement
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
        $mutatable = $this->builder->getMutatableColumns();

        $this->push(
            Syntax::table($schema)
        )->push(
            (new Binder(
                Syntax::set(array_keys($mutatable))
            ))->many(array_values($mutatable))
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

<?php

namespace Stitch\DBAL;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Statements\Persistence\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persistence\Update as UpdateStatement;
use Stitch\DBAL\Statements\Queries\Query as QueryStatement;
use Stitch\DBAL\Statements\Queries\Variables\Declaration as VariableDeclaration;
use Stitch\Stitch;

/**
 * Class Dispatcher
 * @package Stitch\DBAL
 */
class Dispatcher
{
    /**
     * @param QueryBuilder $builder
     * @return mixed
     */
    public static function select(QueryBuilder $builder)
    {
        $connection = Stitch::getConnection();

        if ($builder->limited() && $builder->getJoins()) {
            $connection->execute(
                new VariableDeclaration($builder)
            );
        }

        return $connection->select(
            new QueryStatement($builder)
        );
    }

    /**
     * @param RecordBuilder $builder
     * @return mixed
     */
    public static function insert(RecordBuilder $builder)
    {
        return Stitch::getConnection()->insert(new InsertStatement($builder));
    }

    /**
     * @param RecordBuilder $builder
     * @return mixed
     */
    public static function update(RecordBuilder $builder)
    {
        return Stitch::getConnection()->update(new UpdateStatement($builder));
    }
}
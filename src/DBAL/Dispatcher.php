<?php

namespace Stitch\DBAL;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Statements\Persistence\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persistence\Update as UpdateStatement;
use Stitch\DBAL\Statements\Queries\Query as QueryStatement;
use Stitch\DBAL\Statements\Queries\Variables\Declaration as VariableDeclaration;

/**
 * Class Dispatcher
 * @package Stitch\DBAL
 */
class Dispatcher
{
    /**
     * @param Connection $connection
     * @param QueryBuilder $builder
     * @return mixed
     */
    public static function select(Connection $connection, QueryBuilder $builder)
    {
        if (($builder->hasLimit() || $builder->hasOffset()) && $builder->getJoins()) {
            $connection->execute(
                new VariableDeclaration($builder)
            );
        }

        return $connection->select(
            new QueryStatement($builder)
        );
    }

    /**
     * @param Connection $connection
     * @param RecordBuilder $builder
     * @return string
     */
    public static function insert(Connection $connection, RecordBuilder $builder)
    {
        $connection->insert(new InsertStatement($builder));
    }

    /**
     * @param Connection $connection
     * @param RecordBuilder $builder
     */
    public static function update(Connection $connection, RecordBuilder $builder)
    {
        $connection->update(new UpdateStatement($builder));
    }
}
<?php

namespace Stitch\DBAL;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Statements\Persist\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persist\Update as UpdateStatement;
use Stitch\DBAL\Statements\Select\Query as QueryStatement;
use Stitch\DBAL\Statements\Select\Variables\Declaration as VariableDeclaration;
use Stitch\DBAL\Paths\Resolver as PathResolver;

/**
 * Class Dispatcher
 * @package Stitch\DBAL
 */
class Dispatcher
{
    /**
     * @param Connection $connection
     * @param QueryBuilder $builder
     * @param PathResolver $paths
     * @return mixed
     */
    public static function select(QueryBuilder $builder, PathResolver $paths)
    {
        $connection = $builder->getSchema()->getConnection();

        if (($builder->hasLimit() || $builder->hasOffset()) && count($builder->getJoins())) {
            $connection->execute(
                (new VariableDeclaration($builder, $paths))
            );
        }

        return $connection->select(
            new QueryStatement($builder, $paths)
        );
    }

    /**
     * @param Connection $connection
     * @param RecordBuilder $builder
     * @return string
     */
    public static function insert(Connection $connection, RecordBuilder $builder)
    {
        $connection->insert(
            new InsertStatement($builder)
        );
    }

    /**
     * @param Connection $connection
     * @param RecordBuilder $builder
     */
    public static function update(Connection $connection, RecordBuilder $builder)
    {
        $connection->update(
            new UpdateStatement($builder)
        );
    }
}
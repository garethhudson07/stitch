<?php

namespace Stitch\DBAL;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Record as RecordBuilder;
use Stitch\DBAL\Statements\Persist\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persist\Update as UpdateStatement;
use Stitch\DBAL\Statements\Select\Query as QueryStatement;
use Stitch\DBAL\Statements\Select\Variables\Declaration as VariableDeclaration;
use Stitch\DBAL\Syntax\Select\Select as SelectSyntax;

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
    public static function select(Connection $connection, QueryBuilder $builder, SelectSyntax $syntax)
    {
        if (($builder->hasLimit() || $builder->hasOffset()) && $builder->crossTable()) {
            $connection->execute(
                new VariableDeclaration($syntax, $builder)
            );
        }

        return $connection->select(
            new QueryStatement($syntax, $builder)
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
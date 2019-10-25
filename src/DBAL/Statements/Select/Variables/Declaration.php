<?php

namespace Stitch\DBAL\Statements\Select\Variables;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;

/**
 * Class Declaration
 * @package Stitch\DBAL\Statements\Select\Variables
 */
class Declaration extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Declaration constructor.
     * @param Syntax $syntax
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
        $this->push(
            $this->syntax->setVariables(
                $this->variables($this->builder)
            )
        );
    }

    /**
     * @param Builder $builder
     * @return array
     */
    protected function variables(Builder $builder)
    {
        $scope = $this->syntax->scope($builder);
        $scope->table();
        $scope->column($schema);


        $table = $this->syntax->table($builder);
        $table->column($schema)->path();
        $table->column($schema)->alias();
        $table->primaryKey()->alias();



        $schema = $builder->getSchema();

        $variables[] = $this->syntax->assign(
            $this->syntax->variable(
                $this->syntax->primaryKeyAlias($schema)
            ),
            null
        );

        $variables[] = $this->syntax->assign(
            $this->syntax->variable(
                $this->syntax->rowNumberColumn($schema)
            ),
            0
        );

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join));
        }

        return $variables;
    }
}

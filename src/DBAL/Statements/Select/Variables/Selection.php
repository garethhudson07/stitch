<?php

namespace Stitch\DBAL\Statements\Select\Variables;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Selection
 * @package Stitch\DBAL\Statements\Select\Variables
 */
class Selection extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Selection constructor.
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
            $this->syntax->list(
                $this->variables($this->builder)
            )
        );
    }

    /**
     * @param $builder
     * @param null $parent
     * @return array
     */
    protected function variables(Builder $builder, Builder $parent = null)
    {
        $variables = [];
        $schema = $builder->getSchema();
        $countColumn = $this->syntax->rowNumberColumn($schema);
        $countVariable = $this->syntax->variable($countColumn);
        $pkColumn = $this->syntax->primaryKeyAlias($schema);

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join, $builder));
        }

        $value = $this->syntax->ternary(
            $this->syntax->equal($this->syntax->variable($pkColumn), $pkColumn),
            $countVariable,
            function() use ($parent, $countVariable)
            {
                if ($parent) {
                    $parentPkColumn = $this->syntax->primaryKeyAlias($parent->getSchema());

                    return $this->syntax->ternary(
                        $this->syntax->equal($parentPkColumn, $this->syntax->variable($parentPkColumn)),
                        $this->syntax->add($countVariable, 1),
                        '1'
                    );
                } else {
                    return $this->syntax->add($countVariable, 1);
                }
            },
            $countColumn
        );

        $variables[] = $this->syntax->assign($countVariable, $value) . ' ' . $this->syntax->alias($countColumn);

        return $variables;
    }
}

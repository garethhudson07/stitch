<?php

namespace Stitch\DBAL\Statements\Select\Variables;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;
use Stitch\DBAL\Paths\Resolver as PathResolver;

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

    protected $paths;

    /**
     * Selection constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder, PathResolver $paths)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->paths = $paths;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            Syntax::list(
                $this->variables($this->builder)
            )
        );
    }

    /**
     * @param Builder $builder
     * @param Builder|null $parent
     * @return array
     */
    protected function variables(Builder $builder, Builder $parent = null): array
    {
        $variables = [];

        $table = $this->paths->table($builder);
        $countColumn = Syntax::rowNumber($table);
        $countVariable = Syntax::variable($countColumn);
        $pkColumn = $table->primaryKey()->alias();
        $pkVariable = Syntax::variable($pkColumn);

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join, $builder));
        }

        $value = Syntax::ternary(
            Syntax::equal($pkVariable, $pkColumn),
            $countVariable,
            function() use ($parent, $countVariable)
            {
                if ($parent) {
                    $parentPkColumn = $this->paths->table($parent)->primaryKey()->alias();

                    return Syntax::ternary(
                        Syntax::equal($parentPkColumn, Syntax::variable($parentPkColumn)),
                        Syntax::add($countVariable, 1),
                        '1'
                    );
                } else {
                    return Syntax::add($countVariable, 1);
                }
            },
            $countColumn
        );

        $variables[] = Syntax::assign($countVariable, $value) . ' ' . Syntax::alias($countColumn);
        $variables[] = Syntax::assign($pkVariable, $pkColumn);

        return $variables;
    }
}

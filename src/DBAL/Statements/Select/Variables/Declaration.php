<?php

namespace Stitch\DBAL\Statements\Select\Variables;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;
use Stitch\DBAL\Paths\Resolver as PathResolver;

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

    protected $paths;

    /**
     * Declaration constructor.
     * @param Builder $builder
     * @param PathResolver $paths
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
            Syntax::setVariables(
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
        $table = $this->paths->table($builder);


        $variables[] = Syntax::assign(
            Syntax::variable(
                $table->primaryKey()->alias()
            ),
            null
        );

        $variables[] = Syntax::assign(
            Syntax::variable(
                Syntax::rowNumber($table)
            ),
            0
        );

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join));
        }

        return $variables;
    }
}

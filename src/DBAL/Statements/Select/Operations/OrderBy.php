<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Sorter as Builder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Grammar;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class OrderBy
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class OrderBy extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    protected $columns;

    /**
     * OrderBy constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder, PathResolver $paths)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->paths = $paths;
        $this->columns = (new Assembler())->glue(', ');
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        if ($this->builder->count()) {
            $this->push(
                Syntax::orderBy()
            )->push(
                $this->columns
            );

            foreach ($this->builder->getItems() as $item) {
                $this->columns->push(
                    Grammar::escape($this->paths->column($item['column'])->alias()) . " " . $item['direction']
                );
            }
        }
    }
}

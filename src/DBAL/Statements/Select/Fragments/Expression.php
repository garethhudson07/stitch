<?php

namespace Stitch\DBAL\Statements\Select\Fragments;

use Stitch\DBAL\Builders\Expression as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Raw;
use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;
/**
 * Class Expression
 * @package Stitch\DBAL\Statements\Select\Fragments
 */
class Expression extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    /**
     * Expression constructor.
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
        foreach ($this->builder->getItems() as $key => $item) {
            if ($key > 0) {
                $this->push($item['operator']);
            }

            if ($item['constraint'] instanceOf Builder) {
                $this->push(
                    (new static($item['constraint'], $this->paths))->isolate()
                );
            } elseif ($item['constraint'] instanceOf Raw) {
                $this->push(
                    (new Binder($item['constraint']->getSql()))
                        ->many($item['constraint']->getBindings())
                );
            } else {
                $this->push(
                    new Condition($item['constraint'], $this->paths)
                );
            }
        }
    }
}

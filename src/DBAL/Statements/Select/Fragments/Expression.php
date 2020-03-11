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
 * Class Where
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
     * Where constructor.
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
        $first = true;

        foreach ($this->builder->getItems() as $key => $item) {
            $constraint = null;

            if ($item['constraint'] instanceOf Builder) {
                if ($item['constraint']->count()) {
                    $constraint = (new static($item['constraint'], $this->paths));

                    if ($item['constraint']->count() > 1) {
                        $constraint->isolate();
                    }
                }
            } elseif ($item['constraint'] instanceOf Raw) {
                $constraint = (new Binder($item['constraint']->getSql()))
                        ->many($item['constraint']->getBindings());
            } else {
                $constraint = new Condition($item['constraint'], $this->paths);
            }
            
            if ($constraint) {
                if (!$first) {
                    $this->push($item['operator']);
                }
                
                $this->push($constraint);
                $first = false;
            }
        }
    }
}

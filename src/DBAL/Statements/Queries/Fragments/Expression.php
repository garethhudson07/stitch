<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Expression as Builder;
use Stitch\DBAL\Builders\Raw;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Expression
 * @package Stitch\DBAL\Statements\Queries\Fragments
 */
class Expression extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Expression constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
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
                    $this->component(new static($item['constraint']))->isolate()
                );
            } elseif ($item['constraint'] instanceOf Raw) {
                $this->push(
                    $this->component($item['constraint']->getSql())
                        ->bindMany($item['constraint']->getBindings())
                );
            } else {
                $this->push(new Condition($item['constraint']));
            }
        }
    }
}

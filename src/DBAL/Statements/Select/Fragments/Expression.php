<?php

namespace Stitch\DBAL\Statements\Select\Fragments;

use Stitch\DBAL\Builders\Expression as Builder;
use Stitch\DBAL\Builders\Raw;
use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
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

    /**
     * Expression constructor.
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
        foreach ($this->builder->getItems() as $key => $item) {
            if ($key > 0) {
                $this->push($item['operator']);
            }

            if ($item['constraint'] instanceOf Builder) {
                $this->push(
                    (new static($this->syntax, $item['constraint']))->isolate()
                );
            } elseif ($item['constraint'] instanceOf Raw) {
                $this->push(
                    (new Binder($item['constraint']->getSql()))
                        ->many($item['constraint']->getBindings())
                );
            } else {
                $value = $item['constraint']->getValue();

                $binder = new Binder(
                    $this->syntax->condition(
                        $item['constraint']->getColumn()->getSchema(),
                        $item['constraint']->getOperator(),
                        $value
                    )
                );

                if (!is_null($value)) {
                    $binder->add($value);
                }

                $this->push($binder);
            }
        }
    }
}

<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Expression as ExpressionBuilder;
use Stitch\DBAL\Builders\Raw;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Expression
 * @package Stitch\DBAL\Statements\Queries\Fragments
 */
class Expression extends Statement
{
    /**
     * @var ExpressionBuilder
     */
    protected $expressionBuilder;

    /**
     * Expression constructor.
     * @param ExpressionBuilder $expressionBuilder
     */
    public function __construct(ExpressionBuilder $expressionBuilder)
    {
        $this->expressionBuilder = $expressionBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        foreach ($this->expressionBuilder->getItems() as $key => $item) {
            if ($key > 0) {
                $this->assembler->push(
                    new Component($item['operator'])
                );
            }

            if ($item['constraint'] instanceOf ExpressionBuilder) {
                $component = (new Component(
                    new static($item['constraint'])
                ))->isolate();
            } else if ($item['constraint'] instanceOf Raw) {
                $component = (new Component(
                    $item['constraint']->getSql()
                ))->bindMany($item['constraint']->getBindings());
            } else {
                $component = new Component(
                    new Condition($item['constraint'])
                );
            }

            $this->assembler->push($component);
        }
    }
}
<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

/**
 * Class Statement
 * @package Stitch\DBAL\Statements
 */
abstract class Statement implements Assemblable
{
    /**
     * @var Assembler
     */
    protected $assembler;

    /**
     * Statement constructor.
     */
    public function __construct()
    {
        $this->assembler = new Assembler();
        $this->evaluate();
    }

    /**
     * @return mixed
     */
    protected abstract function evaluate();

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->assembler->getBindings();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->resolve();
    }

    /**
     * @return string
     */
    public function resolve()
    {
        return $this->assembler->resolve();
    }
}
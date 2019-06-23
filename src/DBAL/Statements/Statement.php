<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

abstract class Statement implements Assemblable
{
    protected $assembler;

    public function __construct()
    {
        $this->assembler = new Assembler();
        $this->evaluate();
    }

    protected abstract function evaluate();

    public function resolve()
    {
        return $this->assembler->resolve();
    }

    public function getBindings(): array
    {
        return $this->assembler->getBindings();
    }

    public function __toString(): string
    {
        return $this->resolve();
    }
}
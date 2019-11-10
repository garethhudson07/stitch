<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\HasBindings;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Statement
 * @package Stitch\DBAL\Statements
 */
abstract class Statement implements HasBindings
{
    /**
     * @var Assembler
     */
    protected $assembler;

    protected $isolate;

    protected $query;

    protected $bindings;

    public function __construct()
    {
        $this->assembler = new Assembler();
    }

    /**
     * @return mixed
     */
    abstract public function evaluate();

    /**
     * @return $this
     */
    protected function assemble()
    {
        $this->evaluate();

        $this->query = $this->isolate ?
            Syntax::parentheses($this->assembler->implode()) :
            $this->assembler->implode();


        $this->bindings = $this->assembler->bindings();

        return $this;
    }

    /**
     * @return array
     */
    public function bindings(): array
    {
        if (is_null($this->bindings)) {
            $this->assemble();
        }

        return $this->bindings;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->query();
    }

    /**
     * @return string
     */
    public function query()
    {
        if (is_null($this->query)) {
            $this->assemble();
        }

        return $this->query;
    }

    /**
     * @param $item
     * @return $this
     */
    public function push($item)
    {
        $this->assembler->push($item);

        return $this;
    }

    /**
     * @return $this
     */
    public function isolate()
    {
        $this->isolate = true;

        return $this;
    }
}

<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\HasBindings;
use Stitch\DBAL\Syntax\Select\Select as Syntax;

/**
 * Class Statement
 * @package Stitch\DBAL\Statements
 */
abstract class Statement implements HasBindings
{
    protected $syntax;

    /**
     * @var Assembler
     */
    protected $assembler;

    protected $isolate;

    protected $assembled = [
        'query' => null,
        'bindings' => null
    ];

    public function __construct(Syntax $syntax)
    {
        $this->syntax = $syntax;
        $this->assembler = new Assembler();
    }

    /**
     * @return mixed
     */
    abstract public function evaluate();

    /**
     * @return array
     */
    public function bindings(): array
    {
        return is_null($this->assembled['bindings']) ? $this->assemble()->bindings() : $this->assembled['bindings'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->query();
    }

    /**
     * @return $this
     */
    protected function assemble()
    {
        $this->evaluate();

        $this->assembled['query'] = $this->isolate ?
            $this->syntax->isolate($this->assembler->implode()) :
            $this->assembler->implode();


        $this->assembled['bindings'] = $this->assembler->bindings();

        return $this;
    }

    /**
     * @return string
     */
    public function query()
    {
        return is_null($this->assembled['query']) ? $this->assemble()->query() : $this->assembled['query'];
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

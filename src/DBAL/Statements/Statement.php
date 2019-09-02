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

    protected $assembled;

    protected $bindings;

    /**
     * @return Assembler
     */
    public function getAssembler()
    {
        if (!$this->assembler) {
            $this->assembler = new Assembler();
        }

        return $this->assembler;
    }

    /**
     * @return mixed
     */
    abstract public function evaluate();

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return is_null($this->bindings) ? $this->assemble()->getBindings() : $this->bindings;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->assembled();
    }

    /**
     * @return $this
     */
    protected function assemble()
    {
        $this->evaluate();

        $assembler = $this->getAssembler();

        $this->assembled = $assembler->assemble();
        $this->bindings = $assembler->getBindings();

        return $this;
    }

    /**
     * @return string
     */
    public function assembled()
    {
        return is_null($this->assembled) ? $this->assemble()->assembled() : $this->assembled;
    }

    /**
     * @param $item
     * @return $this
     */
    public function push($item)
    {
        $this->getAssembler()->push($item);

        return $this;
    }

    /**
     * @param $value
     * @return Component
     */
    public function component($value)
    {
        return new Component($value);
    }
}

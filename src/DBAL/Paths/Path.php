<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Syntax\Grammar;

abstract class Path
{
    protected $qualifiedName;

    protected $alias;

    public function __construct()
    {
        $this->qualifiedName = new Components(Grammar::qualifier());
        $this->alias = (new Components('_'))->inherit($this->qualifiedName);
    }

    /**
     * @return $this
     */
    public function assemble()
    {
        $this->evaluate();

        $this->qualifiedName->assemble();
        $this->alias->assemble();

        return $this;
    }

    /**
     * @return Components
     */
    public function qualifiedName(): Components
    {
        if (!$this->qualifiedName->assembled()) {
            $this->assemble();
        }

        return $this->qualifiedName;
    }

    /**
     * @return Components
     */
    public function alias(): Components
    {
        if (!$this->alias->assembled()) {
            $this->assemble();
        }

        return $this->alias;
    }

    /**
     *
     */
    abstract protected function evaluate(): void;
}

<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Syntax\Grammar;

abstract class Path
{
    protected $qualifiedName;

    public function __construct()
    {
        $this->qualifiedName = (new Components(Grammar::qualifier()))->escape();
    }

    /**
     * @return $this
     */
    public function assemble()
    {
        $this->evaluate();

        $this->qualifiedName->assemble();

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
     *
     */
    abstract protected function evaluate(): void;
}

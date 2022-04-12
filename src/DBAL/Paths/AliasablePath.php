<?php

namespace Stitch\DBAL\Paths;

abstract class AliasablePath extends Path
{
    protected $alias;

    public function __construct()
    {
        parent::__construct();

        $this->alias = (new Components('_'))->inherit($this->qualifiedName);
    }

    /**
     * @return $this
     */
    public function assemble()
    {
        parent::assemble();

        $this->alias->assemble();

        return $this;
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

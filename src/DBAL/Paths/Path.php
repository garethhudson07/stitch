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
     *
     */
    abstract protected function build(): void;

    /**
     * @return string
     */
    public function qualifiedName(): Components
    {
        if (!$this->qualifiedName->count()) {
            $this->build();
        }

        return $this->qualifiedName;
    }

    /**
     * @return string
     */
    public function alias(): Components
    {
        if (!$this->alias->count()) {
            $this->build();
        }

        return $this->alias;
    }
}

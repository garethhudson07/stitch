<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Column
 * @package Stitch\DBAL\Builders
 */
class Column
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var
     */
    protected $alias;

    /**
     * Column constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function alias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
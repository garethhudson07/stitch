<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Syntax\Grammar;

class Components
{
    protected $items = [];

    protected $glue;

    protected $parent;

    protected $assembled = '';

    protected $escape = false;

    /**
     * Components constructor.
     * @param string $glue
     */
    public function __construct(string $glue)
    {
        $this->glue($glue);
    }

    /**
     * @param string $glue
     * @return $this
     */
    public function glue(string $glue)
    {
        $this->glue = $glue;

        return $this;
    }

    /**
     * @return $this
     */
    public function escape()
    {
        $this->escape = true;

        return $this;
    }

    /**
     * @param Components $parent
     * @return $this
     */
    public function inherit(Components $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function implode()
    {
        return implode($this->glue, $this->escape ? array_map(fn($segment) => Grammar::escape($segment), $this->all()) : $this->all());
    }

    /**
     * @return string
     */
    public function assemble()
    {
        $this->assembled = $this->implode();

        return $this;
    }

    /**
     * @return mixed
     */
    public function assembled()
    {
        return $this->assembled;
    }

    /**
     * @param $item
     * @return $this
     */
    public function push($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function fill(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return array_merge(
            $this->parent ? $this->parent->all() : [],
            $this->items
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->assembled();
    }
}

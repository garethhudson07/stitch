<?php

namespace Stitch\DBAL\Paths;

class Components
{
    protected $items = [];

    protected $glue;

    protected $parent;

    protected $resolved;

    public function __construct(string $glue)
    {
        $this->glue = $glue;
    }

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
        return implode($this->glue, $this->all());
    }

    public function resolve()
    {
        if (!$this->resolved) {
            $this->resolved = $this->implode();
        }

        return $this->resolved;
    }

    public function push($item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function all(): array
    {
        return array_merge(
            $this->parent ? $this->parent->all() : [],
            $this->items
        );
    }

    public function count()
    {
        return count($this->items);
    }

    public function __toString()
    {
        return $this->resolve();
    }
}
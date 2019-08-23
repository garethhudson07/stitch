<?php

namespace Stitch\DBAL\Builders;

class JsonPath
{
    protected $items = [];

    /**
     * @param string $leg
     * @return $this
     */
    public function push(string $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param array $legs
     * @return $this
     */
    public function merge(array $items)
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }
}

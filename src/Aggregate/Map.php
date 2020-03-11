<?php

namespace Stitch\Aggregate;

class Map extends Aggregate
{
    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set(string $name, $value)
    {
        $this->offsetSet($name, $value);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }
}
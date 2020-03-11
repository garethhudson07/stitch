<?php

namespace Stitch\Aggregate;

class Set extends Aggregate
{
    /**
     * @param $value
     * @return bool
     */
    public function has($value): bool
    {
        return in_array($value, $this->items);
    }

    /**
     * @param $value
     * @return $this
     */
    public function include($value)
    {
        if (!$this->has($value)) {
            $this->items[] = $value;
        }

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        return $this->items[count($this->items) - 1] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function penultimate()
    {
        return $this->items[count($this->items) - 2] ?? null;
    }

    /**
     * @param int $index
     * @return static
     */
    public function before(int $index)
    {
        return (new static)->fill(
            array_slice($this->items, 0, $index)
        );
    }

    /**
     * @param int $index
     * @return static
     */
    public function after(int $index)
    {
        return (new static)->fill(
            array_slice($this->items, $index + 1)
        );
    }
}

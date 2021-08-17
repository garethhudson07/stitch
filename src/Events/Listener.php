<?php

namespace Stitch\Events;

class Listener
{
    /**
     * @param string $action
     * @return bool
     */
    public function handles(string $action): bool
    {
        return method_exists($this, $action);
    }

    /**
     * @param string $action
     * @param $payload
     * @return $this
     */
    public function handle(string $action, $payload)
    {
        if ($this->handles($action)) {
            if (!is_array($payload)) {
                $payload = [$payload];
            }

            $this->{$action}(...$payload);
        }

        return $this;
    }
}

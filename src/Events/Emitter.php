<?php

namespace Stitch\Events;

use Closure;

class Emitter
{
    protected $listeners = [];

    /**
     * @param string $event
     * @param $payload
     * @return $this
     */
    public function emit(string $event, $payload)
    {
        foreach($this->listeners as &$listener) {
            if ($listener instanceof Closure) {
                $listener = $listener();
            }

            $listener->handle($event, $payload);
        }

        return $this;
    }

    /**
     * @param Closure $listener
     * @return $this
     */
    public function listen(Closure $listener)
    {
        $this->listeners[] = $listener;

        return $this;
    }
}

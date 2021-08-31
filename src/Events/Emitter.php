<?php

namespace Stitch\Events;

use Closure;

class Emitter
{
    protected $listeners = [];

    /**
     * @param string $name
     * @return Event
     */
    public function makeEvent(string $name): Event
    {
        return Event::make($this, $name);
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function emit(Event $event): self
    {
        foreach($this->listeners as &$listener) {
            if ($listener instanceof Closure) {
                $listener = $listener();
            }

            $listener->handle($event);

            if (!$event->propagating()) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param Closure $listener
     * @return $this
     */
    public function listen(Closure $listener): self
    {
        $this->listeners[] = $listener;

        return $this;
    }
}

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
     * @param Event $event
     * @return $this
     */
    public function handle(Event $event): self
    {
        $action = $event->getName();

        if ($this->handles($action)) {
            $this->{$action}($event);
        }

        return $this;
    }
}

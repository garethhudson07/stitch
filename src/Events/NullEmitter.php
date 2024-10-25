<?php

namespace Stitch\Events;

class NullEmitter extends Emitter
{
    public function emit(Event $event): self
    {
        return $this;
    }
}

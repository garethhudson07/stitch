<?php

namespace Stitch\Events;

use Closure;
use Aggregate\Map;

class Event
{
    protected $emitter;

    protected $payload;

    protected $name;

    protected $propagating = true;

    protected $defaultPrevented = false;

    /**
     * Event constructor.
     * @param Emitter $emitter
     * @param Map $payload
     * @param string $name
     */
    public function __construct(Emitter $emitter, Map $payload, string $name)
    {
        $this->emitter = $emitter;
        $this->payload = $payload;
        $this->name = $name;
    }

    /**
     * @param Emitter $emitter
     * @param string $name
     * @return Event
     */
    public static function make(Emitter $emitter, string $name): Event
    {
        return new static($emitter, new Map(), $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fillPayload(array $data): self
    {
        $this->payload->fill($data);

        return $this;
    }

    /**
     * @return $this
     */
    public function stopPropagation(): self
    {
        $this->propagating = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function propagating(): bool
    {
        return $this->propagating;
    }

    /**
     * @return $this
     */
    public function preventDefault(): self
    {
        $this->defaultPrevented = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function defaultPrevented(): bool
    {
        return $this->defaultPrevented;
    }

    /**
     * @return $this
     */
    public function fire(): self
    {
        $this->emitter->emit($this);

        return $this;
    }
}

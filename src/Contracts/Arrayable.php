<?php

namespace Stitch\Contracts;

/**
 * Interface Arrayable
 * @package Stitch\Contracts
 */
interface Arrayable
{
    /**
     * @return array
     */
    public function toArray(): array;
}
<?php

namespace Stitch\DBAL\Statements\Contracts;

/**
 * Interface Assemblable
 * @package Stitch\DBAL\Statements\Contracts
 */
interface Assemblable
{
    /**
     * @return array
     */
    public function getBindings(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}
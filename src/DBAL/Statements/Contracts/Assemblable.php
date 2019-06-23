<?php

namespace Stitch\DBAL\Statements\Contracts;

interface Assemblable
{
    public function getBindings(): array;

    public function __toString(): string;
}
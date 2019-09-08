<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Builders\Column;

class Grammar
{
    /**
     * @return string
     */
    public static function wildcard()
    {
        return '*';
    }

    /**
     * @param array $pieces
     * @return string
     */
    public static function path(array $pieces)
    {
        return implode('.', $pieces);
    }

    /**
     * @param Column $builder
     * @return string
     */
    public static function alias(array $pieces)
    {
        return implode('_', $pieces);
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function columns(array $columns)
    {
        return implode(', ', $columns);
    }
}

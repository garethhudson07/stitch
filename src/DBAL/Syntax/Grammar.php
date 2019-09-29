<?php

namespace Stitch\DBAL\Syntax;

class Grammar
{
    /**
     * @return string
     */
    public static function terminator()
    {
        return ';';
    }

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
     * @param array $pieces
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
    public static function list(array $columns)
    {
        return implode(static::listDelimeter(), $columns);
    }

    /**
     * @return string
     */
    public static function listDelimeter()
    {
        return ', ';
    }

    /**
     * @return string
     */
    public static function variable()
    {
        return '@';
    }

    /**
     * @param $expression
     * @param $true
     * @param $false
     * @return string
     */
    public static function ternary($expression, $true, $false)
    {
        return "if($expression, $true, $false)";
    }

    /**
     * @return string
     */
    public static function assign()
    {
        return ":=";
    }

    /**
     * @return string
     */
    public static function equal()
    {
        return '=';
    }

    /**
     * @return string
     */
    public static function notEqual()
    {
        return '!'.static::equal();
    }

    /**
     * @return string
     */
    public static function greaterThan()
    {
        return '>';
    }

    /**
     * @return string
     */
    public static function lessThanOrEqual()
    {
        return '<=';
    }

    /**
     * @return string
     */
    public static function add()
    {
        return '+';
    }

    /**
     * @param string $value
     * @return string
     */
    public static function parentheses(string $value)
    {
        return "($value)";
    }

    /**
     * @param string $name
     * @param string $arguments
     * @return string
     */
    public static function method(string $name, string $arguments)
    {
        return "$name($arguments)";
    }

    /**
     * @return string
     */
    public static function placeholder()
    {
        return '?';
    }

    /**
     * @param array $values
     * @return string
     */
    public static function placeholders(array $values)
    {
        return implode(
            ', ',
            array_fill(0, count($values), static::placeholder())
        );
    }
}

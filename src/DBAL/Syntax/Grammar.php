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
     * @return string
     */
    public static function qualifier()
    {
        return '.';
    }

    /**
     * @return string
     */
    public static function listDelimiter()
    {
        return ', ';
    }

    /**
     * @param string $name
     * @return string
     */
    public static function variable(string $name): string
    {
        return "@$name";
    }

    /**
     * @param $value
     * @return string
     */
    public static function escape($value)
    {
        return "`$value`";
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
    public static function assignment()
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
}

<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Schema\Column as Column;
use Closure;

class Syntax
{
    /**
     * @param array $items
     * @return string
     */
    public static function list(array $items): string
    {
        return implode(Grammar::listDelimiter(), $items);
    }

    /**
     * @param array $values
     * @return string
     */
    public static function placeholders(array $values): string
    {
        return static::list(
            array_fill(0, count($values), Grammar::placeholder())
        );
    }

    /**
     * @return string
     */
    public static function from()
    {
        return Lexicon::from();
    }

    /**
     * @param string $name
     * @return string
     */
    public static function alias(string $name): string
    {
        return static::implode(
            Lexicon::alias(),
            Grammar::escape($name),
        );
    }

    /**
     * @param array $variables
     * @return string
     */
    public static function setVariables(array $variables)
    {
        return static::implode(
            Lexicon::set(),
            static::list($variables) . Grammar::terminator()
        );
    }

    /**
     * @param mixed ...$arguments
     * @return string
     */
    public static function ternary(...$arguments)
    {
        $arguments = array_map(function ($argument)
        {
            return $argument instanceof Closure ? $argument() : $argument;
        }, $arguments);

        return Grammar::ternary(...$arguments);
    }

    /**
     * @param $variable
     * @param $value
     * @return string
     */
    public static function assign($variable, $value)
    {
        switch (gettype($value)) {
            case 'NULL':
                $value = 'NULL';
        }

        return static::implode(
            $variable,
            Grammar::assignment(),
            $value
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public static function equal($left, $right)
    {
        return static::implode(
            $left,
            Grammar::equal(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public static function lessThanOrEqual($left, $right)
    {
        return static::implode(
            $left,
            Grammar::lessThanOrEqual(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public static function greaterThan($left, $right)
    {
        return static::implode(
            $left,
            Grammar::greaterThan(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public static function add($left, $right)
    {
        return static::implode(
            $left,
            Grammar::add(),
            $right
        );
    }

    /**
     * @return string
     */
    public static function where(): string
    {
        return Lexicon::where();
    }


    /**
     * @param string $value
     * @return string
     */
    public static function parentheses(string $value)
    {
        return Grammar::parentheses($value);
    }

    /**
     * @return string
     */
    public static function variable(string $name): string
    {
        return Grammar::variable($name);
    }

    /**
     * @return string
     */
    public static function orderBy(): string
    {
        return Lexicon::orderBy();
    }

    /**
     * @param int $quantity
     * @return string
     */
    public static function limit(int $quantity): string
    {
        return static::implode(
            Lexicon::limit(),
            $quantity
        );
    }

    /**
     * @param int $quantity
     * @return string
     */
    public static function offset(int $quantity): string
    {
        return static::implode(
            Lexicon::offset(),
            $quantity
        );
    }

    /**
     * @param Column $primaryKey
     * @return string
     */
    public static function scope(Column $primaryKey): string
    {
        return static::implode(
            $primaryKey->getName(),
            Grammar::equal(),
            Grammar::placeholder()
        );
    }

    /**
     * @param array ...$arguments
     * @return string
     */
    protected static function implode(...$arguments)
    {
        return implode(' ', $arguments);
    }
}

<?php

namespace Stitch\DBAL\Syntax;

use Closure;
use Stitch\DBAL\Paths\Table as TablePath;
use Stitch\DBAL\Paths\Column as ColumnPath;

class Select
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
    public static function selectAll(): string
    {
        return static::implode(
            Lexicon::select(),
            Grammar::wildcard()
        );
    }

    /**
     * @return string
     */
    public static function selectColumns(array $columns): string
    {
        return static::implode(
            Lexicon::select(),
            static::list($columns)
        );
    }

    /**
     * @return string
     */
    public static function selectAllAnd(): string
    {
        return static::selectAll() . Grammar::listDelimiter();
    }

    /**
     * @return string
     */
    public static function selectSubquery(): string
    {
        return static::implode(
            Lexicon::select(),
            Grammar::wildcard(),
            Lexicon::from()
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
     * @param string $type
     * @param TablePath $table
     * @return string
     */
    public static function join(string $type, TablePath $table): string
    {
        $components = [
            $type,
            Lexicon::join(),
            $table->qualifiedname()
        ];

        if ($table->conflict()) {
            $components[] = static::alias($table->alias());
        }

        $components[] = Lexicon::on();

        return static::implode(...$components);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function alias(string $name): string
    {
        return static::implode(
            Lexicon::alias(),
            $name
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
     * @param TablePath $table
     * @return string
     */
    public static function rowNumber(TablePath $table): string
    {
        return "{$table->alias()}_row_num";
    }

    /**
     * @param ColumnPath $column
     * @param $operator
     * @param $value
     * @return string
     */
    public static function condition(ColumnPath $column, $operator, $value)
    {
        $pieces = [$column->qualifiedName()];

        switch (gettype($value)) {
            case 'array':
                if (in_array($operator, Lexicon::methods())) {
                    $pieces[] = Grammar::method($operator, static::placeholders($value));
                } else {
                    $pieces[] = $operator;
                    $pieces[] = implode(' ' . Lexicon::and() . ' ', $value);
                }
                break;

            case null:
                $pieces[] = $operator === Grammar::notEqual() ? Lexicon::notNull() : Lexicon::null();
                break;

            default:
                $pieces[] = $operator;
                $pieces[] = $value instanceOf ColumnPath ? $value->qualifiedName() : Grammar::placeholder();
        }

        return static::implode(...$pieces);
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
     * @param array ...$arguments
     * @return string
     */
    protected static function implode(...$arguments)
    {
        return implode(' ', $arguments);
    }
}

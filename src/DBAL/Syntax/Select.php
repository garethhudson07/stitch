<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Paths\Table as TablePath;
use Stitch\DBAL\Paths\Column as ColumnPath;

class Select extends Syntax
{
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
     * @param array $columns
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

            case 'NULL':
                $pieces[] = $operator === Grammar::notEqual() ? Lexicon::notNull() : Lexicon::null();
                break;

            default:
                $pieces[] = $operator;
                $pieces[] = $value instanceOf ColumnPath ? $value->qualifiedName() : Grammar::placeholder();
        }

        return static::implode(...$pieces);
    }

    /**
     * @param TablePath $table
     * @return string
     */
    public static function rowNumber(TablePath $table): string
    {
        return "{$table->alias()}_row_num";
    }
}

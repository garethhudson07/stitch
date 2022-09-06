<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Paths\Path;
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
        return static::implode(
            $type,
            Lexicon::join(),
            $table->qualifiedName(),
            Lexicon::alias(),
            Grammar::escape($table->alias()),
            Lexicon::on()
        );
    }

    /**
     * @param Path $path
     * @param $operator
     * @param $value
     * @return string
     */
    public static function condition(Path $path, $operator, $value)
    {
        $pieces = [$path->qualifiedName()];

        switch (gettype($value)) {
            case 'array':
                if (in_array($operator, Lexicon::methods())) {
                    $pieces[] = Grammar::method($operator, static::placeholders($value));
                } else {
                    $pieces[] = $operator;
                    $pieces[] = implode(
                        ' ' . Lexicon::and() . ' ',
                        array_fill(0, count($value), Grammar::placeholder())
                    );
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

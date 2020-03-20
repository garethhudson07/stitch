<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Schema\Table;

class Insert extends Syntax
{
    /**
     * @param Table $table
     * @return string
     */
    public static function into(Table $table): string
    {
        return static::implode(
            Lexicon::insert(),
            Lexicon::into(),
            $table->getName()
        );
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function columns(array $columns): string
    {
        return static::parentheses(
            static::list($columns)
        );
    }

    /**
     * @param array $values
     * @return string
     */
    public static function values(array $values): string
    {
        return static::implode(
            Lexicon::values(),
            static::parentheses(
                static::placeholders($values)
            )
        );
    }
}

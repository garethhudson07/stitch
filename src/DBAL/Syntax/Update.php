<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Schema\Table;

class Update extends Syntax
{
    /**
     * @param Table $table
     * @return string
     */
    public static function table(Table $table): string
    {
        return static::implode(
            Lexicon::update(),
            $table->getName()
        );
    }

    /**
     * @param array $columns
     * @return string
     */
    public static function set(array $columns): string
    {
        return static::implode(
            Lexicon::set(),
            static::list(
                array_map(function ($column)
                {
                    return static::implode(
                        Grammar::escape($column),
                        Grammar::equal(),
                        Grammar::placeholder()
                    );
                }, $columns)
            )
        );
    }
}

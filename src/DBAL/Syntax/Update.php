<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Schema\Table;
use Stitch\DBAL\Schema\Column as Column;

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
                        $column,
                        Grammar::equal(),
                        Grammar::placeholder()
                    );
                }, $columns)
            )
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
}

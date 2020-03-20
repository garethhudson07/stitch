<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Schema\Table;

class Delete extends Syntax
{
    /**
     * @param Table $table
     * @return string
     */
    public static function table(Table $table): string
    {
        return static::implode(
            Lexicon::delete(),
            Lexicon::from(),
            $table->getName()
        );
    }
}

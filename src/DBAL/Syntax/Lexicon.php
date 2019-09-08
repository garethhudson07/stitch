<?php

namespace Stitch\DBAL\Syntax;

class Lexicon
{
    /**
     * @return string
     */
    public static function select()
    {
        return 'SELECT';
    }

    /**
     * @return string
     */
    public static function from()
    {
        return 'FROM';
    }

    /**
     * @return string
     */
    public static function alias()
    {
        return "AS";
    }
}
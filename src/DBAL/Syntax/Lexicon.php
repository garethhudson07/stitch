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
    public static function join()
    {
        return 'JOIN';
    }

    /**
     * @return string
     */
    public static function on()
    {
        return 'ON';
    }

    /**
     * @return string
     */
    public static function where()
    {
        return 'WHERE';
    }

    /**
     * @return string
     */
    public static function alias()
    {
        return "AS";
    }

    /**
     * @return string
     */
    public static function set()
    {
        return 'SET';
    }

    /**
     * @return string
     */
    public static function and()
    {
        return 'AND';
    }

    /**
     * @return string
     */
    public static function null()
    {
        return 'IS NULL';
    }

    /**
     * @return string
     */
    public static function notNull()
    {
        return 'IS NOT NULL';
    }

    /**
     * @return string
     */
    public static function in()
    {
        return 'IN';
    }

    /**
     * @return string
     */
    public static function notIn()
    {
        return 'NOT IN';
    }

    /**
     * @return string
     */
    public static function limit()
    {
        return 'LIMIT';
    }

    /**
     * @return string
     */
    public static function orderBy()
    {
        return 'ORDER BY';
    }
}

<?php

namespace Stitch\DBAL\Syntax;

class Lexicon
{
    /**
     * @return string
     */
    public static function insert(): string
    {
        return 'INSERT';
    }

    /**
     * @return string
     */
    public static function into(): string
    {
        return 'INTO';
    }

    /**
     * @return string
     */
    public static function values(): string
    {
        return 'VALUES';
    }

    /**
     * @return string
     */
    public static function update(): string
    {
        return 'UPDATE';
    }

    /**
     * @return string
     */
    public static function delete(): string
    {
        return 'DELETE';
    }

    /**
     * @return string
     */
    public static function select(): string
    {
        return 'SELECT';
    }

    /**
     * @return string
     */
    public static function from(): string
    {
        return 'FROM';
    }

    /**
     * @return string
     */
    public static function join(): string
    {
        return 'JOIN';
    }

    /**
     * @return string
     */
    public static function on(): string
    {
        return 'ON';
    }

    /**
     * @return string
     */
    public static function where(): string
    {
        return 'WHERE';
    }

    /**
     * @return string
     */
    public static function alias(): string
    {
        return "AS";
    }

    /**
     * @return string
     */
    public static function set(): string
    {
        return 'SET';
    }

    /**
     * @return string
     */
    public static function and(): string
    {
        return 'AND';
    }

    /**
     * @return string
     */
    public static function null(): string
    {
        return 'IS NULL';
    }

    /**
     * @return string
     */
    public static function notNull(): string
    {
        return 'IS NOT NULL';
    }

    /**
     * @return array
     */
    public static function methods(): array
    {
        return [
            'IN',
            'NOT IN'
        ];
    }

    /**
     * @return string
     */
    public static function limit(): string
    {
        return 'LIMIT';
    }

    /**
     * @return string
     */
    public static function orderBy(): string
    {
        return 'ORDER BY';
    }
}

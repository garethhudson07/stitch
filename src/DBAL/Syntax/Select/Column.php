<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Syntax\Grammar;
use Stitch\DBAL\Builders\Column as Builder;
use Stitch\DBAL\Schema\Column as Schema;
use Stitch\DBAL\Syntax\Lexicon;


class Column
{
    protected $table;

    protected $builder;

    protected $pieces = [];

    protected $path;

    protected $alias;

    /**
     * Column constructor.
     * @param Builder $builder
     * @param Table $table
     */
    public function __construct(Builder $builder, Table $table)
    {
        $this->builder = $builder;
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function pieces(): array
    {
        if (!$this->pieces) {
            $this->pieces = [
                $this->table->pieces(),
                $this->builder->getSchema()->getName()
            ];
        }

        return $this->pieces;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return Grammar::path(
            $this->pieces()
        );
    }

    /**
     * @return string
     */
    public function alias(): string
    {
        return Grammar::alias(
            $this->pieces()
        );
    }

    /**
     * @param $operator
     * @param $value
     * @return string
     */
    public function condition($operator, $value): string
    {
        $pieces = [$this->pieces()];

        switch (gettype($value)) {
            case 'array':
                if (in_array($operator, Lexicon::methods())) {
                    $pieces[] = Grammar::method($operator, Grammar::placeholders($value));
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
                $pieces[] = $value instanceOf Schema ? $this->columnPath($value) : Grammar::placeholder();
        }

        return implode(' ', $pieces);
    }

}
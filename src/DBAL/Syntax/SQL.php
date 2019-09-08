<?php

namespace Stitch\DBAL\Syntax;

use Stitch\DBAL\Builders\Column;
use Stitch\DBAL\Builders\Selection;

class SQL
{
    protected $context;

    /**
     * Grammer constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param Column $builder
     * @return string
     */
    public function columnPath(Column $builder)
    {
        return Grammar::path(
            $this->context->columnPath($builder)
        );
    }

    /**
     * @param Column $builder
     * @return string
     */
    public function columnAlias(Column $builder)
    {
        return Grammar::alias(
            $this->context->columnPath($builder)
        );
    }

    /**
     * @param Selection $selection
     * @return string
     */
    public function columns(Selection $selection)
    {
        $columns = array_map(function ($column)
        {
            $path = $this->context->columnPath($column);

            $str = Grammar::path($path);

            if ($this->context->crossTable()) {
                $str .= ' ' . Lexicon::alias() . ' ' . Grammar::alias($path);
            }

            return $str;
        }, $selection->getColumns());

        return Grammar::columns($columns);
    }

    /**
     * @return string
     */
    public function selectAll()
    {
        return Lexicon::select() . ' ' . Grammar::wildcard();
    }

    /**
     * @param array $columns
     * @return string
     */
    public function selectColumns(Selection $columns)
    {
        return Lexicon::select() . ' ' . $this->columns($columns);
    }

    /**
     * @return string
     */
    public function selectSubquery()
    {
        return Lexicon::select() . ' ' . Grammar::wildcard() . ' ' . Lexicon::from();
    }

    /**
     * @param string $name
     * @param string $alias
     * @return string
     */
    public function alias(string $name)
    {
        return Lexicon::alias() . " $name";
    }
}

<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Syntax\Grammar;

class Table
{
    protected $builder;

    protected $suffix = '';

    protected $pieces = [];

    protected $path;

    protected $alias;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function suffix(string $suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return array
     */
    public function pieces(): array
    {
        if (!$this->pieces) {
            $schema = $this->builder->getSchema();

            $this->pieces = [
                $schema->getConnection()->getDatabase(),
                "{$schema->getName()}$this->suffix"
            ];
        }

        return $this->pieces;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        if (!$this->path) {
            $this->path = Grammar::path(
                $this->pieces()
            );
        }

        return $this->path;
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
}

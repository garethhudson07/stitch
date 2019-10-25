<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Schema\Table as Schema;
use Stitch\DBAL\Syntax\Grammar;

class Table
{
    protected $context;

    protected $schema;

    protected $suffix = '';

    protected $columns = [];

    protected $pieces = [];

    protected $path;

    protected $alias;

    public function __construct(Context $context, Schema $schema)
    {
        $this->context = $context;
        $this->schema = $schema;
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
            if ($this->context->crossDatabase()) {
               $this->pieces[] = $this->schema->getConnection()->getDatabase();
            }

            $this->pieces[] = "{$this->schema->getName()}$this->suffix";
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

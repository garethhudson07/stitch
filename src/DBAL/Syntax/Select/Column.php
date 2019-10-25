<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Syntax\Grammar;
use Stitch\DBAL\Schema\Column as Schema;
use Stitch\DBAL\Syntax\Lexicon;

class Column
{
    protected $context;

    protected $schema;

    protected $table;

    protected $pieces = [];

    protected $path;

    protected $alias;

    /**
     * Column constructor.
     * @param Context $context
     * @param Schema $schema
     * @param Table $table
     */
    public function __construct(Context $context, Schema $schema, Table $table)
    {
        $this->context = $context;
        $this->schema = $schema;
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function pieces(): array
    {
        if (!$this->pieces) {
            if ($this->context->crossTable()) {
                $this->pieces = $this->table->pieces();
            }

            $this->pieces[] = $this->schema->getName();
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
}

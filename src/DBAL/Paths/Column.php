<?php

namespace Stitch\DBAL\Paths;

use Stitch\DBAL\Schema\Column as Schema;

class Column extends AliasablePath
{
    protected $schema;

    protected $table;

    protected $resolver;

    /**
     * Column constructor.
     * @param Schema $schema
     * @param Table $table
     * @param Resolver $resolver
     */
    public function __construct(Schema $schema, Table $table, Resolver $resolver)
    {
        parent::__construct();

        $this->schema = $schema;
        $this->table = $table;
        $this->resolver = $resolver;
    }

    /**
     *
     */
    public function evaluate(): void
    {
        if (count($this->resolver->rootTable()->getBuilder()->getJoins())) {
            $this->qualifiedName->push(
                $this->table->alias()
            );
        }

        $this->qualifiedName->push(
            $this->schema->getName()
        );
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->schema->getName();
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }
}

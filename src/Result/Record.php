<?php

namespace Stitch\Result;

use Stitch\Queries\Query;
use Stitch\Queries\Relations\HasOne;

class Record
{
    protected $query;

    protected $table;

    protected $columns;

    protected $relations = [];

    protected $data = [];

    /**
     * Record constructor.
     * @param Query $query
     * @param array $raw
     */
    public function __construct(Query $query, array $raw)
    {
        $this->query = $query;
        $this->table = $query->getModel()->getTable();
        $this->columns = $query->getBuilder()->getSelection()->getColumns();

        $this->assemble($raw);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->data[$key];
    }

    /**
     * @param array $raw
     */
    protected function assemble(array $raw)
    {
        $this->extract($raw)->extractRelations($raw);
    }

    /**
     * @param array $raw
     * @return $this
     */
    protected function extract(array $raw)
    {
        foreach ($this->columns as $column) {
            $name = $column->getName();

            $this->data[$name] = $this->table->getColumn($name)->cast($raw[$column->getAlias()]);
        }

        return $this;
    }

    /**
     * @param $raw
     * @return $this
     */
    public function extractRelations($raw)
    {
        foreach ($this->query->getRelations() as $key => $relation) {
            if ( ! array_key_exists($key, $this->relations)) {
                $instance = ($relation instanceof HasOne) ? new static($relation, $raw) : new Set($relation);

                $this->relations[$key] = $instance->extract($raw);
            } else {
                $this->relations[$key]->extract($raw);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
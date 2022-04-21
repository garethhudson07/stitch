<?php

namespace Stitch\Result;

use Aggregate\Set as Aggregate;

/**
 * Class Set
 * @package Stitch\Result
 */
class Set extends Aggregate
{
    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * Set constructor.
     * @param Blueprint $blueprint
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
    }

    /**
     * @param array $raw
     * @return $this
     */
    public function assemble(array $raw)
    {
        foreach ($raw as $item) {
            $this->extract($item);
        }

        return $this;
    }

    /**
     * @param array $raw
     * @return $this
     */
    public function extract(array $raw)
    {
        $primaryKey = $this->blueprint->table()->primaryKey();

        if ($raw[$primaryKey->alias()->assembled()] !== null) {
            if ($item = $this->find($raw[$primaryKey->alias()->assembled()])) {
                $item->extractRelations($raw);
            } else {
                $item = $this->blueprint->resultRecord()->extract($raw);
                $this->items[] = $item;
                $this->map[$item->{$primaryKey->name()}] = count($this->items) - 1;
            }
        }

        return $this;
    }

    /**
     * @param $primaryKey
     * @return bool|mixed
     */
    public function find($primaryKey)
    {
        if (array_key_exists($primaryKey, $this->map)) {
            return $this->items[$this->map[$primaryKey]];
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function hydrate()
    {
        $collection = $this->blueprint->activeRecordCollection();

        foreach ($this->items as $item) {
            $collection->push(
                $item->hydrate()
            );
        }

        return $collection;
    }
}

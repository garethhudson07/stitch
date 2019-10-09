<?php

namespace Stitch\Result;

use stitch\Collection;
use Stitch\Result\Blueprints\Blueprint;

/**
 * Class Set
 * @package Stitch\Result
 */
class Set extends Collection
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
        $primaryKeyMap = $this->blueprint->columnMap()->primaryKey();

        if ($raw[$primaryKeyMap['alias']] !== null) {
            if ($item = $this->find($raw[$primaryKeyMap['alias']])) {
                $item->extractRelations($raw);
            } else {
                $item = $this->blueprint->factory()->resultRecord()->extract($raw);
                $this->items[] = $item;
                $this->map[$item->{$primaryKeyMap['schema']->getName()}] = count($this->items) - 1;
            }
        }

        return $this;
    }

    /**
     * @param $primaryKey
     * @return bool|mixed
     */
    public function find(int $primaryKey)
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
        $collection = $this->blueprint->factory()->activeRecordCollection();

        foreach ($this->items as $item) {
            $collection->push(
                $item->hydrate()
            );
        }

        return $collection;
    }
}
<?php

namespace Stitch\Records\Relations;

use Stitch\Records\Collection as BaseCollection;
use Stitch\Records\Record;

class Collection extends BaseCollection
{
    protected $associated;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function make(array $attributes = [])
    {
        $record = $this->factory->record($attributes);

        if ($this->associated) {
            $record->associate($this->associated);
        }

        return $record;
    }

    /**
     * @param Record $associated
     * @return $this
     */
    public function associate(Record $associated)
    {
        $this->associated = $associated;

        foreach ($this->items as $item) {
            $item->associate($associated);
        }

        return $this;
    }
}

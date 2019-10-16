<?php

namespace Stitch\Records\Relations;

use Stitch\Records\Collection as BaseCollection;
use Stitch\Records\Record;

class Collection extends BaseCollection
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function record(array $attributes = [])
    {
        $record = parent::make($attributes);

        if ($this->associated) {
            $record->associate($this->associated);
        }

        return $record;
    }

    /**
     * @param Record $record
     * @return $this
     */
    public function associate(Record $record)
    {
        foreach ($this->items as $item) {
            $item->associate($record);
        }

        return $this;
    }
}

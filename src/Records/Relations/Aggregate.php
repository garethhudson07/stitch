<?php

namespace Stitch\Records\Relations;

use Stitch\Aggregate\Set;
use Stitch\Records\Record;

class Aggregate extends Set
{
    protected $relation;

    protected $associated;

    public function __construct($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function record(array $attributes = [])
    {
        $record = $this->relation->record($attributes);

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
        $this->associated = $record;

        return $this->applyAssociation($record);
    }

    /**
     * @return $this
     */
    public function applyAssociation()
    {
        if ($this->associated) {
            foreach ($this->items as $item) {
                $item->associate($this->associated);
            }
        }

        return $this;
    }

    /**
     * @return Aggregate
     */
    public function save()
    {
        $this->applyAssociation();

        foreach ($this->items as $item) {
            $item->save();
        }

        return $this;
    }
}

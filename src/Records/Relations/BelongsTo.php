<?php

namespace Stitch\Records\Relations;

use Stitch\Model;
use Stitch\Records\Record;
use Stitch\Relations\Has as Blueprint;

class BelongsTo extends Record
{
    protected $associatedRecord;

    protected $blueprint;

    public function __construct(Model $model, Record $associatedRecord, Blueprint $blueprint)
    {
        parent::__construct($model);

        $this->associatedRecord = $associatedRecord;
        $this->blueprint = $blueprint;
    }

    /**
     * @return $this
     */
    public function associate()
    {
        $foreignKey = $this->blueprint->getForeignKey();

        $this->attributes[$foreignKey->getLocalColumn()->getName()] = $this->associatedRecord->getAttribute($foreignKey->getReferenceColumnName());

        return $this;
    }
}
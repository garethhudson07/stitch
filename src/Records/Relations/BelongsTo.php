<?php

namespace Stitch\Records\Relations;

use Stitch\Model;
use Stitch\Records\Record;
use Stitch\Relations\Has as Blueprint;

class BelongsTo extends Record
{
    protected $blueprint;

    protected $associated;

    /**
     * BelongsTo constructor.
     * @param Model $model
     * @param Blueprint $blueprint
     */
    public function __construct(Model $model, Blueprint $blueprint)
    {
        parent::__construct($model);


        $this->blueprint = $blueprint;
    }

    /**
     * @param Record $record
     */
    public function associate(Record $record)
    {
        $this->associated = $record;
    }

    /**
     * @return $this
     */
    public function applyAssociation()
    {
        $foreignKey = $this->blueprint->getForeignKey();

        $this->attributes[$foreignKey->getLocalColumn()->getName()] = $this->associated->getAttribute($foreignKey->getReferenceColumnName());

        return $this;
    }

    /**
     * @return Record|void
     */
    public function save()
    {
        $this->applyAssociation();

        parent::save();
    }
}
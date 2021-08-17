<?php

namespace Stitch\Relations;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\Model;
use Stitch\Queries\Joins\Join;
use Stitch\Queries\Path;
use Stitch\Registry;
use Stitch\Records\Relations\Aggregate as RecordAggregate;

/**
 * Class Relation
 * @package Stitch\Relations
 */
abstract class Relation
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Model
     */
    protected $localModel;

    protected $localKey;

    /**
     * @var Model
     */
    protected $foreignModel;

    protected $foreignKey;

    /**
     * @var string
     */
    protected $binding;

    /**
     * @var string
     */
    protected $associate = 'many';

    /**
     * Relation constructor.
     * @param string $name
     * @param Model $localModel
     */
    public function __construct(string $name, Model $localModel)
    {
        $this->name = $name;
        $this->localModel = $localModel;
    }

    /**
     * @return Model
     */
    public function getLocalModel()
    {
        return $this->localModel;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function localKey(string $name)
    {
        $this->localKey = $this->localModel->getTable()->getColumn($name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function foreignModel(Model $model)
    {
        $this->foreignModel = $model;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getForeignModel()
    {
        if ($this->foreignModel) {
            return $this->foreignModel;
        }

        if ($this->binding) {
            $this->foreignModel = Registry::get($this->binding);

            return $this->foreignModel;
        }

        return null;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function foreignKey(string $column)
    {
        $this->foreignKey = $this->getForeignModel()->getTable()->getColumn($column);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function bind(string $name)
    {
        $this->binding = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @return $this
     */
    public function boot()
    {
        if (!$this->hasKeys()) {
            $this->pullKeys();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasKeys()
    {
        return ($this->localKey && $this->foreignKey);
    }

    /**
     * @param Path $path
     * @return Join
     */
    public function join(Path $path)
    {
        return Join::make(
            $this->getForeignModel(),
            $this->joinBuilder(),
            $this,
            $path
        );
    }

    /**
     * @return JoinBuilder
     */
    public function joinBuilder()
    {
        return new JoinBuilder($this->foreignModel->getTable());
    }

    /**
     * @return RecordAggregate
     */
    public function collection()
    {
        return new RecordAggregate($this->getForeignModel());
    }

    /**
     * @return bool
     */
    public function associatesMany()
    {
        return $this->associate === 'many';
    }

    /**
     * @return bool
     */
    public function associatesOne()
    {
        return $this->associate === 'one';
    }

    /**
     * @return mixed
     */
    abstract public function pullKeys();
}

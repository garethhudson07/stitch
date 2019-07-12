<?php

namespace Stitch\Relations;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\Model;
use Stitch\Registry;

/**
 * Class Relation
 * @package Stitch\Relations
 */
abstract class Relation
{
    /**
     * @var Model
     */
    protected $localModel;

    /**
     * @var Model
     */
    protected $foreignModel;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $binding;

    /**
     * Relation constructor.
     * @param Model $localModel
     */
    public function __construct(Model $localModel)
    {
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
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
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
     * @return JoinBuilder
     */
    protected function joinBuilder()
    {
        $table = $this->foreignModel->getTable();

        return new JoinBuilder(
            $table->getName(),
            $table->getPrimaryKey()->getName()
        );
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
     * @return mixed
     */
    abstract public function query();

    /**
     * @return mixed
     */
    abstract public function make();

    /**
     * @return mixed
     */
    abstract public function hasKeys();

    /**
     * @return mixed
     */
    abstract public function pullKeys();
}
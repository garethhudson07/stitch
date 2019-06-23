<?php

namespace Stitch\Relations;

use Stitch\Model;
use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\Registry;

abstract class Relation
{
    protected $localModel;

    protected $foreignModel;

    protected $name;

    protected $binding;

    public function __construct(Model $localModel)
    {
        $this->localModel = $localModel;
    }

    public function getLocalModel()
    {
        return $this->localModel;
    }

    public function foreignModel(Model $model)
    {
        $this->foreignModel = $model;

        return $this;
    }

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

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function bind(string $name)
    {
        $this->binding = $name;

        return $this;
    }

    public function getBinding()
    {
        return $this->binding;
    }

    public function query()
    {
        $queryClass = $this->queryClass();
        $table = $this->foreignModel->getTable();

        return (new $queryClass(
            $this->foreignModel,
            new JoinBuilder(
                $table->getName(),
                $table->getPrimaryKey()->getName()
            ),
            $this
        ));
    }

    public function boot()
    {
        if (!$this->hasKeys()) {
            $this->pullKeys();
        }

        return $this;
    }

    abstract public function hasKeys();

    abstract public function pullKeys();

    abstract protected function queryClass();
}
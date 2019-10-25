<?php

namespace Stitch\DBAL\Syntax\Select;

use Stitch\DBAL\Builders\Table as Builder;

class Context
{
    protected $tables = [];

    protected $map = [];

    protected $crossDatabase;

    protected $crossTable;

    protected $offset;

    protected $limited;

    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->map = new Map();
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function evaluate(Builder $builder)
    {
        $this->crossDatabase = $this->joinsDatabases($builder);
        $this->crossTable = $this->joinsTables($builder);
        $this->offset = $this->hasOffset($builder);
        $this->limited = $this->hasLimit($builder);

        $this->add($builder);

        return $this;
    }
    
    /**
     * @param Builder $builder
     * @return bool
     */
    public function joinsDatabases(Builder $builder)
    {
        $database = $builder->getSchema()->getConnection()->getDatabase();

        foreach ($builder->getJoins() as $join) {
            if ($join->getSchema()->getConnection()->getDatabase() !== $database) {
                return true;
            }

            if ($this->joinsDatabases($join)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function crossDatabase()
    {
        return $this->crossDatabase;
    }

    /**
     * @param Builder $builder
     * @return bool
     */
    public function joinsTables(Builder $builder)
    {
        return (count($builder->getJoins()) > 0);
    }

    /**
     * @return mixed
     */
    public function crossTable()
    {
        return $this->crossTable;
    }

    /**
     * @param Builder $builder
     * @return bool
     */
    public function hasOffset(Builder $builder)
    {
        if ($builder->getOffset() !== null) {
            return true;
        }

        foreach ($builder->getJoins() as $join) {
            if ($this->hasOffset($join)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function offset()
    {
        return $this->offset;
    }

    /**
     * @param Builder $builder
     * @return bool
     */
    public function hasLimit(Builder $builder)
    {
        if ($builder->getLimit() !== null) {
            return true;
        }

        foreach ($builder->getJoins() as $join) {
            if ($this->hasOffset($join)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function limited()
    {
        return $this->limited;
    }

    /**
     * @param Builder $builder
     * @return Table
     */
    public function add(Builder $builder)
    {
        $scope = new Scope($this, $builder);

        $table = new Table($this, $builder->getSchema());

        if ($occurrences = $this->map->occurrences($builder)) {
            $table->suffix('_' . ($occurrences + 1));
        }

        $this->map->add($builder, $table);

        return $table;
    }

    public function scope($builder)
    {
        if (!$table = $this->map->find($builder)) {
            $table = $this->add($builder);
        }

        return new Scope($this, $table);
    }
}

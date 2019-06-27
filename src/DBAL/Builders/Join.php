<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Join
 * @package Stitch\DBAL\Builders
 */
class Join extends Query
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Expression
     */
    protected $on;

    /**
     * Join constructor.
     * @param string $table
     * @param string|null $primaryKey
     */
    public function __construct(string $table, ?string $primaryKey)
    {
        parent::__construct($table, $primaryKey);

        $this->on = new Expression();
    }

    /**
     * @param string $type
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $left
     * @param string $condition
     * @param string $right
     * @return $this
     */
    public function on(string $left, string $condition, string $right)
    {
        $this->on->andRaw("$left $condition $right");

        return $this;
    }

    /**
     * @param string $left
     * @param string $condition
     * @param string $right
     * @return $this
     */
    public function orOn(string $left, string $condition, string $right)
    {
        $this->on->orRaw("$left $condition $right");

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Expression
     */
    public function getOnConditions()
    {
        return $this->on;
    }
}

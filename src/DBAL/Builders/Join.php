<?php

namespace Stitch\DBAL\Builders;

class Join extends Query
{
    protected $type;

    protected $on;

    public function __construct(string $table, ?string $primaryKey)
    {
        parent::__construct($table, $primaryKey);

        $this->on = new Expression();
    }

    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function on(string $left, string $condition, string $right)
    {
        $this->on->andRaw("$left $condition $right");

        return $this;
    }

    public function orOn(string $left, string $condition, string $right)
    {
        $this->on->orRaw("$left $condition $right");

        return $this;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function getOnConditions()
    {
        return $this->on;
    }
}

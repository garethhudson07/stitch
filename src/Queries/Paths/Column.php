<?php

namespace Stitch\Queries\Paths;

use Stitch\Schema\Column as Blueprint;

class Column extends Path
{
    protected $blueprint;

    protected $pieces;

    /**
     * Column constructor.
     * @param Blueprint $blueprint
     * @param array $pieces
     */
    public function __construct(Blueprint $blueprint, array $pieces)
    {
        $this->blueprint = $blueprint;

        parent::__construct($pieces);
    }

    /**
     * @return string
     */
    public function implode(): string
    {
        if ($this->blueprint->getType() === 'json') {
            $column = array_shift($this->pieces);
            $expression = implode('.', array_merge(['$'], $this->pieces));

            return "$column -> '$expression'";
        }

        return $this->pieces[0];
    }
}
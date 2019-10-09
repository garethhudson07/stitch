<?php

namespace Stitch\Relations;

/**
 * Class HasOne
 * @package Stitch\Relations
 */
class HasOne extends Has
{
    protected $associate = 'one';
}
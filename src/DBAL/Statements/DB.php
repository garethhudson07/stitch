<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Schema\Database;
use Stitch\DBAL\Syntax\Lexicon;

/**
 * Class DB
 * @package Stitch\DBAL\Statements\Select
 */
class DB extends Statement
{
    protected $database;

    /**
     * DB constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            Lexicon::use()
        )->push(
            $this->database->getName()
        );
    }
}

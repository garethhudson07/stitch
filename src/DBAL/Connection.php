<?php

namespace Stitch\DBAL;

use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Statements\Queries\Query as QueryStatement;
use Stitch\DBAL\Statements\Persistence\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persistence\Update as UpdateStatement;
use PDO;

class Connection
{
    protected static $driver = 'mysql';

    protected static $host = 'localhost';

    protected $pdo;

    public function __construct(string $database, string $username, string $password)
    {
        $this->pdo = new PDO(
            static::$driver . ':host=' . static::$host . ';dbname=' . $database,
             $username,
             $password
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function setDriver(string $driver)
    {
        static::$driver = $driver;
    }

    public static function setHost(string $host)
    {
        static::$host = $host;
    }

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function execute(Statement $statement)
    {
        $prepared = $this->pdo->prepare($statement->resolve());

        $prepared->execute($statement->getBindings());

        return $prepared;
    }

    public function select(QueryStatement $statement)
    {
        echo $statement;
        //exit;

        $result = $this->execute($statement);

        return $result->fetchAll();
    }

    public function insert(InsertStatement $statement)
    {
        echo $statement;

        exit;

        return $this->execute($statement);
    }

    public function update(UpdateStatement $statement)
    {
        echo $statement;

        var_dump($statement->getBindings());

        //exit;

        return $this->execute($statement);
    }
}

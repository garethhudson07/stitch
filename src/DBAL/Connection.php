<?php

namespace Stitch\DBAL;

use PDO;
use PDOStatement;
use Stitch\DBAL\Statements\Persistence\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persistence\Update as UpdateStatement;
use Stitch\DBAL\Statements\Queries\Query as QueryStatement;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Connection
 * @package Stitch\DBAL
 */
class Connection
{
    /**
     * @var string
     */
    protected static $driver = 'mysql';

    /**
     * @var string
     */
    protected static $host = 'localhost';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Connection constructor.
     * @param string $database
     * @param string $username
     * @param string $password
     */
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

    /**
     * @param string $driver
     */
    public static function setDriver(string $driver)
    {
        static::$driver = $driver;
    }

    /**
     * @param string $host
     */
    public static function setHost(string $host)
    {
        static::$host = $host;
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        $this->pdo = null;
    }

    /**
     * @param QueryStatement $statement
     * @return array
     */
    public function select(QueryStatement $statement)
    {
        $result = $this->execute($statement);

        return $result->fetchAll();
    }

    /**
     * @param Statement $statement
     * @return bool|PDOStatement
     */
    public function execute(Statement $statement)
    {
//        echo $statement;

        $prepared = $this->pdo->prepare($statement->resolve());

        $prepared->execute($statement->getBindings());

        return $prepared;
    }

    /**
     * @param InsertStatement $statement
     * @return bool|PDOStatement
     */
    public function insert(InsertStatement $statement)
    {
        return $this->execute($statement);
    }

    /**
     * @param UpdateStatement $statement
     * @return bool|PDOStatement
     */
    public function update(UpdateStatement $statement)
    {
        return $this->execute($statement);
    }
}

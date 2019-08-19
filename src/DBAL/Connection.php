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
    protected $name = 'default';

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $driver = 'mysql';

    /**
     * @var string
     */
    protected $host = 'localhost';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @return $this
     */
    public function connect()
    {
        $this->pdo = new PDO(
            $this->driver . ':host=' . $this->host . ';dbname=' . $this->database,
            $this->username,
            $this->password
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $this;
    }

    /**
     * @return PDO
     */
    public function get()
    {
        if (!$this->pdo) {
            $this->connect();
        }

        return $this->pdo;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $driver
     * @return $this
     */
    public function driver(string $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function host(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $database
     * @return $this
     */
    public function database(string $database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function username(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function password(string $password)
    {
        $this->password = $password;

        return $this;
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

        $prepared = $this->get()->prepare($statement->resolve());

        $prepared->execute($statement->getBindings());

        return $prepared;
    }

    /**
     * @param InsertStatement $statement
     * @return $this
     */
    public function insert(InsertStatement $statement)
    {
        $this->execute($statement);

        return $this;
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->get()->lastInsertId();
    }

    /**
     * @param UpdateStatement $statement
     * @return $this
     */
    public function update(UpdateStatement $statement)
    {
        $this->execute($statement);

        return $this;
    }
}

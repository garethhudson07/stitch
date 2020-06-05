<?php

namespace Stitch\DBAL;

use PDO;
use PDOStatement;
use Stitch\DBAL\Statements\Persist\Insert as InsertStatement;
use Stitch\DBAL\Statements\Persist\Update as UpdateStatement;
use Stitch\DBAL\Statements\Delete\Delete as DeleteStatement;
use Stitch\DBAL\Statements\Select\Query as QueryStatement;
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
     * @var string
     */
    protected $charset = 'utf8';

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
            $this->driver . ':host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset,
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
     * @param string $charset
     * @return $this
     */
    public function charset(string $charset)
    {
        $this->charset = $charset;

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
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
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
//        echo $statement . '<br>';
//        var_dump($statement->bindings());

        $prepared = $this->get()->prepare($statement->query());

        $prepared->execute($statement->bindings());

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
     * @return string
     */
    public function lastInsertId()
    {
        return $this->get()->lastInsertId();
    }

    /**
     * @param UpdateStatement $statement
     * @return bool|PDOStatement
     */
    public function update(UpdateStatement $statement)
    {
        return $this->execute($statement);
    }

    /**
     * @param DeleteStatement $statement
     * @return bool|PDOStatement
     */
    public function delete(DeleteStatement $statement)
    {
        return $this->execute($statement);
    }
}

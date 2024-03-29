<?php

namespace Stitch\DBAL;

use Closure;
use PDO;
use PDOStatement;
use Stitch\DBAL\Schema\Database;
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
     * @var array
     */
    protected $databases = [];

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
        $this->pdo = new RobustPDO(
            $this->driver . ':host=' . $this->host . ';charset=' . $this->charset,
            $this->username,
            $this->password,
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        return $this;
    }

    /**
     * @return RobustPDO
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
     * @param $arg
     * @return $this
     */
    public function database($arg)
    {
        $db = new Database();

        if ($arg instanceof Closure) {
            $arg($db);
        } else {
            $db->name($arg);
        }

        if (!$db->getAlias()) {
            $db->alias(
                count($this->databases) ? $db->getName() : 'default'
            );
        }

        $this->databases[$db->getAlias()] = $db;

        return $this;
    }

    /**
     * @return array
     */
    public function getDatabases()
    {
        return $this->databases;
    }

    /**
     * @param string $alias
     * @return null|Database
     */
    public function getDatabase(?string $alias = null): ?Database
    {
        return $this->databases[$alias ?: 'default'] ?? null;
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

        $this->get()->tryExecuteStatement($prepared, $statement->bindings());

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

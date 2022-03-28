<?php

// https://gist.github.com/christiaangoossens/6fb1720bf854e6a79d630a8f9e7af66e

namespace Stitch\DBAL;

use PDO;
use PDOException;
use PDOStatement;

class RobustPDO
{
    /**
     * Call setAttribute to set the session wait_timeout value
     */
    const ATTR_MYSQL_TIMEOUT = 100;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * For lazy connection tracking
     *
     * @var bool
     */
    protected $_connected = false;

    /**
     * Cached attributes for reconnection
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Create a new PDO object.
     * Does not connect to the database until needed.
     *
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     * @param string|null $user [optional] The user name for the DSN string. This parameter is optional for some PDO drivers.
     * @param string|null $pass [optional] The password for the DSN string. This parameter is optional for some PDO drivers.
     * @param array|null $options [optional] A key=>value array of driver-specific connection options.
     */
    public function __construct(string $dsn, ?string $user = null, ?string $pass = null, ?array $options = null)
    {
        //Save connection details for later
        $this->config = [
            'dsn'     => $dsn,
            'user'    => $user,
            'pass'    => $pass,
            'options' => $options ?? [],
        ];

        // Throw exceptions when there's an error so we can catch them
        $this->config['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

        if (isset($this->config['options'][self::ATTR_MYSQL_TIMEOUT])) {
            $this->attributes[self::ATTR_MYSQL_TIMEOUT] = $this->config['options'][self::ATTR_MYSQL_TIMEOUT];
        }
    }

    /**
     * Verifies that the PDO connection is still active. If not, it reconnects.
     */
    public function reconnect()
    {
        $this->pdo = new PDO($this->config['dsn'], $this->config['user'], $this->config['pass'], $this->config['options']);

        // Reapply attributes to the new connection
        foreach ($this->attributes as $attr => $value) {
            $this->_setAttribute($attr, $value);
        }

        $this->_connected = true;
    }

    /**
     * Try to call the function on the pdo object inside a try/catch to detect a disconnect
     *
     * @throws PDOException
     */
    public function __call($name, $arguments)
    {
        if (!$this->_connected) {
            $this->reconnect();
        }

        try {
            return call_user_func_array([$this->pdo, $name], $arguments);
        } catch (PDOException $e) {
            if (static::hasGoneAway($e)) {
                $this->reconnect();
                return call_user_func_array([$this->pdo, $name], $arguments);
            } else {
                throw $e;
            }
        }
    }

    /**
     * {@InheritDoc}
     *
     * Caches setAttribute calls to be reapplied on reconnect
     */
    public function setAttribute(int $attribute, $value): bool
    {
        $this->attributes[$attribute] = $value;

        if ($this->_connected) {
            try {
                $this->_setAttribute($attribute, $value);
            } catch (PDOException $e) {
                if (static::hasGoneAway($e)) {
                    $this->reconnect();
                } else {
                    throw $e;
                }
            }
        }

        return true;
    }

    /**
     * Executes a prepared statement inside a try/catch to watch for server disconnections
     *
     * Use to detect connection drops between prepare and PDOStatement->execute
     * Usage:
     * <code>
     * $st = $this->robustPDO->prepare('SELECT :one');
     * if ($this->robustPDO->tryExecuteStatement($st, ['one'=>1])) { print_r($st->fetchAll()); }
     * </code>
     *
     * @param PDOStatement $st                PDOStatement to execute
     * @param null|array   $input_parameters  An array of values with as many elements as there are bound parameters in the SQL statement
     * @param bool         $recursion         If TRUE, don't retry the operation
     * @throws PDOException If the statement throws an exception that's not a disconnect
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function tryExecuteStatement(PDOStatement &$st, $input_parameters = null, $recursion = false)
    {
        try {
            if (is_array($input_parameters)) {
                return $st->execute($input_parameters);
            } else {
                return $st->execute();
            }
        } catch (PDOException $e) {
            if (!$recursion && static::hasGoneAway($e)) {
                $this->reconnect();
                $st = $this->prepare($st->queryString);
                return $this->tryExecuteStatement($st, $input_parameters, true);
            }

            throw $e;
        }
    }

    /**
     * Check if a PDOException is a server disconnection or not
     *
     * @param PDOException $e
     * @return bool Returns TRUE if the PDOException is any of the specified connection errors or FALSE for another error
     */
    public static function hasGoneAway(PDOException $e)
    {
        $errors = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'Transaction() on null',
            'child connection forced to terminate due to client_idle_limit',
            'query_wait_timeout',
            'reset by peer',
            'Physical connection is not usable',
            'TCP Provider: Error code 0x68',
            'ORA-03114',
            'Packets out of order. Expected',
            'Adaptive Server connection failed',
            'Communication link failure',
            'connection is no longer usable',
            'Login timeout expired',
        ];

        foreach ($errors as $error) {
            if (stripos($e->getMessage(), $error) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ping the PDO connection to keep it alive by sending a SELECT 1
     *
     * @return bool
     */
    public function ping()
    {
        return (1 === intval($this->query('SELECT 1')->fetchColumn(0)));
    }

    /**
     * Sets a connection attribute. Adds support for additional custom attributes from this class.
     *
     * This calls setAttribute right away and requires that the PDO connection be open.
     *
     * @param int   $attribute
     * @param mixed $value
     * @return bool Returns TRUE on success or FALSE on failure
     */
    protected function _setAttribute($attribute, $value)
    {
        if ($attribute === self::ATTR_MYSQL_TIMEOUT) {
            $this->pdo->exec("SET SESSION wait_timeout=" . intval($value));
            return true;
        } else {
            return $this->pdo->setAttribute($attribute, $value);
        }
    }
}

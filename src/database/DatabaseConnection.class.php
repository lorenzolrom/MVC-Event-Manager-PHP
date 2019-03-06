<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:43 PM
 */


namespace database;


use exceptions\DatabaseException;
use exceptions\ErrorMessages;

class DatabaseConnection
{
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;
    const FETCH_COLUMN = \PDO::FETCH_COLUMN;
    const FETCH_CLASS = \PDO::FETCH_CLASS;

    // Parameter Types
    const PARAM_BOOL = \PDO::PARAM_BOOL;
    const PARAM_NULL = \PDO::PARAM_NULL;
    const PARAM_INT = \PDO::PARAM_INT;
    const PARAM_STR = \PDO::PARAM_STR;

    private $handler; // Database interaction object

    /**
     * DatabaseConnection constructor.  Automatically connects to database using configured options.
     * @throws DatabaseException In event of database connection failure
     */
    public function __construct()
    {
        try
        {
            $this->handler = new \PDO("mysql:host=" . \FB_CONFIG['databaseHost'] . ";dbname=" . \FB_CONFIG['databaseName'],
                \FB_CONFIG['databaseUser'],
                \FB_CONFIG['databasePassword'],
                array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC));
        }
        catch(\PDOException $e)
        {
            throw new DatabaseException(ErrorMessages::DATABASE_FAILED_TO_CONNECT, DatabaseException::FAILED_TO_CONNECT, $e);
        }
    }

    /**
     * @param string $query Raw SQL query string
     * @return \PDOStatement Results of PHP query
     * @throws DatabaseException In event of query failure
     */
    public function query(string $query): \PDOStatement
    {
        try
        {
            return $this->handler->query($query);
        }
        catch(\PDOException $e)
        {
            throw new DatabaseException(ErrorMessages::DATABASE_DIRECT_QUERY_FAILED, DatabaseException::DIRECT_QUERY_FAILED, $e);
        }
    }

    /**
     * Returns a prepared SQL statement for this connection handler
     * @param string $query SQL query string
     * @return PreparedStatement
     */
    public function prepare(string $query): PreparedStatement
    {
        return new PreparedStatement($this->handler, $query);
    }

    /**
     * @return int Row ID of the last inserted record
     */
    public function getLastInsertId(): int
    {
        return $this->handler->lastInsertId();
    }

    /**
     * Starts a database transaction and sets auto-commit mode to FALSE
     * @return bool Transaction Succeeded
     * @throws DatabaseException In event transaction cannot be started
     */
    public function startTransaction(): bool
    {
        try
        {
            $this->handler->beginTransaction();
            return TRUE;
        }
        catch(\PDOException $e)
        {
            throw new DatabaseException(ErrorMessages::DATABASE_TRANSACTION_START_FAILED, DatabaseException::TRANSACTION_START_FAILED, $e);
        }
    }

    /**
     * Rollback current database transaction and sets auto-commit mode to TRUE
     * @return bool Rollback succeeded
     * @throws DatabaseException In event transaction cannot be rolled back
     */
    public function rollback(): bool
    {
        try
        {
            $this->handler->rollBack();
            return TRUE;
        }
        catch(\PDOException $e)
        {
            throw new DatabaseException(ErrorMessages::DATABASE_TRANSACTION_ROLLBACK_FAILED, DatabaseException::TRANSACTION_ROLLBACK_FAILED, $e);
        }
    }

    /**
     * Commits current database transaction and sets auto-commit mode to TRUE
     * @return bool Commit succeeded
     * @throws DatabaseException In event transaction cannot be committed
     */
    public function commit(): bool
    {
        try
        {
            $this->handler->commit();
            return TRUE;
        }
        catch(\PDOException $e)
        {
            throw new DatabaseException(ErrorMessages::DATABASE_TRANSACTION_COMMIT_FAILED, DatabaseException::TRANSACTION_COMMIT_FAILED, $e);
        }
    }

    /**
     * Un-sets the database connection, symbolically closing the connection
     */
    public function close()
    {
        $this->handler = NULL;
    }
}
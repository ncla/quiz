<?php

namespace App\DB;

use PDO;
use PDOException;

/**
 * Database helper
 * https://draghici.net/2017/12/09/php-pdo-mysql-helper-class-used-slimphp-applications/
 * TODO: This is more like MysqlDatabase, and could use some abstraction.
 */
class Database
{
    /**
     * Private variable to store the connection
     * @var Object
     */
    private $connection;

    /**
     * Constructor for the database function
     * @param array $settings List of settings
     * @throws DatabaseException
     */
    public function __construct($settings)
    {
        try {
            $pdo = new PDO(
                "mysql:host="
                . $settings['host'] . ";dbname=" .
                $settings['dbname'],
                $settings['user'],
                $settings['pass']
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->connection = $pdo;
        } catch (PDOException $e) {
            throw new DatabaseException('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Wrapper to query the Database
     * @param  String $sql    SQL command
     * @param  array  $params Parameters for the command
     * @throws DatabaseException
     * @return Object         The output of the command
     */
    public function query($sql, $params = [])
    {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($params);

            return $query;
        } catch (PDOException $e) {
            throw new DatabaseException('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the last insert id from the SQL Server
     * @return Integer  The last insert id
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}

<?php

/**
 * Database Class
 *
 * Handles database connection and query execution.
 */
class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbName;
    protected $conn;

    /**
     * Database constructor.
     *
     * @param string|null $host     Database host
     * @param string|null $user     Database username
     * @param string|null $pass     Database password
     * @param string|null $dbName   Database name
     */
    public function __construct($host = null, $user = null, $pass = null, $dbName = null)
    {
        $this->host = $host ?? DB_HOST;
        $this->user = $user ?? DB_USER;
        $this->pass = $pass ?? DB_PASSWORD;
        $this->dbName = $dbName ?? DB_NAME;

        $this->connect();
    }

    /**
     * Connect to the database.
     *
     * @return void
     */
    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName}";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Could not connect to the database.');
        }
    }

    /**
     * Execute a query and fetch the first row.
     *
     * @param string $query   SQL query
     * @param array  $params  Parameters for the query
     *
     * @return mixed|null The first row of the result or null on failure
     */
    public function queryOne($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Execute a query.
     *
     * @param string $query   SQL query
     * @param array  $params  Parameters for the query
     *
     * @return bool True on success, false on failure
     */
    public function execute($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Execute a query and fetch a single value.
     *
     * @param string $query   SQL query
     * @param array  $params  Parameters for the query
     *
     * @return mixed|null The single value or null on failure
     */
    public function querySingleValue($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}

<?php
require_once(UTILS . 'prettyError.php');
require_once(UTILS . 'prettyDump.php');

/**
 * Database connection and query execution class.
 *
 * Provides methods for executing SQL queries and modifying records.
 *
 * @package Database
 *
 * @property \PDO $pdo The PDO instance for database connection.
 * @method \PDOStatement|false query(string $sql, array $values = [], bool $showList = false) Executes a query and returns a PDOStatement or false.
 * @method array|false insert(string $table, array $values, bool $insertCreatedAt = true) Inserts a record and returns status details.
 * @method array update(string $table, array $values, array $where, bool $insertUpdatedAt = true) Updates records in a table and returns affected rows.
 */
class DB
{
    private $pdo;

    public function __construct()
    {
        $host   = $_ENV['DB_HOST'];
        $user   = $_ENV['DB_USER'];
        $pass   = $_ENV['DB_PASS'];
        $dbname = $_ENV['DB_NAME'];
        try {
            $dsn       = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Enable exceptions for errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch data as associative array
                PDO::ATTR_EMULATE_PREPARES   => false                    // Prevent SQL injection
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Executes a SQL query.
     * @param string $sql - The SQL query to execute.
     * @param array $values - The values to bind in the query.
     * @return \PDOStatement|false $stmt - The result of the query or false on failure.
     */
    public function query(string $sql, array $values = [], $showList = false)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);

            // Ensure we return the PDOStatement for further method calls
            if ($showList) {
                pretty_dump(get_class_methods($stmt));  // Should list all methods
            } else {
                return $stmt;
            }
        } catch (PDOException $e) {
            pretty_error('Error executing query: ' . $e->getMessage() . "\n\n" . $sql . "\n\n", 'Mysql Error', 'Data:' . pretty_dump($values));

            // 🔹 Check what `$stmt` actually is before calling `get_class_methods()`
            if (isset($stmt) && is_object($stmt) && !$stmt->errorCode) {
                pretty_dump(get_class_methods($stmt));  // This should now work correctly
            } else {
                pretty_dump('Error: $stmt is not an object, instead it is: ' . gettype($stmt), 'Mysql Error');
            }

            return false;
        }
    }

    /**
     * Inserts a new record into the specified table.
     *
     * @param string $table - The table name.
     * @param array $values - Associative array of column => value.
     * @param bool $insertCreatedAt - Whether to include a created_at timestamp.
     * @return \PDOStatement|false - Status of the operation.
     */
    public function insert(string $table, array $values, bool $insertCreatedAt = true)
    {
        if ($insertCreatedAt) {
            $values['createdAt'] = time();
        }

        $columns      = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql          = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($values));

            return (object) [
                'success'      => true,
                'insertId'     => $this->pdo->lastInsertId(),
                'affectedRows' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            // return [
            //     'success' => false,
            //     'message' => $e->getMessage()
            // ];

            echo '<h1>SQL Error</h1>';
            pretty_error('Error executing query: ' . $e->getMessage() . "\n\n");
            echo '<h1>SQL Values</h1>';
            pretty_dump($values);

            // Check what `$stmt` actually is before calling `get_class_methods()`
            if (isset($stmt) && is_object($stmt)) {
                pretty_dump(get_class_methods($stmt));  // This should now work correctly
            } else {
                pretty_dump('Error: $stmt is not an object, instead it is: ' . gettype($stmt));
            }

            return false;
        }
    }

    /**
     * Updates records in a specified table.
     *
     * Useage: update('table_name', ['updateName' => $updateName_value], 'WHERE some_field = ?', [$whereValues]);
     *
     * @param string $table - The table name.
     * @param array $values - Associative array of column => new value.
     * @param string $whereClause - Associative array of column => condition value.
     * @param array $whereValues - Array values of the where values
     * @param bool $insertUpdatedAt - Whether to include an updated_at timestamp.
     * @return array - Status of the operation.
     */
    public function update(string $table, array $values, string $whereClause, array $whereValues = [], bool $insertUpdatedAt = true)
    {
        if ($insertUpdatedAt) {
            $values['updatedAt'] = time();
        }

        # Construct SET clause
        $setClause = implode(', ', array_map(fn($col) => "$col = ?", array_keys($values)));

        # Final SQL statement
        $sql = "UPDATE $table SET $setClause $whereClause";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($values), $whereValues));

            return [
                'success'      => true,
                'affectedRows' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

<?php
class DatabaseClass {
    private $connection=false;
    private $hostname="localhost";
    private $database="employee_management_db";
    private $username="root";
    private $password="";

    public function __construct() {
        try {
        $this->connection = mysqli_connect($this->hostname,$this->username, $this->password,$this->database);

        if (mysqli_connect_errno()) {
         throw new Exception("Connection failed: " . (mysqli_connect_error() ?? "Database connection faild"));
        }

    }catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        die;
     }

    }

    public function getConnection(){
      return $this->connection;
    }
    // // Add new record to the database
    public function addRecord($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = "?" . str_repeat(", ?", count($data) - 1);
    
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = mysqli_prepare($this->connection, $sql);
    
        // Dynamically determine the data types and create the types string
        $types = '';
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd'; 
            } elseif (is_string($value)) {
                $types .= 's'; 
            } elseif (is_null($value)) {
                $types .= 's'; 
            }
        }
    
        mysqli_stmt_bind_param($stmt, $types, ...array_values($data));
        
        if (mysqli_stmt_execute($stmt)) {

            return mysqli_insert_id($this->connection);

        } else {
            return false;
        }
    }

    // Update an existing record in the database
    public function updateRecord($table, $data, $condition) {
         try{
        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "$column = ?";
        }
        $setString = implode(", ", $setClauses);
        
        // Handle where condition
        $whereClauses = [];
        foreach ($condition as $column => $value) {
            $whereClauses[] = "$column = ?";
        }
        $whereString = implode(" AND ", $whereClauses);

        $sql = "UPDATE $table SET $setString WHERE $whereString";
        $stmt = mysqli_prepare($this->connection, $sql);

        // Bind parameters for both SET and WHERE clauses
        $types = str_repeat('s', count($data) + count($condition));
        $params = array_merge(array_values($data), array_values($condition));
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        // Execute the query and return the result
        $result=mysqli_stmt_execute($stmt);
        
        if($result){
            return true;
        }else{
            return false;
         }
      }catch (Exception $e) {
       return false;
     }
    }

    // View records from the database
    public function viewRecords($table, $columns = "*", $condition = []) {
        try{
        // Create SQL query dynamically
        $sql = "SELECT $columns FROM $table";

        if (!empty($condition)) {
            $whereClauses = [];
            foreach ($condition as $column => $value) {
                $whereClauses[] = "$column = ?";
            }
            $whereString = implode(" AND ", $whereClauses);
            $sql .= " WHERE $whereString";
        }

        $stmt = mysqli_prepare($this->connection, $sql);

        // Bind the condition values if provided
        if (!empty($condition)) {
            $types = str_repeat('s', count($condition));
            mysqli_stmt_bind_param($stmt, $types, ...array_values($condition));
        }

        // Execute the query and return the result
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }catch(Exception $e){
        return [];
    }
    }
    
    // Close the database connection
    public function close() {
        mysqli_close($this->connection);
    }

   public function loadEnv($file) {
        if (file_exists($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Ignore comments and lines without an equals sign
                if (strpos($line, '#') === 0 || strpos($line, '=') === false) {
                    continue;
                }
    
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
    
                // Set the environment variable
                putenv("$key=$value");

                /*require_once 'load_env.php';

                // Load the .env file
                loadEnv('.env');

                // Now you can access the environment variables using getenv()
                $dbHost = getenv('DB_HOST');
                $dbName = getenv('DB_NAME');
                $dbUser = getenv('DB_USER');
                $dbPassword = getenv('DB_PASSWORD'); */
            }
        }
    }
      //close close connection when object is destroyed
    public function __destruct() {
       $this->close();
    }
   }

   
?>
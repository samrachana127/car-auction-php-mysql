<?php

// Database connection parameters
$server = 'mysql';        // Docker service name for MySQL, as defined in docker-compose
$username = 'v.je';       // MySQL username, set in docker-compose under MYSQL_USER
$password = 'v.je';       // MySQL password, set in docker-compose under MYSQL_PASSWORD
$database = "assignment1"; // Name of the database to connect to

try {
    // Create a new PDO instance to connect to the MySQL database
    $datapageConnection = new PDO('mysql:host=' . $server . ';dbname=' . $database, $username, $password);
    
    // Set the PDO error mode to exception to handle errors gracefully
    $datapageConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set the character set to UTF-8 for proper encoding of data
    $datapageConnection->exec("set names utf8");
} catch (PDOException $e) {
    // If the connection fails, catch the exception and display an error message
    echo "Connection failed: " . $e->getMessage();
}
?>
<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tuba_money_transfer";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create tables if they don't exist
$sql = "CREATE TABLE IF NOT EXISTS customers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating customers table: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS accounts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    account_holder VARCHAR(100) NOT NULL,
    bank VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating accounts table: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS transactions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT(11) NOT NULL,
    account_id INT(11) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (account_id) REFERENCES accounts(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating transactions table: " . $conn->error);
}
?>


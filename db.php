<?php
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Change if you have a different database user
$password = "root"; // Change if you have set a password for MySQL
$database = "bank_expenditure"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

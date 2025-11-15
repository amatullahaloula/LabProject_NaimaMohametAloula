<?php
// Load .env file variables
$env = parse_ini_file('../.env');

// Assign variables
$servername = $env['host'];
$username = $env['user'];
$password = $env['pass'];
$database = $env['db'];

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

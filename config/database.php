<?php
// These are the settings PHP uses to connect to MariaDB
define('DB_HOST', 'localhost');
define('DB_USER', 'finance_user');
define('DB_PASS', 'finance_pass');
define('DB_NAME', 'finance_tracker');

function getConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}

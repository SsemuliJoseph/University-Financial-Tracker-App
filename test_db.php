<?php
require_once 'config/database.php';

$conn = getConnection();
echo "Connected successfully to: " . DB_NAME;
$conn->close();
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u318220648_basilius');
define('DB_PASS', 'Nirmaan@1234');
define('DB_NAME', 'u318220648_basilius');

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete all activity logs if they are older than 90 days.
$query = "DELETE FROM `tblmodule_activity_log` WHERE DATE(date) <= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
$conn->query($query);

$conn->close();
?>
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "urlShortner";

// Enable exception mode for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // If connection fails
    die("❌ Connection failed: " . $e->getMessage());
}
?>

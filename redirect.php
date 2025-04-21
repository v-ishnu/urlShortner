<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_config.php';

if (!isset($_GET['c'])) {
    http_response_code(404);
    echo "Short code not provided.";
    exit;
}

$code = $_GET['c'];

// Find matching long URL
$stmt = $conn->prepare("SELECT original_url FROM urls WHERE short_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // Increment click count
    $update = $conn->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = ?");
    $update->bind_param("s", $code);
    $update->execute();

    header("Location: " . $row['original_url']);
    exit;
} else {
    http_response_code(404);
    echo "Short URL not found.";
}
?>

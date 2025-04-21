<?php
session_start();
include 'db_config.php';

// Only logged in users
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId     = $_SESSION['user_id'];
$longUrl    = trim($_POST['long_url']    ?? '');
$customCode = trim($_POST['custom_code'] ?? '');

// Validate URL
if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
    $msg    = 'Invalid URL.';
    $status = 'error';
    header("Location: dashboard.php?status={$status}&message=" . urlencode($msg));
    exit;
}

// Auto‑generate code if blank
if ($customCode === '') {
    $customCode = substr(md5(uniqid('', true)), 0, 6);
}

// Check uniqueness
$chk = $conn->prepare("SELECT id FROM short_urls WHERE short_code = ?");
$chk->bind_param("s", $customCode);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
    $msg    = 'Short code already in use.';
    $status = 'error';
    header("Location: dashboard.php?status={$status}&message=" . urlencode($msg));
    exit;
}
$chk->close();

// Insert into database
$ins = $conn->prepare("
    INSERT INTO short_urls (user_id, long_url, short_code, created_at)
    VALUES (?, ?, ?, NOW())
");
$ins->bind_param("iss", $userId, $longUrl, $customCode);

if ($ins->execute()) {
    $msg    = 'Short URL created: ' . $customCode;
    $status = 'success';
} else {
    $msg    = 'Database error.';
    $status = 'error';
}
$ins->close();
$conn->close();

// Redirect back with status + message
header("Location: dashboard.php?status={$status}&message=" . urlencode($msg));
exit;

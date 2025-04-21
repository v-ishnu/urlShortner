<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_SESSION['user_id'])) {
    $id = intval($_POST['id']);
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM urls WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php");
exit;
?>

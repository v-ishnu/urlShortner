<?php
session_start();
include 'db_config.php';

$errorMsg = '';

function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $userName, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userName;

            // Generate token
            $token = generateToken();

            // Save token to database
            $updateTokenStmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
            $updateTokenStmt->bind_param("si", $token, $userId);
            $updateTokenStmt->execute();

            // Set token as cookie (valid for 7 days)
            setcookie("auth_token", $token, time() + (7 * 24 * 60 * 60), "/", "", false, true);

            header("Location: dashboard.php");
            exit;
        } else {
            $errorMsg = "Incorrect password.";
        }
    } else {
        $errorMsg = "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - urlShortner</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="login-wrapper">
        <form class="login-card" method="POST" action="">
            <h2>Login</h2>

            <?php if (!empty($errorMsg)): ?>
                <div class="error-message"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>

            <label for="password">Password <a href="#" class="forgot">Forgot Password?</a></label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="login-submit">Login</button>

            <p class="login-footer">Don't have an account? <a href="signup.php">Sign up</a></p>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

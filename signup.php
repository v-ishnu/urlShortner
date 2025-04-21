<?php
include 'db_config.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            try {
                $stmt->execute();
                $successMsg = "✅ Registration successful! You can now <a href='login.php'>login</a>.";
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $errorMsg = "❌ Email already exists. Please <a href='login.php'>login</a>.";
                } else {
                    $errorMsg = "Database error: " . $e->getMessage();
                }
            }

            $stmt->close();
        } else {
            $errorMsg = "Something went wrong. Please try again.";
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - urlShortner</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="signup-container">
        <div class="signup-box">
            <h2>Create Account</h2>

            <!-- Success / Error Message -->
            <?php if (!empty($successMsg)): ?>
                <div class="success-message"><?php echo $successMsg; ?></div>
            <?php elseif (!empty($errorMsg)): ?>
                <div class="error-message"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Name" required />
                </div>
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required />
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <button type="submit" class="signup-submit-btn">Sign Up</button>
            </form>

            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

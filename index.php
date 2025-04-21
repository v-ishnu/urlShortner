<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>urlShortner - Short links, big impact</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="main-content">
        <p class="tagline">Simplify your links instantly</p>
        <h1>Short links, big impact</h1>
        <p class="subtext">
            Streamline your online experience with our fast and intuitive URL shortener.<br>
            Create memorable links that are easy to share.
        </p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Show login/signup prompt if not logged in -->
            <div class="card">
                <div class="lock-icon">🔒</div>
                <h2>Login Required</h2>
                <p>You need to be logged in to shorten URLs. Login or create an account to continue.</p>
                <div class="card-buttons">
                    <button class="login-btn" onclick="location.href='login.php'">Login</button>
                    <button class="create-btn" onclick="location.href='signup.php'">Create Account</button>
                </div>
            </div>
        <?php else: ?>
            <!-- Show dashboard content if logged in -->
            <section class="url-shortener-box">
                <h3>Shorten a new URL</h3>
                <form method="POST" action="dashboard.php" class="shorten-form">
                    <input type="url" name="long_url" placeholder="Enter your long URL" required>
                    <input type="text" name="custom_code" placeholder="Custom short code (optional)">
                    <button type="submit">Shorten URL</button>
                </form>
            </section>
        <?php endif; ?>

        <div class="features">
            <div class="feature">
                ⚡<h3>Lightning Fast</h3>
                <p>Create shortened URLs in seconds with our streamlined interface.</p>
            </div>
            <div class="feature">
                🔐<h3>Secure & Reliable</h3>
                <p>Your links are safe with us and available whenever you need them.</p>
            </div>
            <div class="feature">
                ✏️<h3>Easy to Use</h3>
                <p>Simple interface designed for the best user experience.</p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>

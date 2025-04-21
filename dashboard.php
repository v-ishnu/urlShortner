<?php
session_start();
include 'db_config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check login via session or cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $userName);
        $stmt->fetch();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
    } else {
        header("Location: index.php");
        exit;
    }
    $stmt->close();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['long_url'])) {
    $userId = $_SESSION['user_id'];
    $longUrl = trim($_POST['long_url']);
    $customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';

    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
        $error = "Invalid URL format.";
    } else {
        if (empty($customCode)) {
            $customCode = substr(md5(uniqid(rand(), true)), 0, 6);
        }

        $check = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
        $check->bind_param("s", $customCode);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Short code already exists. Try another one.";
        } else {
            $stmt = $conn->prepare("INSERT INTO urls (user_id, original_url, short_code) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $longUrl, $customCode);

            if ($stmt->execute()) {
                $success = "✅ URL shortened successfully!";
            } else {
                $error = "❌ Something went wrong while shortening the URL.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Brevity</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .copy-feedback {
            display: none;
            color: green;
            margin-left: 8px;
            font-size: 0.9rem;
        }
        .url-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            background: #fafafa;
        }
        .url-card a {
            color: #007bff;
        }
        .alert.success { color: green; }
        .alert.error { color: red; }
        .actions button {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1.2rem;
            margin-right: 8px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="dashboard">
    <h2>Dashboard</h2>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>

    <?php if (!empty($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <section class="url-shortener-box">
        <h3>Shorten a new URL</h3>
        <form method="POST" class="shorten-form">
            <input type="url" name="long_url" placeholder="Enter your long URL" required>
            <input type="text" name="custom_code" placeholder="Custom short code (optional)">
            <button type="submit">Shorten URL</button>
        </form>
    </section>

    <section class="shortened-urls-box">
        <h3>Your shortened URLs</h3>

        <?php
        $query = $conn->prepare("SELECT * FROM urls WHERE user_id = ? ORDER BY created_at DESC");
        $query->bind_param("i", $_SESSION['user_id']);
        $query->execute();
        $result = $query->get_result();

        while ($row = $result->fetch_assoc()):
            $shortUrl = "http://localhost/urlShortner/" . $row['short_code'];
        ?>
            <div class="url-card">
                <p><strong>Your shortened URL:</strong></p>
                <a href="<?= $shortUrl ?>" target="_blank"><?= $shortUrl ?></a>
                <p>Original: <?= htmlspecialchars($row['original_url']) ?></p>
                <div class="meta">
                    👁️ <?= $row['click_count'] ?> visits &nbsp;&nbsp; 🕒 <?= date('F j, Y', strtotime($row['created_at'])) ?>
                </div>
                <div class="actions">
                    <form method="POST" action="delete_url.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" class="delete" title="Delete">🗑</button>
                    </form>
                    <button class="copy" onclick="copyUrl('<?= $shortUrl ?>', this)" title="Copy URL">📋</button>
                    <span class="copy-feedback">Copied!</span>
                </div>
            </div>
        <?php endwhile; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
function copyUrl(url, button) {
    navigator.clipboard.writeText(url).then(() => {
        const feedback = button.nextElementSibling;
        feedback.style.display = 'inline';
        feedback.textContent = 'Copied!';
        setTimeout(() => {
            feedback.style.display = 'none';
        }, 2000);
    }).catch(err => {
        alert("Failed to copy: " + err);
    });
}
</script>
</body>
</html>

<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $newEmail = trim($_POST['email']);

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $newEmail, $userId);

        if ($stmt->execute()) {
            $success = "✅ Email updated successfully.";
        } else {
            $error = "❌ Failed to update email.";
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<style>
    .profile-container {
  max-width: 700px;
  margin: 2rem auto;
  padding: 1.5rem;
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
  font-family: "Poppins", sans-serif;

  .profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;

    .profile-pic {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-right: 1.5rem;
    }

    .user-info {
      h2 {
        margin: 0;
        font-size: 1.5rem;
      }
      p {
        margin: 0.3rem 0;
        color: #777;
      }

      .badge {
        background-color: #007bff;
        color: white;
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
      }
    }
  }

  .stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;

    .stat-box {
      flex: 1;
      text-align: center;
      border-right: 1px solid #eee;

      &:last-child {
        border-right: none;
      }

      h3 {
        margin: 0;
        font-size: 1.8rem;
        color: #333;
      }

      p {
        margin: 0.3rem 0;
        color: #666;
        font-size: 0.9rem;
      }
    }
  }

  .action-bar {
    text-align: center;
    margin-bottom: 2rem;

    .btn-primary {
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      border: none;
      background-color: #007bff;
      color: white;
      border-radius: 0.5rem;
      cursor: pointer;

      &:hover {
        background-color: #0056b3;
      }
    }
  }

  .recent-urls {
    h4 {
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }

    ul {
      list-style: none;
      padding: 0;

      li {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px solid #eee;

        .url-title {
          font-weight: 500;
          color: #333;
        }

        a {
          text-decoration: none;
          color: #007bff;
          font-size: 0.9rem;

          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }
}

</style>

<body>
    <?php include 'header.php'; ?>

    <!-- <div class="profile-container">
        <div class="card">
            <h2>👤 Your Profile</h2>
            <p class="sub">Logged in as <strong><?= htmlspecialchars($name) ?></strong></p>

            <?php if (!empty($success)): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php elseif (!empty($error)): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <label for="email">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <button type="submit">💾 Update Email</button>
            </form>
        </div>
    </div> -->

    <div class="profile-container">
        <div class="profile-header">
            <img src="https://i.pravatar.cc/100" alt="Profile Picture" class="profile-pic" />
            <div class="user-info">
                <h2>Vishnu Prakash</h2>
                <p>vishnu@example.com</p>
                <span class="badge">Premium User</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat-box">
                <h3>32</h3>
                <p>URLs Created</p>
            </div>
            <div class="stat-box">
                <h3>1,240</h3>
                <p>Total Clicks</p>
            </div>
            <div class="stat-box">
                <h3>2 days ago</h3>
                <p>Last Active</p>
            </div>
        </div>

        <div class="action-bar">
            <button class="btn-primary">+ Create New Short URL</button>
        </div>

        <div class="recent-urls">
            <h4>Recent URLs</h4>
            <ul>
                <li>
                    <span class="url-title">dinestx.in/meet</span>
                    <a href="#">View Stats</a>
                </li>
                <li>
                    <span class="url-title">dinestx.in/offer</span>
                    <a href="#">View Stats</a>
                </li>
                <li>
                    <span class="url-title">dinestx.in/launch</span>
                    <a href="#">View Stats</a>
                </li>
            </ul>
        </div>
    </div>


    <?php include 'footer.php'; ?>
</body>

</html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
  <div class="container">
    <div class="logo">
      <img src="assets/brevity-icon.png" alt="url" />
      <span>Shortner</span>
    </div>
    <nav>
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Profile Icon -->
        <div id="profileIcon" class="profile-icon">
          <?php echo strtoupper($_SESSION['user_name'][0]); ?>
        </div>
        <!-- Profile Modal -->
        <div id="profileModal" class="profile-modal">
          <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="login.php">Login</a>
        <button class="signup-btn" onclick="location.href='signup.php'">Sign Up</button>
      <?php endif; ?>
    </nav>
  </div>
</header>

<script>
// Toggle profile modal visibility
document.addEventListener('DOMContentLoaded', function () {
  const profileIcon = document.getElementById('profileIcon');
  const profileModal = document.getElementById('profileModal');

  // When profile icon is clicked, toggle modal visibility
  profileIcon.addEventListener('click', function (e) {
    e.stopPropagation(); // Prevent event from bubbling to document
    profileModal.classList.toggle('active');
  });

  // Close the modal if the user clicks outside of it
  document.addEventListener('click', function (e) {
    if (!profileIcon.contains(e.target) && !profileModal.contains(e.target)) {
      profileModal.classList.remove('active');
    }
  });
});
</script>

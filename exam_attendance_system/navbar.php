<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
$user = null;
$avatar = 'assets/default_avatar.png';
$name = 'User';
$role = 'unknown';

if ($user_id) {
  $result = $conn->query("SELECT avatar, name, role FROM users WHERE id = $user_id");
  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/default_avatar.png';
    $name = $user['name'] ?? 'User';
    $role = $user['role'] ?? 'unknown';
  }
}
?>

<div class="navbar">
  <div class="left">
    🎓 <strong>Exam Attendance System</strong>
  </div>

  <div class="right">
    <a href="dashboard.php">Dashboard</a>

    <?php if ($role === 'admin' || $role === 'lecturer'): ?>
      <a href="list_exams.php">All Exams</a>
      <a href="add_user.php">Add User</a>
      <a href="modify_user.php">Modify User</a>
      <a href="manage_programs.php">Manage Programs</a>
    <?php endif; ?>

    <?php if ($role === 'student'): ?>
      <a href="register_exam.php">Register for Exam</a>
      <a href="view_my_attendance.php">My Attendance</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>

    <!-- Avatar Dropdown -->
    <div class="avatar-dropdown">
      <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar" onclick="toggleDropdown()">
      <div class="dropdown-content" id="avatarDropdown">
        <p><strong><?= htmlspecialchars($name) ?></strong><br>
        <small><?= htmlspecialchars(ucfirst($role)) ?></small></p>
        <a href="upload_avatar.php">Change Avatar</a>
        <a href="login.php">Switch Account</a>
      </div>
    </div>
  </div>
</div>

<style>
.navbar {
  background: #00264d;
  padding: 10px 20px;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.navbar a {
  color: white;
  margin: 0 10px;
  text-decoration: none;
}
.avatar-dropdown {
  position: relative;
  display: inline-block;
}
.avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
}
.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background-color: #003366;
  color: white;
  min-width: 180px;
  border-radius: 6px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.25);
  border: 1px solid #001f3f;
  z-index: 1;
}
.dropdown-content a, .dropdown-content p {
  padding: 10px;
  text-decoration: none;
  display: block;
  color: white;
}
.dropdown-content a:hover {
  background-color: #0059b3;
}
</style>

<script>
function toggleDropdown() {
  const dropdown = document.getElementById('avatarDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}
window.onclick = function(event) {
  if (!event.target.matches('.avatar')) {
    const dropdown = document.getElementById('avatarDropdown');
    if (dropdown) dropdown.style.display = 'none';
  }
}
</script>

<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$current_id = $_SESSION['user_id'];
$current_role = $_SESSION['user_role'];

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Admin actions (Add or Update others)
  if ($current_role == 'admin') {
    $action = $_POST['action'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if ($action == 'add') {
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
      $check->bind_param("s", $email);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        $message = "❌ User with this email already exists.";
      } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
        $message = "✅ New user created successfully.";
      }
    }

    if ($action == 'update') {
      $user_id = $_POST['user_id'];
      $new_name = $_POST['name'];
      $new_role = $_POST['role'];
      $new_password = $_POST['password'];

      if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, role=?, password_hash=? WHERE id=?");
        $stmt->bind_param("sssi", $new_name, $new_role, $hashed, $user_id);
      } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $new_name, $new_role, $user_id);
      }
      $stmt->execute();
      $message = "✅ User updated successfully.";
    }
  }
  // Non-admin: update only own name & password
  else {
    $new_name = trim($_POST['name']);
    $new_password = trim($_POST['password']);
    if (!empty($new_password)) {
      $hashed = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE users SET name=?, password_hash=? WHERE id=?");
      $stmt->bind_param("ssi", $new_name, $hashed, $current_id);
    } else {
      $stmt = $conn->prepare("UPDATE users SET name=? WHERE id=?");
      $stmt->bind_param("si", $new_name, $current_id);
    }
    $stmt->execute();
    $message = "✅ Your account updated.";
    $_SESSION['user_name'] = $new_name;
  }
}

// Fetch all users if admin
if ($current_role == 'admin') {
  $users = $conn->query("SELECT id, name, email, role FROM users");
} else {
  $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
  $stmt->bind_param("i", $current_id);
  $stmt->execute();
  $users = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Modify User</title>
  <style>
    body {
  font-family: Arial;
  background-image: url('assets/school_bg1.jpg');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  background-attachment: fixed;
  margin: 0;
  padding: 20px;
}
    .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
    h2 { text-align: center; }
    .message { color: green; font-weight: bold; }
    form { margin-top: 20px; }
    label { display: block; margin-top: 10px; }
    input, select { width: 100%; padding: 10px; margin-top: 5px; }
    button { padding: 10px 20px; margin-top: 15px; background: #004080; color: white; border: none; border-radius: 5px; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    .form-section { margin-bottom: 40px; background: #f9f9f9; padding: 15px; border-radius: 6px; }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h2>👥 Modify User</h2>

    <?php if ($message): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <?php if ($current_role == 'admin'): ?>
      <!-- Add New User -->
      <div class="form-section">
        <h3>Add New User</h3>
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <label>Name:</label>
          <input type="text" name="name" required>
          <label>Email:</label>
          <input type="email" name="email" required>
          <label>Password:</label>
          <input type="password" name="password" required>
          <label>Role:</label>
          <select name="role" required>
            <option value="admin">Admin</option>
            <option value="lecturer">Lecturer</option>
            <option value="supervisor">Supervisor</option>
            <option value="student">Student</option>
          </select>
          <button type="submit">Add User</button>
        </form>
      </div>

      <!-- Update Existing Users -->
      <div class="form-section">
        <h3>Update Existing Users</h3>
        <table>
          <tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
          <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
              <form method="POST">
                <td>
                  <input type="text" name="name" value="<?= htmlspecialchars($u['name']) ?>" required>
                </td>
                <td><?= $u['email'] ?></td>
                <td>
                  <select name="role" required>
                    <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="lecturer" <?= $u['role'] == 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                    <option value="supervisor" <?= $u['role'] == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                    <option value="student" <?= $u['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                  </select>
                </td>
                <td>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                  <input type="password" name="password" placeholder="New Password (optional)">
                  <button type="submit">Update</button>
                </td>
              </form>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    <?php else: ?>
      <!-- Self-edit for non-admin -->
      <?php $user = $users->fetch_assoc(); ?>
      <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <label>New Password (optional):</label>
        <input type="password" name="password" placeholder="New Password">
        <button type="submit">Update My Info</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>

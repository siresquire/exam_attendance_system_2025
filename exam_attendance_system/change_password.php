<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $old = $_POST['old_password'];
  $new = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($hash);
  $stmt->fetch();
  $stmt->close();

  if (!password_verify($old, $hash)) {
    $message = "❌ Old password is incorrect.";
    $messageClass = "error";
  } elseif ($new !== $confirm) {
    $message = "❌ New passwords do not match.";
    $messageClass = "error";
  } else {
    $newHash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $newHash, $user_id);
    $stmt->execute();
    $message = "✅ Password changed successfully.";
    $messageClass = "success";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Change Password</title>
  <link rel="stylesheet" href="assets/style.css">
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
    .form-box {
      max-width: 500px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-box h2 {
      margin-bottom: 20px;
      color: #004080;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      width: 100%;
      background: #004080;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .success, .error {
      margin-top: 15px;
      padding: 12px;
      border-radius: 6px;
    }
    .success { background: #e0ffe0; color: green; }
    .error { background: #ffe0e0; color: red; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-box">
  <h2>🔒 Change Password</h2>

  <?php if ($message): ?>
    <div class="<?= $messageClass; ?>"><?= $message; ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="password" name="old_password" placeholder="Old Password" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
    <button type="submit">🔁 Update Password</button>
  </form>
</div>

</body>
</html>

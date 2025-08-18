<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {
  $file = $_FILES['avatar'];

  if ($file['error'] === 0 && in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/png'])) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $target = "uploads/" . $filename;
    move_uploaded_file($file['tmp_name'], $target);

    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $target, $user_id);
    $stmt->execute();
    $success = "✅ Avatar updated.";
  } else {
    $error = "❌ Invalid image file.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Avatar</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 30px; }
    .box { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
    input[type="file"] { margin-top: 10px; }
    button { margin-top: 15px; padding: 10px 20px; background: #007bff; color: white; border: none; }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Update Profile Picture</h2>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
      <label>Select image:</label>
      <input type="file" name="avatar" accept="image/*" required>
      <button type="submit">Upload</button>
    </form>
  </div>
</body>
</html>

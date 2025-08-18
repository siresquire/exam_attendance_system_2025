<?php
require_once 'db.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $index_number = trim($_POST['index_number']) ?: '5300' . rand(100000, 999999); // Default index if not filled
  $department = trim($_POST['department']) ?: 'IT'; // Default department

  $avatarPath = null;

  // Handle avatar upload
  if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $avatarName = uniqid() . "." . $ext;
    $avatarPath = "uploads/" . $avatarName;
    move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
  }

  // Check if email already exists
  $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $error = "❌ Email already exists!";
  } else {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, avatar) VALUES (?, ?, ?, 'student', ?)");
    $stmt->bind_param("ssss", $name, $email, $password_hash, $avatarPath);

    if ($stmt->execute()) {
      $user_id = $stmt->insert_id;

      // Insert into students table
      $stmt2 = $conn->prepare("INSERT INTO students (name, index_number, department, user_id) VALUES (?, ?, ?, ?)");
      $stmt2->bind_param("sssi", $name, $index_number, $department, $user_id);
      $stmt2->execute();

      $success = "✅ Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
      $error = "❌ Error: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Registration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #004080, #0066cc);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-box {
      background: white;
      padding: 35px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }
    h2 {
      color: #004080;
      margin-bottom: 20px;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: #003060;
    }
    .success, .error {
      margin-top: 15px;
      padding: 10px;
      border-radius: 6px;
    }
    .success { background: #e0ffe0; color: green; }
    .error { background: #ffe0e0; color: red; }
  </style>
</head>
<body>

<div class="register-box">
  <h2>👤 Student Registration</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>

    <input type="text" name="index_number" placeholder="Index Number" required>
    <input type="text" name="department" placeholder="Department (e.g. IT)" required>

    <label style="display:block; margin-top:10px;">Upload Avatar (optional):</label>
    <input type="file" name="avatar" accept="image/*">

    <button type="submit">Register</button>
  </form>

  <p style="margin-top: 15px;"><a href="login.php">Already have an account? Login</a></p>
</div>

</body>
</html>

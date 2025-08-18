<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Restrict students and guests
if ($_SESSION['user_role'] === 'student' || $_SESSION['user_role'] === 'guest') {
  include 'navbar.php';
  echo "<div style='padding: 40px; font-family: Arial; text-align: center; font-size: 20px; color: red;'>
          ❌ Sorry, you don't have the privileges to do this.
        </div>";
  exit;
}

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $index_number = trim($_POST['index_number']);

  // Check for duplicate index number
  $check = $conn->prepare("SELECT id FROM students WHERE index_number = ?");
  $check->bind_param("s", $index_number);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $error = "❌ Index number already exists.";
  } else {
    $stmt = $conn->prepare("INSERT INTO students (name, index_number) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $index_number);
    if ($stmt->execute()) {
      $success = "✅ Student added successfully.";
    } else {
      $error = "❌ Error: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Student</title>
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
      background: white;
      padding: 30px;
      max-width: 600px;
      margin: auto;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
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
      padding: 10px;
      border-radius: 6px;
    }
    .success { background: #e0ffe0; color: green; }
    .error { background: #ffe0e0; color: red; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-box">
  <h2>➕ Add Student</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success; ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="name">Student Name:</label>
    <input type="text" name="name" required>

    <label for="index_number">Index Number:</label>
    <input type="text" name="index_number" required>

    <button type="submit">➕ Add Student</button>
  </form>
</div>
</body>
</html>

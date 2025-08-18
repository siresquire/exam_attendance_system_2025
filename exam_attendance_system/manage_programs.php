<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
  $faculty = trim($_POST['faculty']);
  $name = trim($_POST['name']);
  $session = trim($_POST['session']);

  if ($faculty && $name && $session) {
    $stmt = $conn->prepare("INSERT INTO programs (faculty, name, session) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $faculty, $name, $session);
    if ($stmt->execute()) {
      $success = "✅ Program added.";
    } else {
      $error = "❌ Error: " . $stmt->error;
    }
  } else {
    $error = "❌ All fields required.";
  }
}

// Handle deletion
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM programs WHERE id = $id");
  header("Location: manage_programs.php");
  exit;
}

// Fetch all programs
$programs = $conn->query("SELECT * FROM programs ORDER BY program_name ASC");

if (!$programs) {
    die("Query Failed: " . $conn->error);
}


include 'navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Programs</title>
  <style>
    body {
      font-family: Arial;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-attachment: fixed;
      padding: 20px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      background: rgba(255,255,255,0.95);
      padding: 25px;
      border-radius: 10px;
    }
    table {
      width: 100%; margin-top: 20px; border-collapse: collapse;
    }
    th, td {
      padding: 10px; border: 1px solid #ccc; text-align: left;
    }
    th {
      background: #004080; color: white;
    }
    .form {
      display: flex; gap: 10px; flex-wrap: wrap;
    }
    .form input, .form select {
      padding: 10px; border-radius: 6px; border: 1px solid #ccc;
      flex: 1;
    }
    .form button {
      padding: 10px 20px; background: #004080; color: white; border: none;
      border-radius: 6px; cursor: pointer;
    }
    .success { background: #e0ffe0; padding: 10px; color: green; margin-top: 10px; }
    .error { background: #ffe0e0; padding: 10px; color: red; margin-top: 10px; }
  </style>
</head>
<body>
<div class="container">
  <h2>📚 Manage Academic Programs</h2>

  <form method="POST" class="form">
    <input type="text" name="faculty" placeholder="Faculty" required>
    <input type="text" name="name" placeholder="Program Name" required>
    <select name="session" required>
      <option value="">-- Select Session --</option>
      <option value="Full-Time">Full-Time</option>
      <option value="Sandwich">Sandwich</option>
      <option value="Evening">Evening</option>
      <option value="Weekend">Weekend</option>
    </select>
    <button type="submit">➕ Add Program</button>
  </form>

  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>

  <table>
    <tr>
      <th>ID</th>
      <th>Faculty</th>
      <th>Program</th>
      <th>Session</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $programs->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['faculty']) ?></td>
      <td><?= htmlspecialchars($row['program_name']); ?></td>
      <td><?= $row['session'] ?></td>
      <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this program?')">❌ Delete</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>

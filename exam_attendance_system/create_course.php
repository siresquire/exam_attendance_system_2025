<?php
session_start();
require_once 'db.php';

// ✅ Only allow Admin or Lecturer
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'lecturer'])) {
    echo "<p style='color:red; text-align:center; font-weight:bold;'>❌ Sorry, you don't have the privileges to do this.</p>";
    exit;
}

$success = "";
$error = "";

// ✅ Fetch programs for dropdown
$programs = $conn->query("SELECT id, program_name, session FROM programs ORDER BY program_name");

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST['code']);
    $title = trim($_POST['title']);
    $year = trim($_POST['year']);
    $semester = trim($_POST['semester']);
    $department = trim($_POST['department']);
    $program_id = intval($_POST['program_id']);

    // Optional: derive course_name same as title
    $course_name = $title;

    // ✅ Default lecturer_id = NULL
    $lecturer_id = null;

    if ($_SESSION['user_role'] === 'lecturer') {
        // Fetch lecturer's ID from lecturers table using user email
        $lookup = $conn->prepare("
            SELECT l.id 
            FROM lecturers l 
            JOIN users u ON u.email = l.email 
            WHERE u.id = ?
        ");
        $lookup->bind_param("i", $_SESSION['user_id']);
        $lookup->execute();
        $lookup->bind_result($lec_id);
        if ($lookup->fetch()) {
            $lecturer_id = $lec_id;
        }
        $lookup->close();
    }

    // ✅ Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO courses (code, title, course_name, year, semester, department, program_id, lecturer_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssii", $code, $title, $course_name, $year, $semester, $department, $program_id, $lecturer_id);

    if ($stmt->execute()) {
        $success = "✅ Course created successfully!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Course</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('assets/school_bg1.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 20px;
    }
    .box {
      max-width: 600px;
      margin: auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #004080;
    }
    label {
      font-weight: bold;
      margin-top: 12px;
      display: block;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      margin-top: 20px;
      padding: 12px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    .success {
      background: #e0ffe0;
      padding: 10px;
      color: green;
      margin-bottom: 10px;
      border-radius: 5px;
    }
    .error {
      background: #ffe0e0;
      padding: 10px;
      color: red;
      margin-bottom: 10px;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="box">
  <h2>📘 Create Course</h2>

  <?php if ($success): ?><div class="success"><?= $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="error"><?= $error; ?></div><?php endif; ?>

  <form method="POST">
    <label>Course Code:</label>
    <input type="text" name="code" required placeholder="e.g. CSC101">

    <label>Course Title:</label>
    <input type="text" name="title" required placeholder="e.g. Introduction to Computer Science">

    <label>Year:</label>
    <input type="text" name="year" required placeholder="e.g. 2025">

    <label>Semester:</label>
    <select name="semester" required>
      <option value="">-- Select Semester --</option>
      <option value="First">First</option>
      <option value="Second">Second</option>
    </select>

    <label>Department:</label>
    <input type="text" name="department" required placeholder="e.g. Computer Science">

    <label>Program:</label>
    <select name="program_id" required>
      <option value="">-- Select Program --</option>
      <?php while ($p = $programs->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>">
          <?= htmlspecialchars($p['program_name']) ?> (<?= $p['session'] ?>)
        </option>
      <?php endwhile; ?>
    </select>

    <button type="submit">Create Course</button>
  </form>
</div>

</body>
</html>

<?php
session_start();
require_once 'db.php';

// ✅ Only students can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

include 'navbar.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// ✅ Get student's ID from students table
$studentQuery = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$studentQuery->bind_param("i", $user_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();

if ($studentRow = $studentResult->fetch_assoc()) {
  $student_id = $studentRow['id'];
} else {
  die("<p style='color:red;'>❌ You are not properly registered as a student. Contact Admin.</p>");
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $exam_id = $_POST['exam_id'];

  // Prevent duplicate registration
  $checkStmt = $conn->prepare("SELECT id FROM exam_registrations WHERE student_id = ? AND exam_id = ?");
  $checkStmt->bind_param("ii", $student_id, $exam_id);
  $checkStmt->execute();
  $checkStmt->store_result();

  if ($checkStmt->num_rows > 0) {
    $error = "❌ You have already registered for this exam.";
  } else {
    // Register the student
    $stmt = $conn->prepare("INSERT INTO exam_registrations (student_id, exam_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $exam_id);
    if ($stmt->execute()) {
      $success = "✅ Exam registered successfully!";
    } else {
      $error = "❌ Registration failed: " . $stmt->error;
    }
  }
}

// ✅ Fetch all available upcoming exams
$exams = $conn->query("
  SELECT e.id, c.title AS course, e.exam_date, e.start_time, e.end_time, e.venue_name, u.name AS lecturer
  FROM exams e
  JOIN courses c ON e.course_id = c.id
  JOIN users u ON e.lecturer_id = u.id
  ORDER BY e.exam_date ASC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register for Exam</title>
  <style>
    body {
      font-family: Arial;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-attachment: fixed;
      margin: 0;
      padding: 20px;
    }
    .box {
      max-width: 600px;
      margin: 50px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #004080;
      text-align: center;
    }
    select, button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .success { color: green; margin-top: 10px; }
    .error { color: red; margin-top: 10px; }
  </style>
</head>
<body>

<div class="box">
  <h2>📝 Register for Upcoming Exam</h2>

  <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
  <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

  <form method="POST">
    <label>Select Exam:</label>
    <select name="exam_id" required>
      <option value="">-- Choose an exam --</option>
      <?php while ($row = $exams->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>">
          <?= htmlspecialchars($row['course']) ?> - <?= $row['exam_date'] ?> 
          (<?= $row['start_time'] ?> - <?= $row['end_time'] ?>) at <?= htmlspecialchars($row['venue_name']) ?> 
          [Lecturer: <?= htmlspecialchars($row['lecturer']) ?>]
        </option>
      <?php endwhile; ?>
    </select>

    <button type="submit">✅ Register</button>
  </form>
</div>

</body>
</html>

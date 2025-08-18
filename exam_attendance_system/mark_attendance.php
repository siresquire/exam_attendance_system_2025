<?php
session_start();
require_once 'db.php';

// Restrict access to only admins and lecturers
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'lecturer'])) {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

include 'navbar.php';

$exams = $conn->query("SELECT e.id, c.name AS course, e.date
                       FROM exams e
                       JOIN courses c ON e.course_id = c.id
                       ORDER BY e.date DESC");

$students = [];
$examDetails = null;
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : null;
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
  $exam_id = (int)$_POST['exam_id'];
  $present_ids = $_POST['present'] ?? [];

  foreach ($present_ids as $student_id) {
    // Insert only if not already marked
    $check = $conn->prepare("SELECT id FROM attendance WHERE exam_id = ? AND student_id = ?");
    $check->bind_param("ii", $exam_id, $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
      $stmt = $conn->prepare("INSERT INTO attendance (exam_id, student_id) VALUES (?, ?)");
      $stmt->bind_param("ii", $exam_id, $student_id);
      $stmt->execute();
    }
  }

  $success = "✅ Attendance marked successfully.";
}

// Fetch exam info & registered students
if ($exam_id) {
  $stmt = $conn->prepare("SELECT e.*, c.name AS course FROM exams e
                          JOIN courses c ON e.course_id = c.id WHERE e.id = ?");
  $stmt->bind_param("i", $exam_id);
  $stmt->execute();
  $examDetails = $stmt->get_result()->fetch_assoc();

  // Fetch students who registered
  $stmt = $conn->prepare("SELECT s.id, s.name, s.index_number,
         (SELECT COUNT(*) FROM attendance a WHERE a.exam_id = ? AND a.student_id = s.id) AS is_present
         FROM exam_registrations r
         JOIN students s ON r.student_id = s.id
         WHERE r.exam_id = ?");
  $stmt->bind_param("ii", $exam_id, $exam_id);
  $stmt->execute();
  $students = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mark Attendance</title>
  <style>
    body {
      font-family: Arial;
      background: url('assets/school_bg1.jpg') no-repeat center center fixed;
      background-size: cover;
      padding: 20px;
    }
    .container {
      background: rgba(255,255,255,0.95);
      padding: 25px;
      max-width: 900px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    h2 { text-align: center; color: #004080; }
    select, button { padding: 10px; width: 100%; margin-top: 10px; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    th { background-color: #004080; color: white; }
    .success, .error {
      margin-top: 15px;
      text-align: center;
      font-weight: bold;
      padding: 10px;
      border-radius: 5px;
    }
    .success { background: #e0ffe0; color: green; }
    .error { background: #ffe0e0; color: red; }
  </style>
</head>
<body>

<div class="container">
  <h2>📋 Mark Exam Attendance</h2>

  <?php if ($success) echo "<div class='success'>$success</div>"; ?>
  <?php if ($error) echo "<div class='error'>$error</div>"; ?>

  <form method="GET">
    <label>Select Exam:</label>
    <select name="exam_id" onchange="this.form.submit()" required>
      <option value="">-- Choose Exam --</option>
      <?php while ($exam = $exams->fetch_assoc()): ?>
        <option value="<?= $exam['id']; ?>" <?= ($exam['id'] == $exam_id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($exam['course']) . " - " . $exam['date'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <?php if ($examDetails): ?>
    <p><strong>Course:</strong> <?= htmlspecialchars($examDetails['course']) ?></p>
    <p><strong>Date:</strong> <?= $examDetails['date'] ?> | 
       <strong>Time:</strong> <?= $examDetails['start_time'] ?> - <?= $examDetails['end_time'] ?></p>
    <p><strong>Venue:</strong> <?= htmlspecialchars($examDetails['venue_name']) ?></p>
    <p><strong>Lecturer:</strong> <?= htmlspecialchars($examDetails['lecturer_name']) ?></p>
    <p><strong>Semester:</strong> <?= htmlspecialchars($examDetails['semester']) ?></p>

    <form method="POST">
      <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
      <table>
        <thead>
          <tr><th>Index No.</th><th>Name</th><th>Status</th><th>Mark Present</th></tr>
        </thead>
        <tbody>
          <?php if ($students->num_rows > 0): ?>
            <?php while ($row = $students->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['index_number']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['is_present'] ? '✅ Present' : '❌ Not Marked' ?></td>
                <td>
                  <?php if (!$row['is_present']): ?>
                    <input type="checkbox" name="present[]" value="<?= $row['id'] ?>">
                  <?php else: ?>
                    <span style="color:green;">✔</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">No registered students found for this exam.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <button type="submit" name="mark_attendance">✅ Save Attendance</button>
    </form>
  <?php endif; ?>
</div>

</body>
</html>

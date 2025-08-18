<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id || !is_numeric($exam_id)) {
  die("❌ No valid exam selected.");
}

// Fetch exam details
$examStmt = $conn->prepare("
  SELECT c.name AS course, e.date, e.start_time, e.end_time, v.name AS venue, u.name AS lecturer
  FROM exams e
  JOIN courses c ON e.course_id = c.id
  JOIN venues v ON e.venue_id = v.id
  JOIN users u ON e.lecturer_id = u.id
  WHERE e.id = ?
");
$examStmt->bind_param("i", $exam_id);
$examStmt->execute();
$exam = $examStmt->get_result()->fetch_assoc();
$examStmt->close();

if (!$exam) {
  die("❌ Exam not found.");
}

// Fetch attendance
$studentsStmt = $conn->prepare("
  SELECT s.index_number, s.name
  FROM attendance a
  JOIN students s ON a.student_id = s.id
  WHERE a.exam_id = ?
");
$studentsStmt->bind_param("i", $exam_id);
$studentsStmt->execute();
$students = $studentsStmt->get_result();

// Fetch supervisors
$supervisorsStmt = $conn->prepare("
  SELECT u.name
  FROM exam_supervisors es
  JOIN users u ON es.supervisor_id = u.id
  WHERE es.exam_id = ?
");
$supervisorsStmt->bind_param("i", $exam_id);
$supervisorsStmt->execute();
$supervisorsResult = $supervisorsStmt->get_result();
$supervisorNames = [];
while ($row = $supervisorsResult->fetch_assoc()) {
  $supervisorNames[] = $row['name'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Print Attendance</title>
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
    .logo-area {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo-area img {
      width: 80px;
    }
    .logo-area h2 {
      margin: 5px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }
    th, td {
      border: 1px solid black;
      padding: 10px;
      text-align: left;
    }
    .btn-print {
      display: inline-block;
      background: #333;
      color: white;
      padding: 10px 20px;
      margin-bottom: 20px;
      text-decoration: none;
      border-radius: 5px;
    }
    @media print {
      .btn-print {
        display: none;
      }
    }
  </style>
</head>
<body>

  <div class="logo-area">
    <img src="assets/logo.png" alt="Logo"><br>
    <h2>Your School Name</h2>
  </div>

  <a href="#" class="btn-print" onclick="window.print()">🖨️ Print This Page</a>

  <h3>Exam Attendance</h3>
  <p><strong>Course:</strong> <?= htmlspecialchars($exam['course']) ?></p>
  <p><strong>Date:</strong> <?= $exam['date'] ?>,
     <strong>Time:</strong> <?= $exam['start_time'] ?> - <?= $exam['end_time'] ?></p>
  <p><strong>Venue:</strong> <?= htmlspecialchars($exam['venue']) ?></p>
  <p><strong>Lecturer:</strong> <?= htmlspecialchars($exam['lecturer']) ?></p>
  <p><strong>Supervisors:</strong> <?= $supervisorNames ? implode(", ", $supervisorNames) : "None" ?></p>

  <table>
    <tr><th>Index Number</th><th>Name</th><th>Signature</th></tr>
    <?php if ($students->num_rows > 0): ?>
      <?php while ($s = $students->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($s['index_number']) ?></td>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="3">No students marked present for this exam.</td></tr>
    <?php endif; ?>
  </table>

</body>
</html>

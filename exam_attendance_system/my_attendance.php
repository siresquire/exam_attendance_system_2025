<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

include 'navbar.php';

$student_user_id = $_SESSION['user_id'];

// Get student.id using user_id
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $student_user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ You are not properly registered as a student.</p>";
  exit;
}

$student_id = $student['id'];

// Fetch exam registrations and attendance status
$sql = "
  SELECT 
    c.name AS course,
    e.date,
    e.start_time,
    e.end_time,
    e.venue_name AS venue,
    e.lecturer_name AS lecturer,
    e.id AS exam_id,
    (SELECT COUNT(*) FROM attendance a WHERE a.exam_id = e.id AND a.student_id = ?) AS is_present
  FROM exam_registrations r
  JOIN exams e ON r.exam_id = e.id
  JOIN courses c ON e.course_id = c.id
  WHERE r.student_id = ?
  ORDER BY e.date DESC
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("ii", $student_id, $student_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Attendance</title>
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
    .box {
      background: rgba(255,255,255,0.95);
      padding: 30px;
      max-width: 1000px;
      margin: 50px auto;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #004080;
      text-align: center;
    }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #004080;
      color: white;
    }
    .present {
      color: green;
      font-weight: bold;
    }
    .absent {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
<div class="box">
  <h2>👁️ My Attendance</h2>

  <?php if ($result2->num_rows > 0): ?>
    <table>
      <tr>
        <th>Course</th>
        <th>Date</th>
        <th>Time</th>
        <th>Venue</th>
        <th>Lecturer</th>
        <th>Status</th>
      </tr>
      <?php while ($row = $result2->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['course']) ?></td>
          <td><?= $row['date'] ?></td>
          <td><?= $row['start_time'] ?> - <?= $row['end_time'] ?></td>
          <td><?= htmlspecialchars($row['venue']) ?></td>
          <td><?= htmlspecialchars($row['lecturer']) ?></td>
          <td class="<?= $row['is_present'] ? 'present' : 'absent' ?>">
            <?= $row['is_present'] ? '✅ Present' : '❌ Not Marked' ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p style="text-align:center;">No exam registrations yet.</p>
  <?php endif; ?>
</div>
</body>
</html>

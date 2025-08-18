<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

$student_user_id = $_SESSION['user_id'];
date_default_timezone_set('Africa/Accra');
$today = date('Y-m-d');
$currentTime = date('H:i:s');

// Get student ID from students table
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $student_user_id);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();

if (!$student) {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Student record not found.</p>";
  exit;
}

$student_id = $student['id'];

// Fetch today's exam(s) registered by the student
$sql = "
SELECT 
  e.id AS exam_id, 
  c.course_name AS course_name,
  e.exam_date, e.start_time, e.end_time,
  e.venue_name,
  u.name AS lecturer_name,
  (
    SELECT COUNT(*) FROM attendance 
    WHERE exam_id = e.id AND student_id = ?
  ) AS already_present
FROM exam_registrations r
JOIN exams e ON r.exam_id = e.id
JOIN courses c ON e.course_id = c.id
JOIN users u ON e.lecturer_id = u.id
WHERE r.student_id = ? AND e.exam_date = ?
ORDER BY e.start_time ASC
";



$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $student_id, $student_id, $today);
$stmt->execute();
$result = $stmt->get_result();

include 'navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Today's Exams - Mark Attendance</title>
  <style>
    body {
      font-family: Arial;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: rgba(255,255,255,0.95);
      padding: 30px;
      border-radius: 12px;
    }

    h2 {
      color: #004080;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background: #004080;
      color: white;
    }

    .present {
      color: green;
      font-weight: bold;
    }

    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      background: #004080;
      color: white;
      cursor: pointer;
    }

    .btn:disabled {
      background: gray;
      cursor: not-allowed;
    }

    .message {
      text-align: center;
      margin-top: 20px;
      color: red;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>🕐 Today's Exams & Self Check-In</h2>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <tr>
        <th>Course</th>
        <th>Time</th>
        <th>Venue</th>
        <th>Lecturer</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()):
        $start = $row['start_time'];
        $end = $row['end_time'];
        $exam_id = $row['exam_id'];

        // Allow check-in 30 mins before start till exam end
        $checkin_start = date('H:i:s', strtotime($start . ' -30 minutes'));
        $is_within_window = ($currentTime >= $checkin_start && $currentTime <= $end);
        $already_present = $row['already_present'] > 0;
      ?>
        <tr>
          <td><?= htmlspecialchars($row['course_name']) ?></td>
          <td><?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?></td>
          <td><?= htmlspecialchars($row['venue_name']) ?></td>
          <td><?= htmlspecialchars($row['lecturer_name']) ?></td>
          <td class="<?= $already_present ? 'present' : '' ?>">
            <?= $already_present ? '✅ Present' : '❌ Not Marked' ?>
          </td>
          <td>
            <?php if (!$already_present && $is_within_window): ?>
              <form method="POST" action="mark_seated.php">
                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                <button class="btn" type="submit">Mark Me Present</button>
              </form>
            <?php elseif ($already_present): ?>
              <button class="btn" disabled>Already Marked</button>
            <?php else: ?>
              <button class="btn" disabled>Check-In Closed</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="message">😔 You have no exams scheduled for today.</div>
  <?php endif; ?>
</div>

</body>
</html>

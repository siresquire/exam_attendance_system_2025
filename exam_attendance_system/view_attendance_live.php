<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'lecturer'])) {
  echo "<p style='color:red; text-align:center;'>❌ Access Denied</p>";
  exit;
}

include 'navbar.php';

// ✅ Use exam_date instead of date, and title instead of course_name
$exams = $conn->query("
  SELECT exams.id, courses.title AS course, exams.exam_date, exams.start_time, exams.end_time 
  FROM exams 
  JOIN courses ON exams.course_id = courses.id 
  ORDER BY exams.exam_date DESC
");

$selected_exam_id = $_GET['exam_id'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Live Attendance View</title>
  <style>
    body {
      font-family: Arial;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 50px auto;
      background: rgba(255,255,255,0.95);
      padding: 30px;
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      color: #004080;
    }

    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background: #004080;
      color: white;
    }

    .timestamp {
      font-size: 13px;
      color: green;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📡 Live Attendance Monitoring</h2>

  <form method="GET" action="">
    <label for="exam_id">Select Exam:</label>
    <select name="exam_id" id="exam_id" onchange="this.form.submit()">
      <option value="">-- Choose Exam --</option>
      <?php while ($exam = $exams->fetch_assoc()): ?>
        <option value="<?= $exam['id'] ?>" <?= $selected_exam_id == $exam['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($exam['course']) ?> (<?= $exam['exam_date'] ?> <?= $exam['start_time'] ?>)
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <div id="attendance-section">
    <?php if ($selected_exam_id): ?>
      <div>⏳ Loading student attendance...</div>
    <?php endif; ?>
  </div>
</div>

<script>
  function fetchAttendance() {
    const examId = '<?= $selected_exam_id ?>';
    if (!examId) return;

    fetch(`fetch_attendance_live.php?exam_id=${examId}`)
      .then(response => response.text())
      .then(data => {
        document.getElementById('attendance-section').innerHTML = data;
      });
  }

  setInterval(fetchAttendance, 10000); // every 10 seconds
  window.onload = fetchAttendance;
</script>

</body>
</html>

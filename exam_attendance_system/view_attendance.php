<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'navbar.php';

// ✅ Get all exams for dropdown
$exams = $conn->query("
  SELECT exams.id, courses.title AS course_name, exams.exam_date 
  FROM exams 
  JOIN courses ON exams.course_id = courses.id
  ORDER BY exams.exam_date DESC
");

// Initialize variables
$attendance = [];
$examDetails = null;
$exam_id = null;

if (isset($_GET['exam_id']) && is_numeric($_GET['exam_id'])) {
  $exam_id = (int) $_GET['exam_id'];

  // ✅ Get exam details (use exam_date + title)
  $stmt = $conn->prepare("
    SELECT c.title AS course, e.exam_date, e.start_time, e.end_time, e.venue_name AS venue, u.name AS lecturer
    FROM exams e
    JOIN courses c ON e.course_id = c.id
    JOIN users u ON e.lecturer_id = u.id
    WHERE e.id = ?
  ");
  $stmt->bind_param("i", $exam_id);
  $stmt->execute();
  $examDetails = $stmt->get_result()->fetch_assoc();

  // ✅ Get attendance list
  $stmt = $conn->prepare("
    SELECT s.index_number, s.name 
    FROM attendance a 
    JOIN students s ON a.student_id = s.id 
    WHERE a.exam_id = ?
  ");
  $stmt->bind_param("i", $exam_id);
  $stmt->execute();
  $attendance = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Attendance</title>
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
      background: white;
      padding: 25px;
      max-width: 900px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2, h3 { text-align: center; }
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
    th {
      background-color: #004080;
      color: white;
    }
    select {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
    }
    .btn-group {
      margin-top: 15px;
      display: flex;
      gap: 15px;
    }
    .btn-group a {
      padding: 10px 15px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      color: white;
    }
    .btn-pdf { background-color: #c0392b; }
    .btn-excel { background-color: #27ae60; }
    .btn-print { background-color: #2980b9; }
  </style>
</head>
<body>

<div class="box">
  <h2>📊 View Exam Attendance</h2>

  <!-- Exam Selection -->
  <form method="GET">
    <label>Select Exam:</label>
    <select name="exam_id" onchange="this.form.submit()" required>
      <option value="">-- Choose an Exam --</option>
      <?php while ($exam = $exams->fetch_assoc()): ?>
        <option value="<?= $exam['id']; ?>" <?= ($exam_id == $exam['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($exam['course_name'] . " - " . $exam['exam_date']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <?php if ($examDetails): ?>
    <!-- Exam Info -->
    <h3>📝 Exam Information</h3>
    <p><strong>Course:</strong> <?= htmlspecialchars($examDetails['course']) ?></p>
    <p><strong>Date:</strong> <?= $examDetails['exam_date'] ?>,
       <strong>Time:</strong> <?= $examDetails['start_time'] ?> - <?= $examDetails['end_time'] ?></p>
    <p><strong>Venue:</strong> <?= htmlspecialchars($examDetails['venue']) ?></p>
    <p><strong>Lecturer:</strong> <?= htmlspecialchars($examDetails['lecturer']) ?></p>

    <!-- Export Options -->
    <div class="btn-group">
      <a href="export_pdf.php?exam_id=<?= $exam_id ?>" class="btn-pdf">📄 Export PDF</a>
      <a href="#" onclick="window.print();" class="btn-print">🖨️ Print</a>
      <a href="export_excel.php?exam_id=<?= $exam_id ?>" class="btn-excel">📊 Export Excel</a>
    </div>

    <!-- Attendance List -->
    <h3 style="margin-top: 30px;">✅ Attendance List</h3>
    <table>
      <tr><th>Index Number</th><th>Name</th></tr>
      <?php if ($attendance->num_rows > 0): ?>
        <?php while ($row = $attendance->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['index_number']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="2">No students have been marked present yet.</td></tr>
      <?php endif; ?>
    </table>
  <?php endif; ?>
</div>

</body>
</html>

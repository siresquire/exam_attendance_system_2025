<?php
session_start();
require_once 'db.php';

// ✅ Only allow access to students
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
    exit;
}

// ✅ Get the correct student ID from students table
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();

if (!$student) {
    echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ You are not properly registered as a student.</p>";
    exit;
}

$student_id = $student['id'];

include 'navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>📄 My Exam Report</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-attachment: fixed;
      transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
      background-color: #121212;
      background-image: none;
      color: white;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    body.dark-mode .container {
      background: rgba(30, 30, 30, 0.95);
    }
    h2 { text-align: center; color: #004080; }
    table { width: 100%; border-collapse: collapse; margin-top: 25px; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background: #004080; color: white; }
    body.dark-mode th { background: #333; color: #aad8ff; }
    body.dark-mode td { background: #1e1e1e; color: #eee; }
    .present { color: green; font-weight: bold; }
    .absent { color: red; font-weight: bold; }
    .toggle-theme { margin-top: 15px; display: block; text-align: right; }
    .toggle-theme button {
      padding: 10px 20px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .toggle-theme button:hover { background: #003060; }
  </style>
</head>
<body>

<div class="container">
  <div class="toggle-theme">
    <button onclick="toggleTheme()">🌓 Toggle Theme</button>
  </div>

  <h2>📄 My Exam Report</h2>

  <table>
    <tr>
      <th>Course</th>
      <th>Date</th>
      <th>Time</th>
      <th>Venue</th>
      <th>Lecturer</th>
      <th>Status</th>
    </tr>
    <?php
      $sql = "
        SELECT c.course_name, e.exam_date AS date, e.start_time, e.end_time, e.venue_name, u.name AS lecturer,
               e.id AS exam_id,
               (SELECT COUNT(*) FROM attendance a WHERE a.student_id = ? AND a.exam_id = e.id) AS attended
        FROM exam_registrations r
        JOIN exams e ON r.exam_id = e.id
        JOIN courses c ON e.course_id = c.id
        LEFT JOIN users u ON e.lecturer_id = u.id
        WHERE r.student_id = ?
        ORDER BY e.exam_date DESC
      ";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ii", $student_id, $student_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
          echo "<tr><td colspan='6'>No registered exams found.</td></tr>";
      }

      while ($row = $result->fetch_assoc()) {
          $status = $row['attended'] ? "<span class='present'>✅ Present</span>" : "<span class='absent'>❌ Not Marked</span>";
          echo "<tr>
                  <td>{$row['course_name']}</td>
                  <td>".date('d-m-Y', strtotime($row['date']))."</td>
                  <td>{$row['start_time']} - {$row['end_time']}</td>
                  <td>{$row['venue_name']}</td>
                  <td>{$row['lecturer']}</td>
                  <td>$status</td>
                </tr>";
      }
    ?>
  </table>
</div>

<script>
  function toggleTheme() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
  }

  window.onload = () => {
    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark-mode");
    }
  };
</script>

</body>
</html>

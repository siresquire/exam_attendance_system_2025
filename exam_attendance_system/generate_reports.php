<?php
session_start();
require_once 'db.php';

// ✅ Only admin and lecturer can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'lecturer'])) {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
  exit;
}

include 'navbar.php';

$department = $_GET['department'] ?? '';
$course_id = $_GET['course_id'] ?? '';
$venue = $_GET['venue'] ?? '';

$where = "1=1";
$params = [];
$types = "";

if ($department !== '') {
  $where .= " AND s.department = ?";
  $params[] = $department;
  $types .= "s";
}
if ($course_id !== '') {
  $where .= " AND c.id = ?";
  $params[] = $course_id;
  $types .= "i";
}
if ($venue !== '') {
  $where .= " AND e.venue_name = ?";
  $params[] = $venue;
  $types .= "s";
}

// ✅ Fixed: use e.exam_date not e.date, and course title instead of name
$sql = "
SELECT 
  s.name AS student_name,
  s.index_number,
  s.department,
  c.title AS course_name,
  e.venue_name,
  e.exam_date,
  e.start_time,
  e.end_time,
  CASE 
    WHEN a.id IS NOT NULL THEN 'Present'
    ELSE 'Absent'
  END AS attendance_status
FROM exam_registrations r
JOIN students s ON r.student_id = s.id
JOIN exams e ON r.exam_id = e.id
JOIN courses c ON e.course_id = c.id
LEFT JOIN attendance a ON a.exam_id = e.id AND a.student_id = s.id
WHERE $where
ORDER BY e.exam_date DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Generate Reports</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center;
      margin: 0;
      padding: 0;
      transition: background 0.3s, color 0.3s;
    }

    body.dark-mode {
      background: #121212;
      background-image: none;
      color: white;
    }

    .container {
      background: rgba(255, 255, 255, 0.97);
      margin: 50px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      max-width: 1200px;
    }

    body.dark-mode .container {
      background: rgba(30, 30, 30, 0.94);
    }

    h2 {
      text-align: center;
      color: #004080;
    }

    body.dark-mode h2 {
      color: #aad8ff;
    }

    .top-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .theme-toggle {
      padding: 8px 16px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .theme-toggle:hover {
      background: #00264d;
    }

    form {
      text-align: center;
      margin-bottom: 25px;
    }

    select, button {
      padding: 10px;
      margin: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
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

    body.dark-mode th {
      background-color: #1a1a1a;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="top-controls">
    <h2>📄 Exam Attendance Reports</h2>
    <button class="theme-toggle" onclick="toggleTheme()">🌓 Toggle Theme</button>
  </div>

  <form method="GET">
    <select name="department">
      <option value="">-- Department --</option>
      <option value="IT" <?= $department === 'IT' ? 'selected' : '' ?>>IT</option>
      <option value="Business" <?= $department === 'Business' ? 'selected' : '' ?>>Business</option>
      <option value="Engineering" <?= $department === 'Engineering' ? 'selected' : '' ?>>Engineering</option>
    </select>

    <select name="course_id">
      <option value="">-- Course --</option>
      <?php
      $courses = $conn->query("SELECT id, title FROM courses");
      while ($course = $courses->fetch_assoc()):
      ?>
        <option value="<?= $course['id'] ?>" <?= $course_id == $course['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($course['title']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <select name="venue">
      <option value="">-- Venue --</option>
      <?php
      $venues = $conn->query("SELECT DISTINCT venue_name FROM exams WHERE venue_name IS NOT NULL");
      while ($v = $venues->fetch_assoc()):
      ?>
        <option value="<?= $v['venue_name'] ?>" <?= $venue == $v['venue_name'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($v['venue_name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <button type="submit">🔍 Filter</button>
  </form>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <tr>
        <th>Student Name</th>
        <th>Index Number</th>
        <th>Department</th>
        <th>Course</th>
        <th>Venue</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['student_name']) ?></td>
          <td><?= htmlspecialchars($row['index_number']) ?></td>
          <td><?= htmlspecialchars($row['department']) ?></td>
          <td><?= htmlspecialchars($row['course_name']) ?></td>
          <td><?= htmlspecialchars($row['venue_name']) ?></td>
          <td><?= $row['exam_date'] ?></td>
          <td><?= $row['start_time'] ?> - <?= $row['end_time'] ?></td>
          <td class="<?= $row['attendance_status'] === 'Present' ? 'present' : 'absent' ?>">
            <?= $row['attendance_status'] === 'Present' ? '✅ Present' : '❌ Absent' ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p style="text-align:center;">No records found based on your filters.</p>
  <?php endif; ?>
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

<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include 'navbar.php';

// ✅ Handle selected filters from GET
$selectedYear = $_GET['year'] ?? '';
$selectedSemester = $_GET['semester'] ?? '';

// ✅ Fetch distinct years from exams (use exam_date)
$yearResult = $conn->query("SELECT DISTINCT YEAR(exam_date) AS year FROM exams ORDER BY year DESC");
$years = [];
while ($row = $yearResult->fetch_assoc()) {
  $years[] = $row['year'];
}

// ✅ Prepare base SQL
$sql = "
SELECT e.id, c.title AS course_name, e.exam_date, e.start_time, e.end_time, e.venue_name, u.name AS lecturer_name
FROM exams e
JOIN courses c ON e.course_id = c.id
JOIN users u ON e.lecturer_id = u.id
WHERE 1
";

// ✅ Apply filters dynamically
$params = [];
if ($selectedYear) {
  $sql .= " AND YEAR(e.exam_date) = ?";
  $params[] = $selectedYear;
}

if ($selectedSemester) {
  // Assuming Semester 1 = Jan-Jun, Semester 2 = Jul-Dec
  if ($selectedSemester == "1") {
    $sql .= " AND MONTH(e.exam_date) BETWEEN 1 AND 6";
  } elseif ($selectedSemester == "2") {
    $sql .= " AND MONTH(e.exam_date) BETWEEN 7 AND 12";
  }
}

$sql .= " ORDER BY e.exam_date DESC";
$stmt = $conn->prepare($sql);

// ✅ Bind parameters if any
if (!empty($params)) {
  $types = str_repeat("i", count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$exams = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>All Exams</title>
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
      max-width: 950px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #004080;
      color: white;
    }
    tr:nth-child(even) { background: #f9f9f9; }

    a.button {
      padding: 6px 12px;
      text-decoration: none;
      color: white;
      background: #0072ff;
      border-radius: 4px;
      margin-right: 5px;
    }
    a.button:hover { background: #0056cc; }

    .filter-form {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
      align-items: center;
    }
    .filter-form select {
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .filter-form button {
      padding: 8px 14px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .filter-form button:hover {
      background: #002b66;
    }
  </style>
</head>
<body>

<div class="box">
  <h2>📋 All Created Exams</h2>

  <!-- Filter Form -->
  <form method="GET" class="filter-form">
    <label>Year:</label>
    <select name="year">
      <option value="">All Years</option>
      <?php foreach ($years as $year): ?>
        <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>><?= $year ?></option>
      <?php endforeach; ?>
    </select>

    <label>Semester:</label>
    <select name="semester">
      <option value="">All Semesters</option>
      <option value="1" <?= $selectedSemester === "1" ? 'selected' : '' ?>>Semester 1</option>
      <option value="2" <?= $selectedSemester === "2" ? 'selected' : '' ?>>Semester 2</option>
    </select>

    <button type="submit">🔍 Filter</button>
  </form>

  <table>
    <tr>
      <th>#</th>
      <th>Course</th>
      <th>Date</th>
      <th>Time</th>
      <th>Venue</th>
      <th>Actions</th>
    </tr>
    <?php if ($exams->num_rows > 0): ?>
      <?php $sn = 1; while ($row = $exams->fetch_assoc()): ?>
        <tr>
          <td><?= $sn++; ?></td>
          <td><?= htmlspecialchars($row['course_name']); ?></td>
          <td><?= htmlspecialchars($row['exam_date']); ?></td>
          <td><?= htmlspecialchars($row['start_time']) . " - " . htmlspecialchars($row['end_time']); ?></td>
          <td><?= htmlspecialchars($row['venue_name']); ?></td>
          <td>
            <a class="button" href="view_attendance.php?exam_id=<?= $row['id']; ?>">View Attendance</a>
            <a class="button" href="export_pdf.php?exam_id=<?= $row['id']; ?>" style="background: red;">Export PDF</a>
            <a class="button" href="export_excel.php?exam_id=<?= $row['id']; ?>" style="background: green;">Export Excel</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No exams found for the selected filters.</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>

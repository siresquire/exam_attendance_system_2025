<?php
session_start();
require_once 'db.php';

// ✅ Restrict access to only admin or lecturer
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'lecturer'])) {
    echo "<p style='color:red; text-align:center; font-weight:bold;'>❌ Access denied.</p>";
    exit;
}

include 'navbar.php';

$success = "";
$error = "";

// ✅ Fix: use exam_date and courses.title
$exams = $conn->query("
    SELECT e.id, c.title AS course, e.exam_date 
    FROM exams e 
    JOIN courses c ON e.course_id = c.id 
    ORDER BY e.exam_date DESC
");

$students = [];
$selected_exam_id = $_GET['exam_id'] ?? null;

if ($selected_exam_id) {
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.name, s.index_number,
            (SELECT COUNT(*) FROM attendance WHERE student_id = s.id AND exam_id = ?) AS is_present
        FROM exam_registrations r
        JOIN students s ON r.student_id = s.id
        WHERE r.exam_id = ?
    ");
    $stmt->bind_param("ii", $selected_exam_id, $selected_exam_id);
    $stmt->execute();
    $students = $stmt->get_result();
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mark_attendance'])) {
    $exam_id = $_POST['exam_id'];
    $marked_students = $_POST['student_ids'] ?? [];

    foreach ($marked_students as $student_id) {
        $stmt = $conn->prepare("SELECT id FROM attendance WHERE exam_id = ? AND student_id = ?");
        $stmt->bind_param("ii", $exam_id, $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO attendance (exam_id, student_id, timestamp) VALUES (?, ?, NOW())");
            $insert->bind_param("ii", $exam_id, $student_id);
            $insert->execute();
        }
    }

    $success = "✅ Attendance marked successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mark Attendance</title>
  <style>
    body {
      font-family: Arial;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-repeat: no-repeat;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      background: rgba(255,255,255,0.96);
      padding: 30px;
      border-radius: 10px;
    }
    h2 {
      text-align: center;
      color: #004080;
    }
    form {
      margin-top: 20px;
    }
    select, button {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      width: 100%;
      margin-top: 10px;
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
    .btn-submit {
      background: #004080;
      color: white;
      border: none;
      margin-top: 15px;
      padding: 12px;
      cursor: pointer;
    }
    .btn-submit:hover {
      background: #002b66;
    }
    .success {
      background: #e0ffe0;
      padding: 10px;
      margin-top: 15px;
      border-radius: 6px;
      color: green;
    }
    .error {
      background: #ffe0e0;
      padding: 10px;
      margin-top: 15px;
      border-radius: 6px;
      color: red;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📋 Mark Attendance</h2>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>

  <form method="GET">
    <label>Select Exam:</label>
    <select name="exam_id" onchange="this.form.submit()" required>
      <option value="">-- Choose Exam --</option>
      <?php while ($row = $exams->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>" <?= $selected_exam_id == $row['id'] ? 'selected' : '' ?>>
          <?= $row['course'] ?> - <?= $row['exam_date'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <?php if ($selected_exam_id && $students && $students->num_rows > 0): ?>
    <form method="POST">
      <input type="hidden" name="exam_id" value="<?= $selected_exam_id ?>">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Index Number</th>
            <th>Status</th>
            <th>Mark Present</th>
          </tr>
        </thead>
        <tbody>
          <?php $sn = 1; while ($row = $students->fetch_assoc()): ?>
            <tr>
              <td><?= $sn++ ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['index_number']) ?></td>
              <td style="color: <?= $row['is_present'] ? 'green' : 'red' ?>;">
                <?= $row['is_present'] ? '✅ Present' : '❌ Absent' ?>
              </td>
              <td>
                <?php if (!$row['is_present']): ?>
                  <input type="checkbox" name="student_ids[]" value="<?= $row['student_id'] ?>">
                <?php else: ?>
                  <input type="checkbox" disabled checked>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <button type="submit" class="btn-submit" name="mark_attendance">✔ Mark Attendance</button>
    </form>
  <?php elseif ($selected_exam_id): ?>
    <div class="error">No registered students found for this exam.</div>
  <?php endif; ?>
</div>

</body>
</html>

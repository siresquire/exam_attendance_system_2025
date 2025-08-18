<?php
session_start();
require_once 'db.php';

// ✅ Only allow Admin or Exam Officer
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin','lecturer'])) {
  echo "<p style='color:red; text-align:center; font-weight:bold;'>❌ Sorry, you don't have the privileges to do this.</p>";
  exit;
}

$success = "";
$error = "";

// ✅ Fetch courses for dropdown
$courses = $conn->query("SELECT id, code, title FROM courses ORDER BY code");

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $exam_date = isset($_POST['exam_date']) ? $_POST['exam_date'] : null;
    $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : null;
    $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : null;
    $venue_name = isset($_POST['venue_name']) ? trim($_POST['venue_name']) : null;
    $lecturer_name = isset($_POST['lecturer_name']) ? trim($_POST['lecturer_name']) : null;
    $semester = isset($_POST['semester']) ? $_POST['semester'] : null;
    $lecturer_id = $_SESSION['user_id'];

    if ($course_id && $exam_date && $start_time && $end_time && $venue_name && $lecturer_name && $semester) {
        // ✅ Insert into DB
        $stmt = $conn->prepare("
            INSERT INTO exams (course_id, exam_date, start_time, end_time, venue_name, lecturer_name, semester, lecturer_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssssi", $course_id, $exam_date, $start_time, $end_time, $venue_name, $lecturer_name, $semester, $lecturer_id);

        if ($stmt->execute()) {
            $success = "✅ Exam created successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    } else {
        $error = "❌ Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Create Exam</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
    .box { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
    h2 { text-align: center; color: #004080; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input, select { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    button { width: 100%; margin-top: 20px; padding: 12px; background: #004080; color: white; border: none; border-radius: 6px; cursor: pointer; }
    .success { background: #e0ffe0; padding: 10px; color: green; margin-bottom: 10px; border-radius: 5px; }
    .error { background: #ffe0e0; padding: 10px; color: red; margin-bottom: 10px; border-radius: 5px; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="box">
  <h2>📘 Create Exam</h2>

  <?php if ($success): ?><div class="success"><?= $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="error"><?= $error; ?></div><?php endif; ?>

  <form method="POST">
    <label>Course:</label>
    <select name="course_id" required>
      <option value="">-- Select Course --</option>
      <?php while ($c = $courses->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>"><?= $c['code'] ?> - <?= htmlspecialchars($c['title']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Date:</label>
    <input type="date" name="exam_date" required> <!-- ✅ fixed -->

    <label>Start Time:</label>
    <input type="time" name="start_time" required>

    <label>End Time:</label>
    <input type="time" name="end_time" required>

    <label>Venue Name:</label>
    <input type="text" name="venue_name" required placeholder="e.g. Main Hall">

    <label>Lecturer Name:</label>
    <input type="text" name="lecturer_name" required placeholder="e.g. Dr. John Doe">

    <label>Semester:</label>
    <select name="semester" required>
      <option value="">-- Select Semester --</option>
      <option value="First">First</option>
      <option value="Second">Second</option>
    </select>

    <button type="submit">Create Exam</button>
  </form>
</div>

</body>
</html>

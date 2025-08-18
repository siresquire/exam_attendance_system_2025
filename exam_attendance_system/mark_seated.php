<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$student_user_id = $_SESSION['user_id'];

if (!isset($_POST['exam_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Exam ID missing']);
  exit;
}

$exam_id = (int) $_POST['exam_id'];

// Get student_id from user_id
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $student_user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$student = $result->fetch_assoc()) {
  echo json_encode(['status' => 'error', 'message' => 'Student not registered properly']);
  exit;
}
$student_id = $student['id'];

// Check exam time window
$now = date('Y-m-d H:i:s');
$query = "SELECT date, start_time, end_time FROM exams WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
  echo json_encode(['status' => 'error', 'message' => 'Exam not found']);
  exit;
}

$exam_start = $exam['date'] . ' ' . $exam['start_time'];
$exam_end = $exam['date'] . ' ' . $exam['end_time'];
$exam_start_buffer = date('Y-m-d H:i:s', strtotime($exam_start . ' -30 minutes'));

if ($now < $exam_start_buffer || $now > $exam_end) {
  echo json_encode(['status' => 'error', 'message' => 'You can only mark attendance within the exam period.']);
  exit;
}

// Check if already marked
$check = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND exam_id = ?");
$check->bind_param("ii", $student_id, $exam_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo json_encode(['status' => 'info', 'message' => 'You have already marked your attendance.']);
  exit;
}

// Mark attendance
$insert = $conn->prepare("INSERT INTO attendance (student_id, exam_id, timestamp) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $student_id, $exam_id);

if ($insert->execute()) {
  echo json_encode(['status' => 'success', 'message' => '✅ Attendance marked successfully!']);
} else {
  echo json_encode(['status' => 'error', 'message' => '❌ Could not mark attendance. Try again.']);
}

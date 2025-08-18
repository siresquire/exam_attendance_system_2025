<?php
require_once 'db.php';

// Ensure exam_id is set and is a number
if (!isset($_GET['exam_id']) || !is_numeric($_GET['exam_id'])) {
  die("Invalid exam ID.");
}

$exam_id = (int) $_GET['exam_id'];

// Set headers for Excel export
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=exam_attendance.xls");

// Fetch exam details
$examQuery = $conn->prepare("
  SELECT c.name AS course, e.date, e.start_time, e.end_time, v.name AS venue, u.name AS lecturer
  FROM exams e
  JOIN courses c ON e.course_id = c.id
  JOIN venues v ON e.venue_id = v.id
  JOIN users u ON e.lecturer_id = u.id
  WHERE e.id = ?
");
$examQuery->bind_param("i", $exam_id);
$examQuery->execute();
$examResult = $examQuery->get_result();
$exam = $examResult->fetch_assoc();

if (!$exam) {
  die("No exam found.");
}

// Output exam info
echo "Course\tDate\tStart Time\tEnd Time\tVenue\tLecturer\n";
echo "{$exam['course']}\t{$exam['date']}\t{$exam['start_time']}\t{$exam['end_time']}\t{$exam['venue']}\t{$exam['lecturer']}\n\n";

// Fetch student attendance
$studentsQuery = $conn->prepare("
  SELECT s.index_number, s.name
  FROM attendance a
  JOIN students s ON a.student_id = s.id
  WHERE a.exam_id = ?
");
$studentsQuery->bind_param("i", $exam_id);
$studentsQuery->execute();
$students = $studentsQuery->get_result();

// Output student list
echo "Index Number\tName\n";
while ($s = $students->fetch_assoc()) {
  echo "{$s['index_number']}\t{$s['name']}\n";
}
?>

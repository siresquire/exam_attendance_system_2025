<?php
require_once 'db.php';

if (!isset($_GET['exam_id'])) {
  die("Missing exam_id.");
}

$exam_id = intval($_GET['exam_id']);

// Fetch exam and attendance
$exam = $conn->query("
  SELECT c.name AS course 
  FROM exams e 
  JOIN courses c ON e.course_id = c.id 
  WHERE e.id = $exam_id
")->fetch_assoc();

$att = $conn->query("
  SELECT s.index_number, s.name 
  FROM attendance a 
  JOIN students s ON a.student_id = s.id 
  WHERE a.exam_id = $exam_id
");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Attendance_{$exam['course']}.xls");

echo "Index Number\tName\n";

while ($row = $att->fetch_assoc()) {
  echo "{$row['index_number']}\t{$row['name']}\n";
}

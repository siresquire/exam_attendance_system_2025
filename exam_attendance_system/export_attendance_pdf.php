<?php
require_once 'db.php';
require_once 'vendor/autoload.php'; // For Dompdf

use Dompdf\Dompdf;

if (!isset($_GET['exam_id'])) {
  die("Missing exam_id.");
}

$exam_id = intval($_GET['exam_id']);

// Fetch exam and attendance
$examQuery = $conn->prepare("
  SELECT c.name AS course, e.date, e.start_time, e.end_time 
  FROM exams e 
  JOIN courses c ON e.course_id = c.id 
  WHERE e.id = ?
");
$examQuery->bind_param("i", $exam_id);
$examQuery->execute();
$exam = $examQuery->get_result()->fetch_assoc();

$attQuery = $conn->prepare("
  SELECT s.index_number, s.name 
  FROM attendance a 
  JOIN students s ON a.student_id = s.id 
  WHERE a.exam_id = ?
");
$attQuery->bind_param("i", $exam_id);
$attQuery->execute();
$attResult = $attQuery->get_result();

// HTML output
$html = "<h2>Exam Attendance - {$exam['course']}</h2>";
$html .= "<p>Date: {$exam['date']} | Time: {$exam['start_time']} - {$exam['end_time']}</p>";
$html .= "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
$html .= "<tr><th>Index Number</th><th>Name</th></tr>";

while ($row = $attResult->fetch_assoc()) {
  $html .= "<tr><td>{$row['index_number']}</td><td>{$row['name']}</td></tr>";
}

$html .= "</table>";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Attendance_{$exam['course']}.pdf");

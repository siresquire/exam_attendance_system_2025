<?php
require 'vendor/autoload.php';
require 'db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['exam_id'])) {
  die("No exam selected.");
}

$exam_id = (int) $_GET['exam_id'];

// Fetch exam info
$stmt = $conn->prepare("
  SELECT e.date, e.start_time, e.end_time, c.name AS course, v.name AS venue, u.name AS lecturer
  FROM exams e
  JOIN courses c ON e.course_id = c.id
  JOIN venues v ON e.venue_id = v.id
  JOIN users u ON e.lecturer_id = u.id
  WHERE e.id = ?
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

// Fetch students
$students = $conn->query("
  SELECT s.index_number, s.name 
  FROM attendance a
  JOIN students s ON a.student_id = s.id
  WHERE a.exam_id = $exam_id
");

// Build HTML
$logo_path = 'assets/school_logo.png'; // adjust path if needed
$school_name = "🌍 Bright Future Institute";
$date = date("F j, Y");

$html = "
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; }
  .header { text-align: center; }
  .logo { width: 80px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
  th { background-color: #004080; color: white; }
</style>

<div class='header'>
  <img src='$logo_path' class='logo' alt='Logo'>
  <h2>$school_name</h2>
  <h3>Exam Attendance Sheet</h3>
  <p>Date Generated: $date</p>
</div>

<p><strong>Course:</strong> {$exam['course']}<br>
<strong>Date:</strong> {$exam['date']} | 
<strong>Time:</strong> {$exam['start_time']} - {$exam['end_time']}<br>
<strong>Venue:</strong> {$exam['venue']}<br>
<strong>Lecturer:</strong> {$exam['lecturer']}</p>

<table>
  <thead>
    <tr>
      <th>Index Number</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody>";

while ($row = $students->fetch_assoc()) {
  $html .= "<tr>
    <td>{$row['index_number']}</td>
    <td>{$row['name']}</td>
  </tr>";
}

$html .= "</tbody></table>";

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true); // Enable external assets like images
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("exam_attendance_{$exam_id}.pdf", ["Attachment" => 0]); // 0 to view, 1 to force download
?>

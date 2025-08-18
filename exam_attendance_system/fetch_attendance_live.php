<?php
require_once 'db.php';

if (!isset($_GET['exam_id']) || !is_numeric($_GET['exam_id'])) {
  echo "<p style='color:red;'>❌ Invalid Exam ID.</p>";
  exit;
}

$exam_id = intval($_GET['exam_id']);

$stmt = $conn->prepare("
  SELECT s.name, s.index_number, s.department, a.timestamp 
  FROM attendance a 
  JOIN students s ON a.student_id = s.id 
  WHERE a.exam_id = ?
  ORDER BY a.timestamp ASC
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
  <h3>📋 Students Present</h3>
  <table>
    <tr>
      <th>Name</th>
      <th>Index Number</th>
      <th>Department</th>
      <th>Marked At</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['index_number']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td class="timestamp"><?= $row['timestamp'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php else: ?>
  <p>No student has marked attendance for this exam yet.</p>
<?php endif; ?>

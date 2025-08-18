<?php
session_start();
require_once 'db.php';

include 'navbar.php';

// ✅ Only allow students to view
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Access Denied.</p>";
    exit;
}

// Try to get student_id using user_id first
$student_id = null;

$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();

if ($student) {
    $student_id = $student['id'];
} elseif (isset($_SESSION['index_number'])) {
    // fallback: use index_number if user_id not set
    $stmt2 = $conn->prepare("SELECT id FROM students WHERE index_number = ?");
    $stmt2->bind_param("s", $_SESSION['index_number']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $student = $res2->fetch_assoc();
    if ($student) {
        $student_id = $student['id'];
    }
}

// If no student_id found, just show message
if (!$student_id) {
    echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ You are not registered as a student.</p>";
    exit;
}

// ✅ Get all exams the student has registered for
$sql = "
SELECT 
    e.exam_date AS date,
    e.start_time,
    e.end_time,
    e.venue_name AS venue,
    c.course_name AS course,
    u.name AS lecturer,
    a.timestamp
FROM attendance a
JOIN exams e ON a.exam_id = e.id
JOIN courses c ON e.course_id = c.id
JOIN users u ON e.lecturer_id = u.id
WHERE a.student_id = ?
ORDER BY e.exam_date DESC, e.start_time DESC
";

$stmt3 = $conn->prepare($sql);
$stmt3->bind_param("i", $student_id);
$stmt3->execute();
$result = $stmt3->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
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
            background: rgba(255,255,255,0.95);
            padding: 30px;
            max-width: 1000px;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #004080;
            text-align: center;
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
        .registered {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>👁️ My Attendance</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Course</th>
                <th>Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Lecturer</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td><?= date('d-m-Y', strtotime($row['date'])) ?></td>
                    <td><?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?></td>
                    <td><?= htmlspecialchars($row['venue']) ?></td>
                    <td><?= htmlspecialchars($row['lecturer']) ?></td>
                    <td class="registered">✅ Registered</td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No exam registrations yet.</p>
    <?php endif; ?>
</div>
</body>
</html>

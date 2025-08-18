<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Fetch avatar
$avatar = 'assets/default_avatar.png';
$stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($avatarPath);
if ($stmt->fetch() && $avatarPath) {
  $avatar = $avatarPath;
}
$stmt->close();

// Stats
$total_students = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$total_exams = $conn->query("SELECT COUNT(*) FROM exams")->fetch_row()[0];
$total_courses = $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];

// Count total programs
//$program_result = $conn->query("SELECT COUNT(*) AS total FROM programs");
//$total_programs = $program_result->fetch_assoc()['total'];


include 'navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <style>
    body {
      font-family: Arial;
      margin: 0;
      padding: 0;
      background-image: url('assets/school_bg1.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      background-attachment: fixed;
      transition: background 0.3s, color 0.3s;
    }

    body.dark-mode {
      background-color: #121212;
      background-image: none;
      color: white;
    }

    .container {
      max-width: 1200px;
      margin: 50px auto;
      padding: 30px;
      background: #a7d0fa;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    body.dark-mode .container {
      background: rgba(30, 30, 30, 0.94);
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .profile {
      display: flex;
      align-items: center;
    }

    .profile img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
      margin-right: 10px;
      border: 2px solid #ccc;
    }

    .stats {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }

    .stat-box {
      flex: 1;
      min-width: 200px;
      background: #004080;
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      font-size: 18px;
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }

    .card {
  background: #fff;
  padding: 15px;
  border-radius: 10px;
  text-align: center;
  font-weight: bold;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: transform 0.2s;
  height: 15px; /* fixed height for uniformity */
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  overflow: hidden;
}



    .card:hover {
      transform: scale(1.05);
      background: #e6f2ff;
    }

    .card a {
      color: #004080;
      text-decoration: none;
    }

    .toggle-theme {
      cursor: pointer;
      padding: 10px 20px;
      background: #004080;
      color: white;
      border: none;
      border-radius: 6px;
    }

    .toggle-theme:hover {
      background: #003060;
    }

    /* Dark Mode Styles */
    body.dark-mode .card {
      background: #1e1e1e;
    }

    body.dark-mode .card a {
      color: #aad8ff;
    }

    body.dark-mode .stat-box {
      background: #333;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="top-bar">
    <div class="profile">
      <img src="<?= $avatar ?>" alt="Avatar">
      <div>
        <strong><?= htmlspecialchars($user_name) ?></strong><br>
        <small><?= ucfirst($user_role) ?></small>
      </div>
    </div>
    <button class="toggle-theme" onclick="toggleTheme()">🌓 Toggle Theme</button>
  </div>

  <div class="stats">
    <div class="stat-box">👨‍🎓 Students: <?= $total_students ?></div>
    <div class="stat-box">📝 Exams: <?= $total_exams ?></div>
    <div class="stat-box">📚 Courses: <?= $total_courses ?></div>
    <!-- <div class="stat-box">🎓 Programs: <?= $total_programs ?></div> -->
     
  </div>

  <div class="card-grid">
    <?php if ($user_role === 'admin'): ?>
      <div class="card"><a href="create_course.php">📚 Create Course</a></div>
      <div class="card"><a href="create_exam.php">📆 Create Exam</a></div>
      <div class="card"><a href="list_exams.php">📋 View Exams</a></div>
      <div class="card"><a href="record_attendance.php">📝 Mark Attendance</a></div>
      <div class="card"><a href="view_attendance.php">📊 View Attendance</a></div>
      <div class="card"><a href="view_attendance_live.php">📡 Live Attendance</a></div>
      <div class="card"><a href="add_user.php">👥 Add User</a></div>
      <div class="card"><a href="generate_reports.php">📄 Generate Reports</a></div>
    <?php elseif ($user_role === 'lecturer'): ?>
      <div class="card"><a href="create_exam.php">🧪 Schedule Exam</a></div>
      <div class="card"><a href="record_attendance.php">📝 Mark Attendance</a></div>
      <div class="card"><a href="view_attendance.php">📊 View Attendance</a></div>
      <div class="card"><a href="view_attendance_live.php">📡 Live Attendance</a></div>
      <div class="card"><a href="generate_reports.php">📄 Generate Reports</a></div>
    <?php elseif ($user_role === 'student'): ?>
      <div class="card"><a href="register_exam.php">📝 Register for Exam</a></div>
      <div class="card"><a href="today_exam.php">📌 Today’s Exam Check-In</a></div>
      <div class="card"><a href="view_my_attendance.php">👁️ My Attendance</a></div>
      <div class="card"><a href="my_exam_report.php">📄 My Exam Report</a></div>
    <?php endif; ?>
    <div class="card"><a href="change_password.php">🔐 Change Password</a></div>
  </div>
</div>

<script>
  function toggleTheme() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
  }

  window.onload = () => {
    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark-mode");
    }
  };
</script>

</body>
</html>

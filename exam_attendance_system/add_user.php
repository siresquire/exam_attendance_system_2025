<?php
session_start();
require_once 'db.php';

// ✅ Only allow access to admin users
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  echo "<p style='color:red; font-weight:bold; text-align:center;'>❌ Sorry, you don't have the privileges to do this.</p>";
  exit;
}

// ✅ Fetch programs
$programs = $conn->query("SELECT id, program_name, session FROM programs ORDER BY program_name");



$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $role = $_POST['role'];
  $phone = $_POST['phone'] ?? null;

  $avatarPath = null;

  if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $avatarName = uniqid() . "." . $ext;
    $avatarPath = "uploads/" . $avatarName;
    move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
  }

  $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $error = "❌ Email already exists!";
  } else {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, avatar) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password_hash, $role, $avatarPath);

    if ($stmt->execute()) {
      $user_id = $stmt->insert_id;

      if ($role === 'student') {
        $index_number = $_POST['index_number'] ?? '5300' . rand(100000, 999999);
        $department = $_POST['department'] ?? 'IT';
        $program_id = intval($_POST['program_id']);

        $stmt2 = $conn->prepare("INSERT INTO students (name, index_number, department, user_id, program_id) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssii", $name, $index_number, $department, $user_id, $program_id);
        $stmt2->execute();
      }

      if ($role === 'lecturer') {
        $stmt3 = $conn->prepare("INSERT INTO lecturers (name, email, phone) VALUES (?, ?, ?)");
        $stmt3->bind_param("sss", $name, $email, $phone);
        $stmt3->execute();
      }

      $success = "✅ User added successfully!";
    } else {
      $error = "❌ Error: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add New User</title>
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
    max-width: 600px;
    margin: auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
  }
  h2 {
    text-align: center;
    color: #004080;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
  }
  input, select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 5px;
  }
  button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #004080;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
  }
  .success {
    background: #e0ffe0;
    color: green;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
  }
  .error {
    background: #ffe0e0;
    color: red;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
  }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="box">
  <h2>➕ Add New User</h2>

  <?php if ($success): ?><div class="success"><?= $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="error"><?= $error; ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Role:</label>
    <select name="role" id="role-select" required>
      <option value="">-- Select Role --</option>
      <option value="admin">Admin</option>
      <option value="lecturer">Lecturer</option>
      <option value="student">Student</option>
    </select>

    <label>Upload Avatar:</label>
    <input type="file" name="avatar" accept="image/*">

    <!-- Student-specific fields -->
    <div id="student-fields" style="display:none;">
      <label>Index Number:</label>
      <input type="text" name="index_number" placeholder="e.g. 5300123456">

      <label>Department:</label>
      <input type="text" name="department" value="IT">

      <label>Program:</label>
        <select name="program_id" required>
          <option value="">-- Select Program --</option>
          <?php while ($p = $programs->fetch_assoc()): ?>
  <option value="<?= $p['id'] ?>">
    <?= htmlspecialchars($p['program_name']) ?> (<?= $p['session'] ?>)
  </option>
<?php endwhile; ?>
</select>

    <!-- Lecturer-specific fields -->
    <div id="lecturer-fields" style="display:none;">
      <label>Phone:</label>
      <input type="text" name="phone" placeholder="e.g. 0551234567">
    </div>

    <button type="submit">Create User</button>
  </form>
</div>

<script>
  const roleSelect = document.getElementById('role-select');
  const studentFields = document.getElementById('student-fields');
  const lecturerFields = document.getElementById('lecturer-fields');

  function toggleFields() {
    studentFields.style.display = roleSelect.value === 'student' ? 'block' : 'none';
    lecturerFields.style.display = roleSelect.value === 'lecturer' ? 'block' : 'none';
  }

  roleSelect.addEventListener('change', toggleFields);
  toggleFields(); // Initial load
</script>

</body>
</html>

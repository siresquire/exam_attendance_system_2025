<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['user_role'] = $user['role'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "❌ Incorrect password.";
    }
  } else {
    $error = "❌ No user found with that email.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #004080, #0066cc);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #004080;
    }
    input[type="email"], input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background: #004080;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: #003060;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
    .logo {
      margin-bottom: 15px;
    }
    .register-link {
      margin-top: 15px;
      display: block;
      color: #004080;
      text-decoration: none;
      font-size: 14px;
    }
    .register-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="logo">
      <img src="assets/logo.png" alt="School Logo" width="60">
    </div>
    <h2>Login to Exam System</h2>

    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <!-- New User Link -->
    <a href="register_student.php" class="register-link">I’m a new user – Register me instead</a>
  </div>
</body>
</html>

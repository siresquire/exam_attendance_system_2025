<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "exam_attendance_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define admin account
$name = "Admin User";
$email = "itewkndgrp@aamusted.edu";
$plainPassword = "admin123";
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
$role = "admin";

// Delete existing user (optional)
$conn->query("DELETE FROM users WHERE email = '$email'");

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "✅ Admin user inserted successfully!<br>";
    echo "Email: $email<br>";
    echo "Password: $plainPassword<br>";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

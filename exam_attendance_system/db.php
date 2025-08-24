<?php
$host = "database";
$user = "lamp";
$password = "lamp";
$database = "lamp";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

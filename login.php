<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "nutricion";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = hash('sha256', $_POST['password']);

$sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $_SESSION['usuario'] = $row['username'];
  $_SESSION['tipo'] = $row['tipo'];
  
  if ($row['tipo'] === 'admin') {
    header("Location: admin_dashboard.php");
  } else {
    header("Location: user_dashboard.php");
  }
} else {
  echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href = 'index.php';</script>";
}

$conn->close();
?>

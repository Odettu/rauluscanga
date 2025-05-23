<?php
session_start();

// Solo usuarios normales pueden acceder
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'normal') {
  header("Location: index.php");
  exit();
}

$conexion = new mysqli("localhost", "root", "", "nutricion");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$username = $_SESSION['usuario'];

// Obtener datos del paciente
$stmt = $conexion->prepare("SELECT * FROM pacientes WHERE usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
  echo "<div class='alert alert-danger m-5'>No se encontró información del paciente.</div>";
  exit();
}

$paciente = $resultado->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Mi Perfil</h3>
  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6"><strong>Nombre:</strong> <?= htmlspecialchars($paciente['nombre']) ?></div>
        <div class="col-md-6"><strong>Usuario:</strong> <?= htmlspecialchars($paciente['usuario']) ?></div>
        <div class="col-md-6"><strong>Correo:</strong> <?= htmlspecialchars($paciente['correo']) ?></div>
        <div class="col-md-6"><strong>WhatsApp:</strong> <?= htmlspecialchars($paciente['whatsapp']) ?></div>
        <div class="col-md-4"><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($paciente['fecha_nacimiento']) ?></div>
        <div class="col-md-2"><strong>Edad:</strong> <?= $paciente['edad'] ?></div>
        <div class="col-md-3"><strong>Sexo:</strong> <?= htmlspecialchars($paciente['sexo']) ?></div>
        <div class="col-md-3"><strong>Ocupación:</strong> <?= htmlspecialchars($paciente['ocupacion']) ?></div>
        <div class="col-md-3"><strong>Peso actual:</strong> <?= $paciente['peso_actual'] ?> kg</div>
        <div class="col-md-3"><strong>% Grasa:</strong> <?= $paciente['porcentaje_grasa'] ?>%</div>
      </div>
    </div>
  </div>
</div>
</body>
</html>

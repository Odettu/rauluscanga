<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
  header("Location: index.php");
  exit();
}

$conexion = new mysqli("localhost", "root", "", "nutricion");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$id = $_GET['id'] ?? null;
if (!$id) {
  die("ID de paciente no especificado.");
}

// Obtener paciente
$stmt = $conexion->prepare("SELECT * FROM pacientes WHERE id_px = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$paciente = $resultado->fetch_assoc();
$stmt->close();

if (!$paciente) {
  die("Paciente no encontrado.");
}

// Actualizar paciente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
  $sql = "UPDATE pacientes SET nombre=?, whatsapp=?, correo=?, fecha_nacimiento=?, edad=?, ocupacion=?, sexo=?, peso_actual=?, porcentaje_grasa=?, usuario=?";

  // Si se proporcionó nueva contraseña
  if (!empty($_POST['nueva_contrasena'])) {
    $sql .= ", contrasena=?";
  }

  $sql .= " WHERE id_px=?";

  // Preparar parámetros
  if (!empty($_POST['nueva_contrasena'])) {
    $hash = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
      "ssssissddssi",
      $_POST['nombre'], $_POST['whatsapp'], $_POST['correo'], $_POST['fecha_nacimiento'],
      $_POST['edad'], $_POST['ocupacion'], $_POST['sexo'],
      $_POST['peso_actual'], $_POST['porcentaje_grasa'],
      $_POST['usuario'], $hash, $id
    );
  } else {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
      "ssssissddsi",
      $_POST['nombre'], $_POST['whatsapp'], $_POST['correo'], $_POST['fecha_nacimiento'],
      $_POST['edad'], $_POST['ocupacion'], $_POST['sexo'],
      $_POST['peso_actual'], $_POST['porcentaje_grasa'],
      $_POST['usuario'], $id
    );
  }

  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Paciente actualizado correctamente'); window.location.href='registro_pacientes.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Paciente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Editar Paciente</h3>
  <form method="POST" class="row g-3">
    <!-- campos normales -->
    <div class="col-md-6">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($paciente['nombre']) ?>" required>
    </div>
    <div class="col-md-6">
      <label>WhatsApp</label>
      <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($paciente['whatsapp']) ?>">
    </div>
    <div class="col-md-6">
      <label>Correo Electrónico</label>
      <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($paciente['correo']) ?>">
    </div>
    <div class="col-md-3">
      <label>Fecha de Nacimiento</label>
      <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $paciente['fecha_nacimiento'] ?>">
    </div>
    <div class="col-md-3">
      <label>Edad</label>
      <input type="number" name="edad" class="form-control" value="<?= $paciente['edad'] ?>">
    </div>
    <div class="col-md-4">
      <label>Ocupación</label>
      <input type="text" name="ocupacion" class="form-control" value="<?= htmlspecialchars($paciente['ocupacion']) ?>">
    </div>
    <div class="col-md-4">
      <label>Sexo</label>
      <select name="sexo" class="form-select">
        <option value="Masculino" <?= $paciente['sexo'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="Femenino" <?= $paciente['sexo'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
        <option value="Otro" <?= $paciente['sexo'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
      </select>
    </div>
    <div class="col-md-2">
      <label>Peso (kg)</label>
      <input type="number" step="0.01" name="peso_actual" class="form-control" value="<?= $paciente['peso_actual'] ?>">
    </div>
    <div class="col-md-2">
      <label>% Grasa</label>
      <input type="number" step="0.01" name="porcentaje_grasa" class="form-control" value="<?= $paciente['porcentaje_grasa'] ?>">
    </div>
    <div class="col-md-3">
      <label>Usuario</label>
      <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($paciente['usuario']) ?>" required>
    </div>
    <div class="col-md-3">
      <label>Nueva Contraseña (opcional)</label>
      <input type="password" name="nueva_contrasena" class="form-control">
      <small class="text-muted">Déjalo vacío si no quieres cambiarla</small>
    </div>
    <div class="col-md-12 text-end">
      <button type="submit" name="actualizar" class="btn btn-success">Actualizar</button>
      <a href="registro_pacientes.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
  header("Location: index.php");
  exit();
}

$conexion = new mysqli("localhost", "root", "", "nutricion");

$id = $_GET['id'] ?? null;
if (!$id) {
  die("ID de usuario no especificado.");
}

// Obtener datos actuales
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario) {
  die("Usuario no encontrado.");
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $tipo = $_POST['tipo'];

  if (!empty($_POST['nueva_contrasena'])) {
    $contrasena = hash('sha256', $_POST['nueva_contrasena']);
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, tipo=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $nombre, $tipo, $contrasena, $id);
  } else {
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, tipo=? WHERE id=?");
    $stmt->bind_param("ssi", $nombre, $tipo, $id);
  }

  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Usuario actualizado correctamente'); window.location.href='usuarios.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Editar Usuario</h3>
  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label>Nombre Completo</label>
      <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    </div>
    <div class="col-md-4">
      <label>Tipo de Usuario</label>
      <select name="tipo" class="form-select" required>
        <option value="normal" <?= $usuario['tipo'] === 'normal' ? 'selected' : '' ?>>Usuario Normal</option>
        <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
      </select>
    </div>
    <div class="col-md-6">
      <label>Nueva Contraseña (opcional)</label>
      <input type="password" name="nueva_contrasena" class="form-control">
      <small class="text-muted">Déjalo vacío si no quieres cambiarla</small>
    </div>
    <div class="col-md-12 text-end">
      <button type="submit" class="btn btn-success">Guardar Cambios</button>
      <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>

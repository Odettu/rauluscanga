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

// INSERTAR PACIENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  include("conexion.php");

  $nombre = $_POST['nombre'];
  $whatsapp = $_POST['whatsapp'];
  $correo = $_POST['correo'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];
  $edad = $_POST['edad'];
  $ocupacion = $_POST['ocupacion'];
  $sexo = $_POST['sexo'];
  $peso_actual = $_POST['peso_actual'];
  $porcentaje_grasa = $_POST['porcentaje_grasa'];
  $username = $_POST['usuario'];
  $password_raw = $_POST['contrasena'];
  $password_hash = hash('sha256', $password_raw); // Importante: usar el mismo hash que el login

  // Verificar si el username ya existe en la tabla usuarios
  $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
  $verificar->bind_param("s", $username);
  $verificar->execute();
  $verificar->store_result();

  if ($verificar->num_rows > 0) {
    echo "<script>alert('El nombre de usuario ya existe. Elige otro.'); window.history.back();</script>";
    $verificar->close();
    exit();
  }
  $verificar->close();

  // Insertar en pacientes
  $stmt = $conexion->prepare("INSERT INTO pacientes 
    (nombre, whatsapp, correo, fecha_nacimiento, edad, ocupacion, sexo, peso_actual, porcentaje_grasa, usuario, contrasena)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssissddss", $nombre, $whatsapp, $correo, $fecha_nacimiento, $edad, $ocupacion, $sexo, $peso_actual, $porcentaje_grasa, $username, $password_hash);
  $stmt->execute();
  $stmt->close();

  // Insertar en usuarios (para login)
  $stmt2 = $conexion->prepare("INSERT INTO usuarios (nombre, username, password, tipo) VALUES (?, ?, ?, 'normal')");
  $stmt2->bind_param("sss", $nombre, $username, $password_hash);
  $stmt2->execute();
  $stmt2->close();

  echo "<script>alert('Paciente y usuario creados correctamente'); window.location.href='registro_pacientes.php';</script>";
}

// ELIMINAR PACIENTE
if (isset($_GET['eliminar'])) {
  $stmt = $conexion->prepare("DELETE FROM pacientes WHERE id = ?");
  $stmt->bind_param("i", $_GET['eliminar']);
  $stmt->execute();
  $stmt->close();
}

// LISTAR PACIENTES
$resultado = $conexion->query("SELECT * FROM pacientes ORDER BY id_px DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Pacientes</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-user-shield me-2"></i>Panel de Administrador</a>
            <div class="d-flex">
                <span class="text-white me-3"><i class="fas fa-user"></i> <?= $_SESSION['usuario']; ?></span>
                <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
            </div>
        </div>
    </nav>
<div class="container mt-5">
  <h3 class="mb-4">Registrar Paciente</h3>
  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>WhatsApp</label>
      <input type="text" name="whatsapp" class="form-control">
    </div>
    <div class="col-md-6">
      <label>Correo Electrónico</label>
      <input type="email" name="correo" class="form-control">
    </div>
    <div class="col-md-3">
      <label>Fecha de Nacimiento</label>
      <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
    </div>
    <div class="col-md-3">
      <label>Edad</label>
      <input type="number" name="edad" id="edad" class="form-control" readonly>
    </div>
    <div class="col-md-4">
      <label>Ocupación</label>
      <input type="text" name="ocupacion" class="form-control">
    </div>
    <div class="col-md-4">
      <label>Sexo</label>
      <select name="sexo" class="form-select">
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
        <option value="Otro">Otro</option>
      </select>
    </div>
    <div class="col-md-2">
      <label>Peso (kg)</label>
      <input type="number" step="0.01" name="peso_actual" class="form-control">
    </div>
    <div class="col-md-2">
      <label>% Grasa</label>
      <input type="number" step="0.01" name="porcentaje_grasa" class="form-control">
    </div>
    <div class="col-md-3">
      <label>Usuario</label>
      <input type="text" name="usuario" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label>Contraseña</label>
      <input type="password" name="contrasena" class="form-control" required>
    </div>
    <div class="col-md-12 text-end">
      <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
      <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Regresar</a>

    </div>
  </form>

  <hr class="my-5">

  <h4>Lista de Pacientes</h4>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>WhatsApp</th>
        <th>Correo</th>
        <th>Edad</th>
        <th>Sexo</th>
        <th>Usuario</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($fila['nombre']) ?></td>
          <td><?= htmlspecialchars($fila['whatsapp']) ?></td>
          <td><?= htmlspecialchars($fila['correo']) ?></td>
          <td><?= $fila['edad'] ?></td>
          <td><?= $fila['sexo'] ?></td>
          <td><?= htmlspecialchars($fila['usuario']) ?></td>
          <td>
            <a href="editar_paciente.php?id=<?= $fila['id_px'] ?>" class="btn btn-sm btn-warning">Editar</a>
            <a href="?eliminar=<?= $fila['id_px'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este paciente?')">Eliminar</a>
             <a href="cntacitas.php?id_px=<?= $fila['id_px'] ?>" class="btn btn-sm btn-primary">Citas</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script>
  document.getElementById('fecha_nacimiento').addEventListener('change', function () {
    const fechaNacimiento = new Date(this.value);
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const m = hoy.getMonth() - fechaNacimiento.getMonth();

    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
      edad--;
    }

    document.getElementById('edad').value = edad >= 0 ? edad : '';
  });
</script>
</body>
</html>

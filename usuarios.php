<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
  header("Location: index.php");
  exit();
}

$conexion = new mysqli("localhost", "root", "", "nutricion");

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
  $nombre = $_POST['nombre'];
  $usuario = $_POST['usuario'];
  $contrasena = hash('sha256', $_POST['contrasena']);
  $tipo = $_POST['tipo'];

  $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, username, password, tipo) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $nombre, $usuario, $contrasena, $tipo);
  $stmt->execute();
  $stmt->close();
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
  $id = $_GET['eliminar'];
  $conexion->query("DELETE FROM usuarios WHERE id = $id");
}

// Obtener usuarios
$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Gestión de Usuarios</h3>

  <form method="POST" class="row g-3 mb-4">
    <div class="col-md-3">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
    </div>
    <div class="col-md-2">
      <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
    </div>
    <div class="col-md-2">
      <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
    </div>
    <div class="col-md-2">
      <select name="tipo" class="form-select" required>
        <option value="normal">Usuario Normal</option>
        <option value="admin">Administrador</option>
      </select>
    </div>
    <div class="col-md-3 text-end">
      <button type="submit" name="crear" class="btn btn-primary w-100">Agregar Usuario</button>
    </div>
  </form>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Usuario</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $usuarios->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['nombre']) ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= $row['tipo'] === 'admin' ? 'Administrador' : 'Normal' ?></td>
    <td>
      <a href="editar_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
      <a href="?eliminar=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
    </td>
  </tr>
<?php endwhile; ?>

    </tbody>
  </table>
</div>
</body>
</html>

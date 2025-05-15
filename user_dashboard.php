<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'normal') {
  header("Location: index.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-user me-2"></i>Usuario</a>
      <div class="d-flex">
        <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <h3>Hola, <?php echo $_SESSION['usuario']; ?> (Usuario Normal)</h3>
    <p class="text-muted">Este es tu panel de usuario. Aquí podrás acceder a tus opciones personales.</p>

    <div class="row mt-4">
      <div class="col-md-4">
        <div class="card shadow p-3">
          <i class="fas fa-user-cog fa-2x mb-2 text-info"></i>
          <h5>Perfil</h5>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow p-3">
          <i class="fas fa-envelope fa-2x mb-2 text-warning"></i>
          <h5>Mensajes</h5>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
  header("Location: index.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-user-shield me-2"></i>Panel Admin</a>
      <div class="d-flex">
        <span class="text-white me-3"><i class="fas fa-user"></i> <?= $_SESSION['usuario']; ?></span>
        <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <h3>Bienvenido, <?= $_SESSION['usuario']; ?> (Administrador)</h3>
    <p class="text-muted">Selecciona un módulo para administrar.</p>

    <div class="row mt-4 g-4">
      <!-- Módulo: Registro de Pacientes -->
      <div class="col-md-4">
        <a href="registro_pacientes.php" class="text-decoration-none">
          <div class="card shadow p-4 text-center h-100">
            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
            <h5 class="text-dark">Pacientes</h5>
          </div>
        </a>
      </div>

      <div class="row mt-4 g-4">
      <!-- Módulo: Registro de Cita -->
      <div class="col-md-4">
        <a href="registro_cita.php" class="text-decoration-none">
          <div class="card shadow p-4 text-center h-100">
            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
            <h5 class="text-dark">Citas</h5>
          </div>
        </a>
      </div> 

      <!-- Módulo: Gestión de Usuarios -->
      <div class="col-md-4">
        <a href="usuarios.php" class="text-decoration-none">
          <div class="card shadow p-4 text-center h-100">
            <i class="fas fa-users-cog fa-3x text-warning mb-3"></i>
            <h5 class="text-dark">Usuarios</h5>
          </div>
        </a>
      </div>

      <!-- Módulo: Estadísticas (Placeholder o futuro) -->
      <div class="col-md-4">
        <a href="#" class="text-decoration-none">
          <div class="card shadow p-4 text-center h-100">
            <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
            <h5 class="text-dark">Estadísticas</h5>
          </div>
        </a>
      </div>
    </div>
  </div>

</body>
</html>

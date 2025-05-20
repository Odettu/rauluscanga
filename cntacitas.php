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



// Obtener pacientes para el formulario

$nombre_paciente = "";
if (isset($_GET['id_px'])) {
    $id_px = $_GET['id_px'];

    $stmt1 = $conexion->prepare("SELECT id_px, nombre FROM pacientes where id_px=? ");
    $stmt1->bind_param("i", $id_px);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
    if ($res1->num_rows > 0) {
        $fila = $res1->fetch_assoc();
        $nombre_paciente = $fila['nombre']; // Aquí guardas el nombre en tu variable

    } else {
        echo "No se encontró ningún paciente con ese ID";
    }

    $stmt1->close(); // No olvides cerrar el statement


    $stmt = $conexion->prepare("SELECT fecha_cita, peso_actual, porcentaje_grasa, biceps, triceps, cintura, gluteo, pierna, espalda
                              FROM citas
                              WHERE id_px = ?
                              ORDER BY fecha_cita DESC");

    if ($stmt) {
        $stmt->bind_param("i", $id_px);
        $stmt->execute();
        $resultado = $stmt->get_result();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Registrar Cita</title>
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>

<body class="bg-light ">

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
        <div class="card">
            <div class="card-header bg-success">
                <h3>Consulta de Citas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <strong><label>Paciente</label></strong>
                        <input type="nombre" name="<?= $nombre_paciente ?>" class="form-control" required value="<?= $nombre_paciente ?>">
                    </div>
                </div>
                <br>

                <?php if (isset($_GET['id_px'])) { ?>
                    <h4>Historial de Citas</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Peso</th>
                                <th>% Grasa</th>
                                <th>Bíceps</th>
                                <th>Tríceps</th>
                                <th>Cintura</th>
                                <th>Glúteo</th>
                                <th>Pierna</th>
                                <th>Espalda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fila['fecha_cita']) ?></td>
                                    <td><?= $fila['peso_actual'] ?></td>
                                    <td><?= $fila['porcentaje_grasa'] ?></td>
                                    <td><?= $fila['biceps'] ?></td>
                                    <td><?= $fila['triceps'] ?></td>
                                    <td><?= $fila['cintura'] ?></td>
                                    <td><?= $fila['gluteo'] ?></td>
                                    <td><?= $fila['pierna'] ?></td>
                                    <td><?= $fila['espalda'] ?></td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>




</body>

</html>
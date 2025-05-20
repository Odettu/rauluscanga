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

// INSERTAR CITA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_px = $_POST['id_px'];
    $fecha_cita = $_POST['fecha_cita'];
    $biceps = $_POST['biceps'];
    $triceps = $_POST['triceps'];
    $cintura = $_POST['cintura'];
    $gluteo = $_POST['gluteo'];
    $pierna = $_POST['pierna'];
    $espalda = $_POST['espalda'];
    $peso_actual = $_POST['peso_actual'];
    $grasa = $_POST['porcentaje_grasa'];

    // Insertar en la tabla citas
    $stmt = $conexion->prepare("INSERT INTO citas 
    (id_px, fecha_cita, biceps, triceps, cintura, gluteo, pierna, espalda, peso_actual, porcentaje_grasa)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");



    $stmt->bind_param("isdddddddd", $id_px, $fecha_cita, $biceps, $triceps, $cintura, $gluteo, $pierna, $espalda, $peso_actual, $grasa);
    $stmt->execute();
    $stmt->close();

    // Actualizar peso y grasa en pacientes
    $update = $conexion->prepare("UPDATE pacientes SET peso_actual = ?, porcentaje_grasa = ? WHERE id_px = ?");
    $update->bind_param("ddi", $peso_actual, $grasa, $id_px);
    $update->execute();
    $update->close();

    echo "<script>alert('Cita registrada y datos del paciente actualizados.'); window.location.href='admin_dashboard.php';</script>";



    $id_px = $_GET['id_px'];

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


// Obtener pacientes para el formulario
$pacientes = $conexion->query("SELECT id_px, nombre FROM pacientes ORDER BY nombre");

if (isset($_GET['id_px'])) {
    $id_px = $_GET['id_px'];

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">
    <h3>Registrar Cita de Paciente</h3>
    <form method="POST" class="row g-3">

        <div class="col-md-6">
            <label>Paciente</label>
            <select name="id_px" id="id_px" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while ($row = $pacientes->fetch_assoc()): ?>
                    <option value="<?= $row['id_px'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Fecha de la Cita</label>
            <input type="date" name="fecha_cita" class="form-control" required>
        </div>

        <?php
        $campos = ['biceps', 'triceps', 'cintura', 'gluteo', 'pierna', 'espalda', 'peso_actual', 'porcentaje_grasa'];
        foreach ($campos as $campo):
        ?>
            <div class="col-md-6">
                <label><?= ucfirst(str_replace("_", " ", $campo)) ?></label>
                <input type="number" step="0.01" name="<?= $campo ?>" class="form-control" required>
            </div>
        <?php endforeach; ?>

        <div class="col-12">
            <button class="btn btn-primary">Registrar Cita</button>
            <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Regresar</a>

        </div>

    </form>
<?php if(isset($_GET['id_px'])){?>
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
<?php }?>

</body>

</html>
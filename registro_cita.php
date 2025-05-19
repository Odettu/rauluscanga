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
    $paciente_id = $_POST['id_px'];
    $fecha_cita = $_POST['fecha_cita'];
    $biceps = $_POST['biceps'];
    $triceps = $_POST['triceps'];
    $cintura = $_POST['cintura'];
    $gluteo = $_POST['gluteo'];
    $pierna = $_POST['pierna'];
    $espalda = $_POST['espalda'];
    $peso = $_POST['peso'];
    $grasa = $_POST['porcentaje_grasa'];

    // Insertar en la tabla citas
    $stmt = $conexion->prepare("INSERT INTO citas 
        (paciente_id, fecha_cita, biceps, triceps, cintura, gluteo, pierna, espalda, peso, porcentaje_grasa)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddddddd", $paciente_id, $fecha_cita, $biceps, $triceps, $cintura, $gluteo, $pierna, $espalda, $peso, $grasa);
    $stmt->execute();
    $stmt->close();

    // Actualizar peso y grasa en pacientes
    $update = $conexion->prepare("UPDATE pacientes SET peso_actual = ?, porcentaje_grasa = ? WHERE id = ?");
    $update->bind_param("ddi", $peso, $grasa, $paciente_id);
    $update->execute();
    $update->close();

    echo "<script>alert('Cita registrada y datos del paciente actualizados.'); window.location.href='registro_cita.php';</script>";
}

// Obtener pacientes para el formulario
$pacientes = $conexion->query("SELECT id_px, nombre FROM pacientes ORDER BY nombre");
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
            <select name="id_px" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while ($row = $pacientes->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Fecha de la Cita</label>
            <input type="date" name="fecha_cita" class="form-control" required>
        </div>

        <?php
        $campos = ['biceps', 'triceps', 'cintura', 'gluteo', 'pierna', 'espalda', 'peso', 'porcentaje_grasa'];
        foreach ($campos as $campo):
        ?>
            <div class="col-md-6">
                <label><?= ucfirst(str_replace("_", " ", $campo)) ?></label>
                <input type="number" step="0.01" name="<?= $campo ?>" class="form-control" required>
            </div>
        <?php endforeach; ?>

        <div class="col-12">
            <button class="btn btn-primary">Registrar Cita</button>
        </div>
    </form>
</body>
</html>

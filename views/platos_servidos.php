<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PlatosServidosController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';
require_once __DIR__ . '/../controllers/MatriculaLimiteController.php';

$auth = new AuthController();
$platosServidosController = new PlatosServidosController();
$asistenciaController = new AsistenciaController();
$matriculaLimiteController = new MatriculaLimiteController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha_actual = date('Y-m-d');
if ($fecha > $fecha_actual) {
    $fecha = $fecha_actual;
    $mensaje = '<div class="alert alert-warning">La fecha no puede ser mayor a la fecha actual. Se ha ajustado al día de hoy.</div>';
}
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'estudiante';
$grado = isset($_GET['grado']) ? $_GET['grado'] : null;

// Get available grados only if tipo is estudiante
$grados = $tipo === 'estudiante' ? $asistenciaController->getGrados() : null;

// Debug info: count attendance records per tipo for selected date
$attendanceDataAll = $asistenciaController->getAsistenciasPorFecha($fecha);
$attendanceCountByTipo = ['estudiante' => 0, 'docente' => 0, 'otros' => 0];
foreach ($attendanceDataAll as $attendance) {
    if (isset($attendanceCountByTipo[$attendance['tipo']])) {
        $attendanceCountByTipo[$attendance['tipo']]++;
    }
}

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'registrar_platos') {
        // Restrict modifications for 'base' role
        if ($auth->checkRole(['base'])) {
            $mensaje = '<div class="alert alert-danger">No tiene permisos para modificar los platos servidos.</div>';
        } else {
            $platos_servidos = $_POST['platos_servidos'];
            $platos_devueltos = $_POST['platos_devueltos'];
            $tipo = $_POST['tipo'] ?? 'estudiante';
            $grado = $_POST['grado'] ?? null;
            
            if ($platosServidosController->registrarPlatos($fecha, $platos_servidos, $platos_devueltos, $tipo, $grado)) {
                $mensaje = '<div class="alert alert-success">Platos registrados correctamente</div>';
            } else {
                $mensaje = '<div class="alert alert-danger">Error al registrar los platos</div>';
            }
        }
    }
}

// Fetch saved platos servidos data for the date
$savedPlatos = $platosServidosController->getPlatosByFecha($fecha);

// Map saved platos by id_matricula for easy lookup
$savedPlatosMap = [];
foreach ($savedPlatos as $plato) {
    $savedPlatosMap[$plato['id_matricula']] = $plato;
}

// If no saved platos servidos data, fetch attendance data for the date
$attendanceDataMap = [];
if (empty($savedPlatos)) {
    $attendanceData = $asistenciaController->getAsistenciasPorFecha($fecha);
    foreach ($attendanceData as $attendance) {
        $attendanceDataMap[$attendance['id_matricula']] = $attendance;
    }
}

// Get matriculas for selected tipo and grado
$matriculasForPlatos = $asistenciaController->getMatriculas($tipo, $grado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar Platos Servidos y Devueltos - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/asistencia.css" />
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Registrar Platos Servidos y Devueltos</h2>
            <?php echo $mensaje; ?>


            <form method="GET" class="mb-4">
                <div class="filters">
                    <div class="form-group">
                        <label for="fecha">Seleccionar Fecha:</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" max="<?php echo date('Y-m-d'); ?>" class="form-control" onchange="this.form.submit()">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <select id="tipo" name="tipo" class="form-control" onchange="this.form.submit()">
                             <option value="estudiante" <?php echo $tipo === 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                            <option value="docente" <?php echo $tipo === 'docente' ? 'selected' : ''; ?>>Docente</option>
                            <option value="otros" <?php echo $tipo === 'otros' ? 'selected' : ''; ?>>Otros</option>
                        </select>
                    </div>
                    <?php if ($grados): ?>
                        <div class="form-group">
                            <label for="grado">Grado:</label>
                            <select id="grado" name="grado" class="form-control" onchange="this.form.submit()">
                                <option value="">Todos los grados</option>
                                <?php while ($row = $grados->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $row['grado']; ?>" <?php echo $grado === $row['grado'] ? 'selected' : ''; ?>>
                                        <?php echo $row['grado']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

            <form id="platosForm" method="POST" action="">
                <input type="hidden" name="action" value="registrar_platos">
                <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">

                <div class="form-group">
                    <label>Platos Servidos y Devueltos por Matrícula (Tipo, Grado, Sección):</label>
                    <table class="table table-bordered" id="platosTable">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Grado</th>
                                <th>Sección</th>
                                <th>Platos Servidos</th>
                                <th>Platos Devueltos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($matricula = $matriculasForPlatos->fetch(PDO::FETCH_ASSOC)) {
                                $id_matricula = $matricula['id_matricula'];
                                $platos_servidos_val = 0;
                                $platos_devueltos_val = 0;

                                if (!empty($savedPlatos)) {
                                    $platos_servidos_val = isset($savedPlatosMap[$id_matricula]) ? $savedPlatosMap[$id_matricula]['platos_servidos'] : 0;
                                    $platos_devueltos_val = isset($savedPlatosMap[$id_matricula]) ? $savedPlatosMap[$id_matricula]['platos_devueltos'] : 0;
                                } elseif (isset($attendanceDataMap[$id_matricula])) {
                                    $attendance = $attendanceDataMap[$id_matricula];
                                    $platos_servidos_val = $attendance['total_masculino'] + $attendance['total_femenino'];
                                    $platos_devueltos_val = 0;
                                }

                                echo '<tr data-tipo="' . htmlspecialchars($matricula['tipo']) . '" data-grado="' . htmlspecialchars($matricula['grado']) . '">';
                                echo '<td>' . htmlspecialchars($matricula['tipo']) . '</td>';
                                echo '<td>' . htmlspecialchars($matricula['grado']) . '</td>';
                                echo '<td>' . htmlspecialchars($matricula['seccion']) . '</td>';
                                echo '<td><input type="number" name="platos_servidos[' . $id_matricula . ']" min="0" class="form-control" value="' . $platos_servidos_val . '" ' . ($auth->checkRole(['base']) ? 'readonly' : '') . '></td>';
                                echo '<td><input type="number" name="platos_devueltos[' . $id_matricula . ']" min="0" class="form-control" value="' . $platos_devueltos_val . '" ' . ($auth->checkRole(['base']) ? 'readonly' : '') . '></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Guardar</button>
            </form>
        </div>
    </main>

    <!-- Custom modal for unsaved changes alert -->
    <div id="unsavedChangesModal" class="modal">
        <div class="modal-content warning-modal-content">
            <div class="modal-icon warning-icon">⚠️</div>
            <h2 class="warning-title">Advertencia de Seguridad</h2>
            <p class="warning-message">Hay cambios sin guardar. ¿Seguro que quieres salir?</p>
            <div class="warning-buttons">
                <button id="confirmExitBtn" class="btn btn-primary">Confirmar</button>
                <button id="cancelExitBtn" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/platos_servidos.js"></script>
</body>
</html>

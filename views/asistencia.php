    <?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';
require_once __DIR__ . '/../controllers/MatriculaLimiteController.php';

$auth = new AuthController();
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

// Get available grados for estudiantes
$grados = $tipo === 'estudiante' ? $asistenciaController->getGrados() : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Restrict modifications for 'base' role
        if ($auth->checkRole(['base'])) {
            $mensaje = '<div class="alert alert-danger">No tiene permisos para modificar la asistencia.</div>';
        } else {
            switch ($_POST['action']) {
                case 'updateMatricula':
                    $id_matricula = $_POST['id_matricula'];
                    $grado = $_POST['grado'];
                    $seccion = $_POST['seccion'];
                    if ($asistenciaController->actualizarMatricula($id_matricula, $grado, $seccion)) {
                        $mensaje = '<div class="alert alert-success">Matrícula actualizada correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al actualizar la matrícula</div>';
                    }
                    break;

                case 'updateLimites':
                    $id_matricula = $_POST['id_matricula'];
                    $limite_masculino = $_POST['limite_masculino'];
                    $limite_femenino = $_POST['limite_femenino'];
                    
                    if ($matriculaLimiteController->setLimite($id_matricula, $limite_masculino, $limite_femenino)) {
                        $mensaje = '<div class="alert alert-success">Límites actualizados correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al actualizar los límites</div>';
                    }
                    break;

                case 'updateAsistencia':
                    $nuevaFecha = $_POST['fecha'];
                    $asistencias = [];
                    $error = false;
                    $errorMessage = '';

                    if (isset($_POST['asistencia']) && is_array($_POST['asistencia'])) {
                        foreach ($_POST['asistencia'] as $id_matricula => $datos) {
                            if (isset($datos['masculino']) && isset($datos['femenino'])) {
                                // Verificar límites
                                $limite = $matriculaLimiteController->getLimite($id_matricula)->fetch(PDO::FETCH_ASSOC);
                                
                                if ($limite) {
                                    if ((int)$datos['masculino'] > $limite['limite_masculino']) {
                                        $error = true;
                                        $errorMessage = 'La asistencia masculina excede el límite establecido';
                                        break;
                                    }
                                    if ((int)$datos['femenino'] > $limite['limite_femenino']) {
                                        $error = true;
                                        $errorMessage = 'La asistencia femenina excede el límite establecido';
                                        break;
                                    }
                                }

                                $asistencias[] = [
                                    'id_matricula' => $id_matricula,
                                    'total_masculino' => (int)$datos['masculino'],
                                    'total_femenino' => (int)$datos['femenino']
                                ];
                            }
                        }
                    }

                    if ($error) {
                        $mensaje = '<div class="alert alert-danger">' . $errorMessage . '</div>';
                    } else if (!empty($asistencias) && $asistenciaController->registrarAsistencia($nuevaFecha, $asistencias)) {
                        $mensaje = '<div class="alert alert-success">Asistencia registrada correctamente</div>';
                        $fecha = $nuevaFecha;
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al registrar la asistencia</div>';
                    }
                    break;

                case 'registrar_platos':
                    $platos_servidos = $_POST['platos_servidos'];
                    $platos_devueltos = $_POST['platos_devueltos'];
                    $tipo = $_POST['tipo'] ?? 'estudiante';
                    $grado = $_POST['grado'] ?? null;
                    
                    if ($asistenciaController->registrarPlatos($fecha, $platos_servidos, $platos_devueltos, '', $tipo, $grado)) {
                        $mensaje = '<div class="alert alert-success">Platos registrados correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al registrar los platos</div>';
                    }
                    break;

                case 'registrarMatricula':
                    $data = [
                        'tipo' => $_POST['tipo'],
                        'grado' => $_POST['grado'] ?? null,
                        'seccion' => $_POST['seccion'] ?? null,
                        'lapso_academico' => $_POST['lapso_academico'] ?? null,
                        'total_masculino' => $_POST['total_masculino'],
                        'total_femenino' => $_POST['total_femenino']
                    ];
                    if ($asistenciaController->registrarMatricula($data)) {
                        $mensaje = '<div class="alert alert-success">Matrícula registrada correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al registrar la matrícula</div>';
                    }
                    break;
            }
        }
    }
}

$matriculas = $asistenciaController->getMatriculas($tipo, $grado);
$asistenciasDia = $asistenciaController->getAsistenciasPorFecha($fecha);

// Prepare asistencias data
$asistenciasData = [];
while ($asistencia = $asistenciasDia->fetch(PDO::FETCH_ASSOC)) {
    $asistenciasData[$asistencia['id_matricula']] = [
        'total_masculino' => $asistencia['total_masculino'],
        'total_femenino' => $asistencia['total_femenino']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asistencias - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/asistencia.css">
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>    
    <main class="main-content">
        <div class="card">
            <h2>Gestión de Asistencias</h2>
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
                    <?php if ($tipo === 'estudiante' && $grados): ?>
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
<?php if(!$auth->checkRole(['base'])): ?>
    <button type="button" class="btn btn-success" id="btnNuevaMatricula">
        Nueva Matrícula
    </button>
<?php endif; ?>
            <form method="POST" action="" id="asistenciaForm">
                <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                <input type="hidden" name="action" value="updateAsistencia">
                
<table id="asistenciaTable" class="table">
    <thead>
        <tr>
            <?php if ($tipo === 'estudiante'): ?>
                <th>Grado</th>
                <th>Sección</th>
                <th>Lapso Académico</th>
            <?php elseif ($tipo === 'docente'): ?>
                <th>Lapso Académico</th>
            <?php endif; ?>
            <th>Masculino</th>
            <th>Femenino</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($matricula = $matriculas->fetch(PDO::FETCH_ASSOC)): 
            $asistencia = $asistenciasData[$matricula['id_matricula']] ?? ['total_masculino' => 0, 'total_femenino' => 0];
            $limite = $matriculaLimiteController->getLimite($matricula['id_matricula'])->fetch(PDO::FETCH_ASSOC);
        ?>
            <tr data-id="<?php echo $matricula['id_matricula']; ?>">
                <?php if ($tipo === 'estudiante'): ?>
                    <td><?php echo htmlspecialchars($matricula['grado']); ?></td>
                    <td><?php echo htmlspecialchars($matricula['seccion']); ?></td>
                    <td><?php echo htmlspecialchars($matricula['lapso_academico']); ?></td>
                <?php elseif ($tipo === 'docente'): ?>
                    <td><?php echo htmlspecialchars($matricula['lapso_academico']); ?></td>
                <?php endif; ?>
                <td>
                    <input type="number" 
                           name="asistencia[<?php echo $matricula['id_matricula']; ?>][masculino]" 
                           class="form-control" 
                           min="0"
                           max="<?php echo $limite ? $limite['limite_masculino'] : ''; ?>"
                           value="<?php echo $asistencia['total_masculino']; ?>"
                           required
                           <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                    <?php if ($limite): ?>
                        <small class="text-muted">Límite: <?php echo $limite['limite_masculino']; ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <input type="number" 
                           name="asistencia[<?php echo $matricula['id_matricula']; ?>][femenino]" 
                           class="form-control" 
                           min="0"
                           max="<?php echo $limite ? $limite['limite_femenino'] : ''; ?>"
                           value="<?php echo $asistencia['total_femenino']; ?>"
                           required
                           <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                    <?php if ($limite): ?>
                        <small class="text-muted">Límite: <?php echo $limite['limite_femenino']; ?></small>
                    <?php endif; ?>
                </td>
                <td class="total-row"><?php echo $asistencia['total_masculino'] + $asistencia['total_femenino']; ?></td>
                <td>
                    <?php if(!$auth->checkRole(['base'])): ?>
                    <button type="button" class="btn btn-primary btn-edit" 
                            onclick="editarMatricula('<?php echo $matricula['id_matricula']; ?>', 
                                                    '<?php echo $matricula['grado']; ?>', 
                                                    '<?php echo $matricula['seccion']; ?>')">
                        Editar
                    </button>
                    <button type="button" class="edit-limits" 
                            onclick="editarLimites('<?php echo $matricula['id_matricula']; ?>', 
                                                 '<?php echo $limite ? $limite['limite_masculino'] : 0; ?>', 
                                                 '<?php echo $limite ? $limite['limite_femenino'] : 0; ?>')">
                        Editar Límites
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-primary btn-edit" disabled>
                        Editar
                    </button>
                    <button type="button" class="edit-limits" disabled>
                        Editar Límites
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
            <tfoot>
        <tr>
            <?php if ($tipo === 'estudiante'): ?>
                <th colspan="3">Total General</th>
            <?php elseif ($tipo === 'docente'): ?>
                <th colspan="1">Total General</th>
            <?php else: ?>
                <th colspan="3">Total General</th>
            <?php endif; ?>
            <th id="total-masculino">0</th>
            <th id="total-femenino">0</th>
            <th id="total-general">0</th>
            <th></th>
        </tr>
    </tfoot>
</table>

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                    Guardar Asistencia
                </button>
            </div>
            </form>

            <?php
            // Store grados options in an array to reuse
            $gradosOptions = [];
            if ($tipo === 'estudiante' && $grados) {
                while ($row = $grados->fetch(PDO::FETCH_ASSOC)) {
                    $gradosOptions[] = $row;
                }
            }
            ?>

     <!-- Modal para editar matrícula -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Matrícula</h3>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="updateMatricula">
                <input type="hidden" name="id_matricula" id="edit-id">
                <div class="form-group">
                    <label for="edit-grado">Grado:</label>
                    <input type="text" id="edit-grado" name="grado" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>
                <div class="form-group">
                    <label for="edit-seccion">Sección:</label>
                    <input type="text" id="edit-seccion" name="seccion" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>
                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/asistencia.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('asistenciaForm');
            let isFormDirty = false;

            // Mark form as dirty on input change
            form.querySelectorAll('input, select, textarea').forEach(element => {
                element.addEventListener('change', () => {
                    isFormDirty = true;
                });
            });

            // Reset dirty flag on form submit
            form.addEventListener('submit', () => {
                isFormDirty = false;
            });

        });
    </script>
    
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

    <!-- Modal para editar límites -->
    <div id="limitesModal" class="modal">
        <div class="modal-content">
            <h3>Editar Límites de Matrícula</h3>
            <form id="limitesForm" method="POST" action="">
                <input type="hidden" name="action" value="updateLimites">
                <input type="hidden" name="id_matricula" id="limites-id">
                <div class="form-group">
                    <label for="limite_masculino">Límite Masculino:</label>
                    <input type="number" id="limite_masculino" name="limite_masculino" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label for="limite_femenino">Límite Femenino:</label>
                    <input type="number" id="limite_femenino" name="limite_femenino" class="form-control" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('limitesModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[type="number"]');
            
            function updateTotals() {
                let totalMasculino = 0;
                let totalFemenino = 0;

                document.querySelectorAll('#asistenciaTable tbody tr').forEach(row => {
                    const masculino = parseInt(row.querySelector('input[name*="[masculino]"]').value) || 0;
                    const femenino = parseInt(row.querySelector('input[name*="[femenino]"]').value) || 0;
                    const total = masculino + femenino;
                    
                    row.querySelector('.total-row').textContent = total;
                    totalMasculino += masculino;
                    totalFemenino += femenino;
                });

                document.getElementById('total-masculino').textContent = totalMasculino;
                document.getElementById('total-femenino').textContent = totalFemenino;
                document.getElementById('total-general').textContent = totalMasculino + totalFemenino;
            }

            inputs.forEach(input => {
                input.addEventListener('input', updateTotals);
            });

            updateTotals();
        });

        function editarMatricula(id, grado, seccion) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-grado').value = grado;
            document.getElementById('edit-seccion').value = seccion;
            document.getElementById('editModal').style.display = 'block';
        }

        function editarLimites(id, limite_masculino, limite_femenino) {
            document.getElementById('limites-id').value = id;
            document.getElementById('limite_masculino').value = limite_masculino;
            document.getElementById('limite_femenino').value = limite_femenino;
            document.getElementById('limitesModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Cerrar modales cuando se hace clic fuera de ellos
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
    
    <!-- Modal para nueva matrícula -->
    <div id="nuevaMatriculaModal" class="modal">
        <div class="modal-content">
            <h3>Nueva Matrícula</h3>
            <form id="nuevaMatriculaForm" method="POST" action="">
                <input type="hidden" name="action" value="registrarMatricula">

                <div class="form-group">
                    <label for="tipoNuevaMatricula">Tipo:</label>
                    <select id="tipoNuevaMatricula" name="tipo" class="form-control" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="estudiante">Estudiante</option>
                        <option value="docente">Docente</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div id="camposEstudiante" style="display:none;">
                    <div class="form-group">
                        <label for="grado">Grado:</label>
                        <input type="text" id="grado" name="grado" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="seccion">Sección:</label>
                        <input type="text" id="seccion" name="seccion" class="form-control">
                    </div>
                </div>

                <div class="form-group" id="lapsoAcademicoGroup" style="display:none;">
                    <label for="lapso_academico">Lapso Académico:</label>
                    <input type="text" id="lapso_academico" name="lapso_academico" class="form-control">
                </div>

                <div class="form-group">
                    <label for="total_masculino">Total Masculino:</label>
                    <input type="number" id="total_masculino" name="total_masculino" class="form-control" min="0" required>
                </div>

                <div class="form-group">
                    <label for="total_femenino">Total Femenino:</label>
                    <input type="number" id="total_femenino" name="total_femenino" class="form-control" min="0" required>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('nuevaMatriculaModal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('btnNuevaMatricula').addEventListener('click', function() {
            document.getElementById('nuevaMatriculaModal').style.display = 'block';

            // Attach event listener to tipoNuevaMatricula select inside modal
            var tipoSelect = document.getElementById('tipoNuevaMatricula');
            var camposEstudiante = document.getElementById('camposEstudiante');

            function toggleCamposEstudiante() {
                var tipo = tipoSelect.value;
                var lapsoAcademicoGroup = document.getElementById('lapsoAcademicoGroup');
                var gradoInput = document.getElementById('grado');
                var seccionInput = document.getElementById('seccion');
                var lapsoInput = document.getElementById('lapso_academico');
                if (tipo === 'estudiante') {
                    camposEstudiante.style.display = 'block';
                    lapsoAcademicoGroup.style.display = 'block';
                    gradoInput.required = true;
                    gradoInput.disabled = false;
                    lapsoInput.required = false;
                } else if (tipo === 'docente') {
                    camposEstudiante.style.display = 'none';
                    lapsoAcademicoGroup.style.display = 'block';
                    gradoInput.required = false;
                    gradoInput.disabled = true;
                    gradoInput.value = '';
                    seccionInput.value = '';
                    lapsoInput.required = true;
                } else if (tipo === 'otros') {
                    camposEstudiante.style.display = 'none';
                    lapsoAcademicoGroup.style.display = 'none';
                    gradoInput.required = false;
                    gradoInput.disabled = true;
                    gradoInput.value = '';
                    seccionInput.value = '';
                    lapsoInput.value = '';
                    lapsoInput.required = false;
                } else {
                    camposEstudiante.style.display = 'none';
                    lapsoAcademicoGroup.style.display = 'none';
                    lapsoInput.required = false;
                }
            }

            tipoSelect.addEventListener('change', toggleCamposEstudiante);

            // Set initial visibility when modal opens
            toggleCamposEstudiante();

            // Add form validation on submit
            var nuevaMatriculaForm = document.getElementById('nuevaMatriculaForm');
            nuevaMatriculaForm.addEventListener('submit', function(event) {
                var tipo = tipoSelect.value;
                var gradoInput = document.getElementById('grado');
                if (tipo === 'estudiante' && gradoInput.value.trim() === '') {
                    event.preventDefault();
                    alert('Por favor, complete el campo Grado para estudiantes.');
                    gradoInput.focus();
                }
            });
        });

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

    </script>
</body>
</html>

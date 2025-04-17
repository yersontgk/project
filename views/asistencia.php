<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';

$auth = new AuthController();
$asistenciaController = new AsistenciaController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'estudiante';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
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
            case 'updateAsistencia':
                $nuevaFecha = $_POST['fecha'];
                $asistencias = [];
                if (isset($_POST['asistencia']) && is_array($_POST['asistencia'])) {
                    foreach ($_POST['asistencia'] as $id_matricula => $datos) {
                        if (isset($datos['masculino']) && isset($datos['femenino'])) {
                            $asistencias[] = [
                                'id_matricula' => $id_matricula,
                                'total_masculino' => (int)$datos['masculino'],
                                'total_femenino' => (int)$datos['femenino']
                            ];
                        }
                    }
                }
                if (!empty($asistencias) && $asistenciaController->registrarAsistencia($nuevaFecha, $asistencias)) {
                    $mensaje = '<div class="alert alert-success">Asistencia registrada correctamente</div>';
                    $fecha = $nuevaFecha;
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al registrar la asistencia. Verifique los datos ingresados.</div>';
                }
                break;
            case 'updateTotals':
                $id_matricula = $_POST['id_matricula'];
                $total_masculino = (int)$_POST['total_masculino'];
                $total_femenino = (int)$_POST['total_femenino'];
                if ($asistenciaController->actualizarTotales($id_matricula, $total_masculino, $total_femenino)) {
                    $mensaje = '<div class="alert alert-success">Totales actualizados correctamente</div>';
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al actualizar los totales</div>';
                }
                break;
        }
    }
}

$matriculas = $asistenciaController->getMatriculas($tipo);
$asistenciasDia = $asistenciaController->getAsistenciasPorFecha($fecha);
$factorGramaje = $asistenciaController->getFactorGramaje();

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
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
        }
        .gramaje-section {
            margin-top: 2rem;
            padding: 1rem;
            border-top: 1px solid #ddd;
        }
        .edit-totals {
            margin-left: 0.5rem;
            padding: 0.25rem 0.5rem;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-totals:hover {
            background-color: #357abd;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3>Comedor Escolar</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="asistencia.php">Asistencia</a></li>
            <li><a href="menu.php">Menú</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="reportes.php">Reportes</a></li>
            <?php if($auth->checkRole(['admin'])): ?>
                <li><a href="usuarios.php">Usuarios</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="card">
            <h2>Gestión de Asistencias</h2>
            <?php echo $mensaje; ?>

            <form method="GET" class="mb-4">
                <div class="form-group">
                    <label for="fecha">Seleccionar Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" class="form-control" onchange="this.form.submit()">
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" class="form-control" onchange="this.form.submit()">
                        <option value="estudiante" <?php echo $tipo === 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                        <option value="docente" <?php echo $tipo === 'docente' ? 'selected' : ''; ?>>Docente</option>
                        <option value="otros" <?php echo $tipo === 'otros' ? 'selected' : ''; ?>>Otros</option>
                    </select>
                </div>
            </form>

            <form method="POST" action="" id="asistenciaForm">
                <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                <input type="hidden" name="action" value="updateAsistencia">
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Grado</th>
                            <th>Sección</th>
                            <th>Masculino</th>
                            <th>Femenino</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($matricula = $matriculas->fetch(PDO::FETCH_ASSOC)): 
                            $asistencia = $asistenciasData[$matricula['id_matricula']] ?? ['total_masculino' => 0, 'total_femenino' => 0];
                        ?>
                            <tr data-id="<?php echo $matricula['id_matricula']; ?>">
                                <td><?php echo htmlspecialchars($matricula['grado']); ?></td>
                                <td><?php echo htmlspecialchars($matricula['seccion']); ?></td>
                                <td>
                                    <input type="number" 
                                           name="asistencia[<?php echo $matricula['id_matricula']; ?>][masculino]" 
                                           class="form-control" 
                                           min="0" 
                                           value="<?php echo $asistencia['total_masculino']; ?>"
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="asistencia[<?php echo $matricula['id_matricula']; ?>][femenino]" 
                                           class="form-control" 
                                           min="0" 
                                           value="<?php echo $asistencia['total_femenino']; ?>"
                                           required>
                                </td>
                                <td class="total-row"><?php echo $asistencia['total_masculino'] + $asistencia['total_femenino']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-edit" 
                                            onclick="editarMatricula('<?php echo $matricula['id_matricula']; ?>', 
                                                                    '<?php echo $matricula['grado']; ?>', 
                                                                    '<?php echo $matricula['seccion']; ?>')">
                                        Editar
                                    </button>
                                    <button type="button" class="edit-totals" 
                                            onclick="editarTotales('<?php echo $matricula['id_matricula']; ?>')">
                                        Editar Totales
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total General</th>
                            <th id="total-masculino">0</th>
                            <th id="total-femenino">0</th>
                            <th id="total-general">0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <button type="submit" class="btn btn-primary">Guardar Asistencia</button>
            </form>

            <div class="gramaje-section">
                <h3>Cálculo de Gramaje</h3>
                <div class="form-group">
                    <label for="factor-gramaje">Factor de Gramaje:</label>
                    <input type="number" id="factor-gramaje" class="form-control" value="<?php echo $factorGramaje; ?>" 
                           <?php echo !$auth->checkRole(['admin']) ? 'readonly' : ''; ?>>
                    <?php if($auth->checkRole(['admin'])): ?>
                        <button type="button" class="btn btn-primary" onclick="actualizarFactorGramaje()">
                            Actualizar Factor
                        </button>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-primary" onclick="calcularGramaje()">Calcular Gramaje</button>
                <div id="resultado-gramaje" class="mt-3"></div>
            </div>
        </div>
    </main>

    <!-- Modal para editar matrícula -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Matrícula</h3>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="updateMatricula">
                <input type="hidden" name="id_matricula" id="edit-id">
                <div class="form-group">
                    <label for="edit-grado">Grado:</label>
                    <input type="text" id="edit-grado" name="grado" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit-seccion">Sección:</label>
                    <input type="text" id="edit-seccion" name="seccion" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar totales -->
    <div id="totalsModal" class="modal">
        <div class="modal-content">
            <h3>Editar Totales</h3>
            <form id="totalsForm" method="POST" action="">
                <input type="hidden" name="action" value="updateTotals">
                <input type="hidden" name="id_matricula" id="totals-id">
                <div class="form-group">
                    <label for="total-masculino-edit">Total Masculino:</label>
                    <input type="number" id="total-masculino-edit" name="total_masculino" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label for="total-femenino-edit">Total Femenino:</label>
                    <input type="number" id="total-femenino-edit" name="total_femenino" class="form-control" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('totalsModal')">Cancelar</button>
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

                document.querySelectorAll('tbody tr').forEach(row => {
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

        function editarTotales(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const masculino = row.querySelector('input[name*="[masculino]"]').value;
            const femenino = row.querySelector('input[name*="[femenino]"]').value;

            document.getElementById('totals-id').value = id;
            document.getElementById('total-masculino-edit').value = masculino;
            document.getElementById('total-femenino-edit').value = femenino;
            document.getElementById('totalsModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function calcularGramaje() {
            const totalGeneral = parseInt(document.getElementById('total-general').textContent);
            const factorGramaje = parseFloat(document.getElementById('factor-gramaje').value);
            
            const resultado = totalGeneral * factorGramaje;
            const resultadoDiv = document.getElementById('resultado-gramaje');
            resultadoDiv.innerHTML = `
                <div class="alert alert-success">
                    <h4>Resultados del cálculo:</h4>
                    <p>Total de personas: ${totalGeneral}</p>
                    <p>Factor de gramaje: ${factorGramaje}</p>
                    <p>Gramaje total: ${resultado.toFixed(2)} gramos</p>
                </div>
            `;
        }

        function actualizarFactorGramaje() {
            const nuevoFactor = document.getElementById('factor-gramaje').value;
            fetch('actualizar_factor_gramaje.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `factor=${nuevoFactor}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Factor de gramaje actualizado correctamente');
                } else {
                    alert('Error al actualizar el factor de gramaje');
                }
            });
        }

        // Cerrar modales cuando se hace clic fuera de ellos
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
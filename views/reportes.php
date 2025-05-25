<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ReporteController.php';

$auth = new AuthController();
$reporteController = new ReporteController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

$hoy = date('Y-m-d');
// Clamp fecha_inicio and fecha_fin to today if they exceed today
if ($fecha_inicio > $hoy) {
    $fecha_inicio = $hoy;
}
if ($fecha_fin > $hoy) {
    $fecha_fin = $hoy;
}

// Obtener datos para los reportes
$resumenAsistencia = $reporteController->getResumenAsistencia($fecha_inicio, $fecha_fin);
$asistenciaPorDia = $reporteController->getAsistenciaPorDia($fecha_inicio, $fecha_fin);
$consumoInsumos = $reporteController->getConsumoInsumos($fecha_inicio, $fecha_fin);
$menusPopulares = $reporteController->getMenusPopulares($fecha_inicio, $fecha_fin);
$averageAttendancePerMenu = $reporteController->getAverageAttendancePerMenu($fecha_inicio, $fecha_fin);
$estadisticasGenerales = $reporteController->getEstadisticasGenerales($fecha_inicio, $fecha_fin);

// New data for form automation
$usuariosCarga = $reporteController->getUsuariosQueRealizaronGramaje($fecha_inicio, $fecha_fin);
$cantidadCocineras = $reporteController->getCantidadCocineras();
$matriculaTotal = $reporteController->getMatriculaTotal();
$totalAsistencia = $reporteController->getTotalAsistencia($fecha_inicio, $fecha_fin);
$totalPlatosServidos = $reporteController->getTotalPlatosServidos($fecha_inicio, $fecha_fin);
$platosServidosDocentes = $reporteController->getPlatosServidosDocentes($fecha_inicio, $fecha_fin);
$casosVulnerables = $reporteController->getCasosVulnerables();
$menuDelDia = $reporteController->getMenuDelDia($fecha_inicio, $fecha_fin);

// Calculate estudiantes que repiten (excedentes platos servidos sobre asistencia)
$estudiantesRepiten = 0;
if ($totalPlatosServidos > $totalAsistencia) {
    $estudiantesRepiten = $totalPlatosServidos - $totalAsistencia;
}

// Total comensales = estudiantes + docentes + otros
$totalComensales = ($resumenAsistencia['total_estudiantes'] ?? 0) + ($resumenAsistencia['total_docentes'] ?? 0) + $casosVulnerables;

// Preparar datos para gráficos
$datosAsistencia = [];
$datosConsumo = [];

while ($row = $asistenciaPorDia->fetch(PDO::FETCH_ASSOC)) {
    $datosAsistencia[] = [
        'fecha' => $row['fecha'],
        'total' => $row['total_asistencia'],
        'tipo' => $row['tipo']
    ];
}

while ($row = $consumoInsumos->fetch(PDO::FETCH_ASSOC)) {
    $datosConsumo[] = [
        'producto' => $row['producto'],
        'total' => $row['total_consumido'],
        'unidad' => $row['unidad']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/reportes.css">
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>

    <main class="main-content">
        <div class="report-container">
            <div class="report-header">
                <h2>Reportes del Sistema</h2>
                <form method="GET" class="mb-4" id="filterForm">
                    <div class="form-grid" style="align-items: flex-end;">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?php echo $fecha_inicio; ?>" max="<?php echo $hoy; ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" 
                                   value="<?php echo $fecha_fin; ?>" max="<?php echo $hoy; ?>" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 0.7;">
                            <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Filtrar</button>
                            <button type="button" id="reporteDiarioBtn" class="btn btn-secondary">Reporte Diario</button>
                        </div>
                    </div>
                </form>
                <script>
                    document.getElementById('reporteDiarioBtn').addEventListener('click', function() {
                        const hoy = new Date().toISOString().split('T')[0];
                        document.getElementById('fecha_inicio').value = hoy;
                        document.getElementById('fecha_fin').value = hoy;
                        document.getElementById('filterForm').submit();
                    });
                </script>
            </div>

            <div class="report-nav">
                <button class="tab-btn active" data-tab="resumen">Resumen General</button>
                <button class="tab-btn" data-tab="asistencia">Asistencia</button>
                <button class="tab-btn" data-tab="consumo">Consumo</button>
                <button class="tab-btn" data-tab="menus">Menús</button>
                <button class="tab-btn" data-tab="formulario">Formulario</button>
            </div>

            <!-- Resumen General -->
            <div id="resumen" class="report-section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Estudiantes</h3>
                        <div class="stat-value"><?php echo $resumenAsistencia['total_estudiantes']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Docentes</h3>
                        <div class="stat-value"><?php echo $resumenAsistencia['total_docentes']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Promedio Diario</h3>
                        <div class="stat-value"><?php echo round($resumenAsistencia['promedio_diario']); ?></div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="resumenChart"></canvas>
                </div>
            </div>

            <!-- Asistencia -->
            <div id="asistencia" class="report-section">
                <div class="chart-container">
                    <canvas id="asistenciaChart"></canvas>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Días Registrados</h3>
                        <div class="stat-value"><?php echo $resumenAsistencia['total_dias']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Promedio Estudiantes</h3>
                        <div class="stat-value"><?php echo round($resumenAsistencia['total_estudiantes'] / $resumenAsistencia['total_dias']); ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Promedio Docentes</h3>
                        <div class="stat-value"><?php echo round($resumenAsistencia['total_docentes'] / $resumenAsistencia['total_dias']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Consumo -->
            <div id="consumo" class="report-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Productos</h3>
                        <div class="stat-value"><?php echo $estadisticasGenerales['total_productos']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Productos Bajo Stock</h3>
                        <div class="stat-value"><?php echo $estadisticasGenerales['productos_bajo_stock']; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Consumo Total</h3>
                        <div class="stat-value"><?php echo round($estadisticasGenerales['consumo_total'], 2); ?> kg</div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="consumoChart"></canvas>
                </div>
            </div>

            <!-- Menús -->
            <div id="menus" class="report-section">
                <div class="chart-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menú</th>
                                <th>Veces Utilizado</th>
                                <th>Última Fecha</th>
                                <th>Promedio Asistencia</th>
                            </tr>
                        </thead>
                            <tbody>
                                <?php while ($menu = $menusPopulares->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($menu['menu']); ?></td>
                                        <td><?php echo $menu['veces_utilizado']; ?></td>
                                        <td><?php echo $menu['ultima_fecha']; ?></td>
                                        <td><?php 
                                            $promedio = isset($averageAttendancePerMenu[$menu['id_menu']]) ? $averageAttendancePerMenu[$menu['id_menu']] : 0;
                                            echo round($promedio);
                                        ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario -->
            <div id="formulario" class="report-section">
                <div class="form-section">
                    <form id="reportForm" class="form-grid">
                        <div class="form-group">
                            <label for="parroquia">PARROQUIAS</label>
                            <select id="parroquia" name="parroquia" class="form-control" required>
                                <option value="">Elegir...</option>
                                <!-- Opciones dinámicas -->
                            </select>
                        </div>

                        <!-- Removed fecha_ingesta field -->

                        <div class="form-group">
                            <label for="responsable">QUIÉN REALIZÓ LA CARGA</label>
                            <select id="responsable" name="responsable" class="form-control" required>

                                <?php foreach ($usuariosCarga as $usuario): ?>
                                    <option value="<?php echo htmlspecialchars($usuario['username']); ?>">
                                        <?php echo htmlspecialchars($usuario['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nombre_responsable">NOMBRE Y APELLIDO DEL RESPONSABLE</label>
                            <select id="nombre_responsable" name="nombre_responsable" class="form-control" required>
                                
                                <?php foreach ($usuariosCarga as $usuario): ?>
                                    <option value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>">
                                        <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cedula">CÉDULA DEL RESPONSABLE</label>
                            <input type="text" id="cedula" name="cedula" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">NÚMERO TELEFÓNICO</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo_espacio">TIPO DE ESPACIO EDUCATIVO</label>
                            <select id="tipo_espacio" name="tipo_espacio" class="form-control" required>
                                <option value="">Elegir...</option>
                                <!-- Opciones dinámicas -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="turno">TURNO</label>
                            <select id="turno" name="turno" class="form-control" required>
                                <option value="">Elegir...</option>
                                <option value="manana">Mañana</option>
                                <option value="tarde">Tarde</option>
                            </select>
                        </div>

                        <!-- Replaced desayuno, almuerzo, merienda with realizaron ingesta -->
                        <div class="form-group">
                            <label>REALIZARON INGESTA</label>
                            <select name="realizaron_ingesta" class="form-control" required>
                                <option value="">Elegir...</option>
                                <option value="si" <?php echo $reporteController->huboRestaInsumos($fecha_inicio, $fecha_fin) ? 'selected' : ''; ?>>Sí</option>
                                <option value="no" <?php echo !$reporteController->huboRestaInsumos($fecha_inicio, $fecha_fin) ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>

                        <!-- Campos para información institucional -->
                        <div class="form-group">
                            <label>NOMBRE DE LA INSTITUCIÓN EDUCATIVA</label>
                            <input type="text" name="institucion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>CÓDIGO DEA</label>
                            <input type="text" name="codigo_dea" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>NIVEL QUE ATIENDE</label>
                            <select name="nivel" class="form-control" required>
                                <option value="">Elegir...</option>
                                <!-- Opciones dinámicas -->
                            </select>
                        </div>

                        <!-- Campos para menú y personal -->
                        <div class="form-group">
                            <label>MENÚ DEL DÍA</label>
                            <input type="text" name="menu_dia" class="form-control" value="<?php echo htmlspecialchars($menuDelDia); ?>" required readonly>
                        </div>

                        <div class="form-group">
                            <label>CANTIDAD DE COCINERAS(OS)</label>
                            <select name="cant_cocineros" class="form-control" required>
                                <?php if ($cantidadCocineras > 0): ?>
                                    <?php for ($i = 1; $i <= $cantidadCocineras; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <option value="">No hay cocineras/os disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>COCINERAS(OS) INASISTENTES</label>
                            <select name="cocineros_inasistentes" class="form-control" required>
                                <option value="">Elegir...</option>
                                <?php for ($i = 0; $i <= $cantidadCocineras; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Campos para matrícula y asistencia -->
                        <div class="form-group">
                            <label>MATRÍCULA INSCRITA</label>
                            <input type="number" name="matricula_inscrita" class="form-control" value="<?php echo $matriculaTotal; ?>" required readonly>
                        </div>

                        <div class="form-group">
                            <label>MATRÍCULA ASISTENTE</label>
                            <input type="number" name="matricula_asistente" class="form-control" value="<?php echo $totalAsistencia; ?>" required readonly>
                        </div>

                        <div class="form-group">
                            <label>ESTUDIANTES QUE RECIBIERON LA INGESTA</label>
                            <input type="number" name="estudiantes_ingesta" class="form-control" value="<?php echo $totalPlatosServidos; ?>" required readonly>
                        </div>

                        <!-- Campos para repetición y personal -->
                        <div class="form-group">
                            <label>ESTUDIANTES QUE REPITEN</label>
                            <select name="estudiantes_repiten" class="form-control" required>
                                <?php for ($i = 0; $i <= 50; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == $estudiantesRepiten) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>DOCENTES QUE RECIBEN</label>
                            <select name="docentes_reciben" class="form-control" required>
                                <?php for ($i = 0; $i <= max(20, $platosServidosDocentes); $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == $platosServidosDocentes) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Removed administrativos, obreros, cocineros que reciben -->

                        <div class="form-group">
                            <label>CASOS VULNERABLES</label>
                            <?php 
                                $platosOtros = $reporteController->getPlatosServidosOtros($fecha_inicio, $fecha_fin);
                            ?>
                            <select name="casos_vulnerables" class="form-control" required>
                                <?php for ($i = 0; $i <= max(20, $platosOtros); $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == $platosOtros) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>TOTAL DE COMENSALES</label>
                            <input type="number" name="total_comensales" class="form-control" value="<?php echo $totalComensales; ?>" required readonly>
                        </div>

                        <!-- Campos para incidencias y observaciones -->
                        <div class="form-group">
                            <label>INCIDENCIAS</label>
                            <select name="incidencias" class="form-control" required>
                                <option value="">Elegir...</option>
                                <option value="ninguna">Ninguna</option>
                                <option value="falta_insumos">Falta de insumos</option>
                                <option value="falla_equipos">Falla de equipos</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>OBSERVACIONES</label>
                            <textarea name="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <div class="actions-section">
                <button onclick="exportToPDF()" class="btn btn-primary">Exportar a PDF</button>
                <button onclick="exportToExcel()" class="btn btn-primary">Exportar a Excel</button>
                <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.datosAsistencia = <?php echo json_encode($datosAsistencia); ?>;
        window.datosConsumo = <?php echo json_encode($datosConsumo); ?>;
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
    <script src="../assets/js/reportes.js"></script>
</body>
</html>

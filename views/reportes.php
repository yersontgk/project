<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';
require_once __DIR__ . '/../controllers/MenuController.php';
require_once __DIR__ . '/../controllers/InventarioController.php';

$auth = new AuthController();
$asistenciaController = new AsistenciaController();
$menuController = new MenuController();
$inventarioController = new InventarioController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .report-section {
            margin-bottom: 2rem;
        }
        .report-filters {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        .report-filters form {
            display: flex;
            gap: 1rem;
            align-items: end;
        }
        .report-content {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }
        .stat-card h4 {
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .chart-container {
            margin-top: 1.5rem;
            height: 300px;
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
            <h2>Reportes del Sistema</h2>

            <div class="report-filters">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" 
                               value="<?php echo $fecha_inicio; ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" 
                               value="<?php echo $fecha_fin; ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>
            </div>

            <div class="report-section">
                <h3>Resumen de Asistencia</h3>
                <div class="report-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>Total Estudiantes</h4>
                            <div class="value">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Total Docentes</h4>
                            <div class="value">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Promedio Diario</h4>
                            <div class="value">0</div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <!-- Aquí irá el gráfico de asistencia -->
                        <p class="text-center">Gráfico de asistencia por día</p>
                    </div>
                </div>
            </div>

            <div class="report-section">
                <h3>Consumo de Insumos</h3>
                <div class="report-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>Total Productos</h4>
                            <div class="value">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Productos Bajo Stock</h4>
                            <div class="value">0</div>
                        </div>
                        <div class="stat-card">
                            <h4>Consumo Total</h4>
                            <div class="value">0 kg</div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <!-- Aquí irá el gráfico de consumo -->
                        <p class="text-center">Gráfico de consumo por producto</p>
                    </div>
                </div>
            </div>

            <div class="report-section">
                <h3>Menús más Utilizados</h3>
                <div class="report-content">
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
                            <tr>
                                <td colspan="4" class="text-center">No hay datos disponibles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="report-section">
                <h3>Acciones Disponibles</h3>
                <div class="report-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <button class="btn btn-primary" onclick="exportarPDF()">
                            Exportar a PDF
                        </button>
                        <button class="btn btn-primary" onclick="exportarExcel()">
                            Exportar a Excel
                        </button>
                        <button class="btn btn-primary" onclick="imprimirReporte()">
                            Imprimir Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function exportarPDF() {
            alert('Funcionalidad de exportar a PDF en desarrollo');
        }

        function exportarExcel() {
            alert('Funcionalidad de exportar a Excel en desarrollo');
        }

        function imprimirReporte() {
            window.print();
        }
    </script>
</body>
</html>
<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <h2>Bienvenido al Sistema de Gestión del Comedor Escolar</h2>
            <p>Seleccione una opción del menú para comenzar.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div class="card">
                <h3>Asistencia Diaria</h3>
                <p>Registro y control de asistencia de estudiantes.</p>
                <a href="asistencia.php" class="btn btn-primary">Gestionar</a>
            </div>

            <div class="card">
                <h3>Menú del Día</h3>
                <p>Planificación y gestión del menú diario.</p>
                <a href="menu.php" class="btn btn-primary">Gestionar</a>
            </div>

            <div class="card">
                <h3>Inventario</h3>
                <p>Control de insumos y productos.</p>
                <a href="inventario.php" class="btn btn-primary">Gestionar</a>
            </div>

            <div class="card">
                <h3>Reportes</h3>
                <p>Generación de informes y estadísticas.</p>
                <a href="reportes.php" class="btn btn-primary">Ver Reportes</a>
            </div>
        </div>
    </main>
</body>
</html>
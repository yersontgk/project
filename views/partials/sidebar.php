<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
$auth = new AuthController();
?>
<head>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<nav class="sidebar">
    <div class="sidebar-header">
        <h3 class="menu-text">Comedor Escolar</h3>
        <button class="sidebar-toggle">☰</button>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php">
                <span>📊</span>
                <span class="menu-text">Inicio</span>
            </a>
        </li>
        <?php if($auth->checkRole(['admin', 'base'])): ?>
        <li>
            <a href="asistencia.php">
                <span>📋</span>
                <span class="menu-text">Asistencia</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if($auth->checkRole(['admin', 'base', 'cocinero'])): ?>
        <li>
            <a href="platos_servidos.php">
                <span>👩‍👩‍👦‍👦</span>
                <span class="menu-text">Platos Servidos</span>
            </a>
        </li>
        <li>
            <a href="menu.php">
                <span>🍽️</span>
                <span class="menu-text">Menú</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if($auth->checkRole(['admin', 'cocinero'])): ?>
        <li>
            <a href="inventario.php">
                <span>📦</span>
                <span class="menu-text">Inventario</span>
            </a>
        </li>
        <li>
            <a href="gramaje/index.php">
                <span>⚖️</span>
                <span class="menu-text">Gramaje</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if($auth->checkRole(['admin'])): ?>
        <li>
            <a href="reportes.php">
                <span>📊</span>
                <span class="menu-text">Reportes</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if($auth->checkRole(['admin'])): ?>
        <li>
            <a href="usuarios.php">
                <span>👥</span>
                <span class="menu-text">Usuarios</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="logout.php">
                <span>🚪</span>
                <span class="menu-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>
<script src="../assets/js/sidebar.js"></script>

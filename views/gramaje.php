<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/MenuController.php';
require_once __DIR__ . '/../controllers/MenuProductoController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';
require_once __DIR__ . '/../controllers/ConsumoController.php';
require_once __DIR__ . '/../controllers/InventarioController.php';

$auth = new AuthController();
$menuController = new MenuController();
$menuProductoController = new MenuProductoController();
$asistenciaController = new AsistenciaController();
$consumoController = new ConsumoController();
$inventarioController = new InventarioController();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$fecha = isset($_GET['fecha']) ? DateTime::createFromFormat('Y-m-d', $_GET['fecha'])->format('Y-m-d') : date('Y-m-d');
if ($fecha > date('Y-m-d')) {
    $fecha = date('Y-m-d');
}

// Get menu del día
$consumoMenu = $consumoController->getConsumoWithMenuByDateAndUser($fecha, $_SESSION['user_id'])->fetch(PDO::FETCH_ASSOC);

// Get asistencia total
require_once __DIR__ . '/../controllers/PlatosServidosController.php';
$platosServidosController = new PlatosServidosController();

$totalPlatosServidos = 0;
if ($consumoMenu) {
    $platosServidosRecords = $platosServidosController->getPlatosByFecha($fecha);
    foreach ($platosServidosRecords as $record) {
        $totalPlatosServidos += $record['platos_servidos'];
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cálculo de Gramaje - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/gramaje.css">
</head>
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Cálculo de Gramaje</h2>

            <form method="GET" class="mb-4">
                <div class="form-group">
                    <label for="fecha">Seleccionar Fecha:</label>
<input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" max="<?php echo date('Y-m-d'); ?>" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
                </div>
            </form>

            <?php if ($consumoMenu): ?>
                <div id="custom-alert" class="alert" style="display:none;"></div>
                <div class="alert alert-info">
                    <strong>Menú del día:</strong> <?php echo htmlspecialchars($consumoMenu['nombre']); ?>
                    <?php if ($consumoMenu['observacion']): ?>
                        <br>
                        <small><?php echo htmlspecialchars($consumoMenu['observacion']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="calculo-gramaje">
                    <h3>Cálculo de Consumo</h3>
<p>Total de platos servidos: <strong><?php echo $totalPlatosServidos; ?></strong></p>

                    <div class="producto-row header-row">
                        <div>Producto</div>
                        <div>Cantidad por plato</div>
                        <div>Stock actual</div>
                        <div>Total necesario</div>
                    </div>

                    <?php 
$productos = $menuProductoController->getProductosByMenu($consumoMenu['id_menu']);
                        while ($producto = $productos->fetch(PDO::FETCH_ASSOC)):
$cantidadNecesaria = $producto['cantidad_por_plato'] * $totalPlatosServidos;
                            $stockActual = $producto['stock'];
                            $claseStock = $cantidadNecesaria > $stockActual ? 'text-danger' : 'text-success';
                    ?>
                    <div class="producto-row" data-low-stock="<?php echo ($cantidadNecesaria > $stockActual) ? 'true' : 'false'; ?>">
                        <div><?php echo htmlspecialchars($producto['producto_nombre']); ?></div>
                        <div><?php echo $producto['cantidad_por_plato'] . ' ' . $producto['unidad']; ?></div>
                        <div class="<?php echo $claseStock; ?>"><?php echo $stockActual . ' ' . $producto['unidad']; ?></div>
                        <div><?php echo $cantidadNecesaria . ' ' . $producto['unidad']; ?></div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button id="restar-insumos-btn" class="btn btn-primary mt-3">Restar insumos</button>
            <?php else: ?>
                <div class="alert alert-warning">
                    No hay un menú seleccionado para la fecha seleccionada. Por favor, seleccione un menú en el Dashboard.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Custom Warning Modal -->
    <div id="warning-modal" class="modal" style="display:none;">
        <div class="modal-content warning-modal-content">
            <div id="modal-icon" class="modal-icon warning-icon">⚠️</div>
            <h2 class="warning-title">Advertencia de Seguridad</h2>
            <p id="warning-message" class="warning-message"></p>
            <div class="warning-buttons">
                <button id="warning-confirm-btn" class="btn btn-primary" style="margin-right: 100px;">Confirmar</button>
                <button id="warning-cancel-btn" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Custom Success Modal -->
    <div id="success-modal" class="modal" style="display:none;">
        <div class="modal-content success-modal-content">
            <div class="modal-icon success-icon">✅</div>
            <h2 class="success-title">Operación Exitosa</h2>
            <p id="success-message" class="success-message"></p>
            <div class="success-buttons">
                <button id="success-close-btn" class="btn btn-primary">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Custom Friendly Modal -->
    <div id="friendly-modal" class="modal" style="display:none;">
        <div class="modal-content friendly-modal-content">
            <div class="modal-icon friendly-icon">😊</div>
            <h2 class="friendly-title">Inventario Normal</h2>
            <p id="friendly-message" class="friendly-message"></p>
            <div class="friendly-buttons">
                <button id="friendly-confirm-btn" class="btn btn-primary" style="margin-right: 100px;">Confirmar</button>
                <button id="friendly-close-btn" class="btn btn-primary">Cerrar</button>
            </div>
        </div>
    </div>

<script src="../../assets/js/gramaje.js"></script>
</body>
</html>

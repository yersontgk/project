<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/InventarioController.php';
require_once __DIR__ . '/../controllers/MenuController.php';

$auth = new AuthController();
$inventarioController = new InventarioController();
$menuController = new MenuController();

if(!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get products with low stock
$productosBajoStock = $inventarioController->getProductosBajoStock();

// Get today's menu
$fecha = date('Y-m-d');
$menuHoy = $menuController->getMenuByDate($fecha)->fetch(PDO::FETCH_ASSOC);

// Get all available menus for selection
$menus = $menuController->getAllMenus();
$menusArray = $menus->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../controllers/ConsumoController.php';

$consumoController = new ConsumoController();
/* $created_by = $_SESSION['user_id']; */

$consumoMenuStmt = $consumoController->getConsumoWithMenuByDate($fecha);
$consumoMenu = $consumoMenuStmt->fetch(PDO::FETCH_ASSOC);

// Handle menu selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_menu'])) {
    $id_menu = $_POST['id_menu'];
    $observacion = ''; // You can adjust this if you want to get from form
    $estado = true;
    $created_by = $_SESSION['user_id'];

    $result = $consumoController->createOrUpdateConsumo($fecha, $observacion, $id_menu, $estado, $created_by);

    // Set flash message for success
    $_SESSION['menu_set_success'] = "Menú establecido correctamente";

    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/dashboard.css" />
    <link rel="stylesheet" href="../assets/css/notifications.css" />
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>

    <main class="main-content">
        <?php
        $productosBajoStockArray = [];
        if($productosBajoStock->rowCount() > 0) {
            while($producto = $productosBajoStock->fetch(PDO::FETCH_ASSOC)) {
                $productosBajoStockArray[] = $producto;
            }
        }
        ?>

        <div class="dashboard-container">
            <div class="dashboard-layout">
                <div class="slider" style="width: 1150px; height: 450px; padding: 2rem; display: flex; position: relative;">
                    <div class="background-circles"></div>
                    <div class="illumination-overlay"></div>

                    <div style="flex: 1; position: relative; z-index: 2;">
                        <div class="slider-slide active" data-section="asistencia" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Asistencia</h2>
                                <p>Registro y control de asistencia de estudiantes.</p>
                                <?php if(!$auth->checkRole(['cocinero'])): ?>
                        <button class="btn-link" data-link="asistencia.php">Gestionar Asistencia</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="slider-slide" data-section="platos_servidos" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Platos Servidos</h2>
                                <p>Registro de platos servidos y devueltos.</p>
                        <button class="btn-link" data-link="platos_servidos.php">Gestionar Platos Servidos</button>
                            </div>
                        </div>
                        <div class="slider-slide" data-section="menu" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Menú</h2>
                                <p>Creación de Menús y Gramaje por Menú/producto.</p>
                        <button class="btn-link" data-link="menu.php">Gestionar Menú</button>
                            </div>
                        </div>

                        <div class="slider-slide" data-section="gramaje" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Gramaje</h2>
                                <p>Cálculos automáticos de Gramaje.</p>
                        <?php if(!$auth->checkRole(['base'])): ?>
                        <button class="btn-link" data-link="gramaje/index.php">Gestionar Gramaje</button>
                        <?php endif; ?>
                            </div>
                        </div>

                        <div class="slider-slide" data-section="inventario" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Inventario</h2>
                                <p>Control de insumos y productos.</p>
                        <?php if(!$auth->checkRole(['base'])): ?>
                        <button class="btn-link" data-link="inventario.php">Gestionar Inventario</button>
                        <?php endif; ?>
                            </div>
                        </div>

                        <div class="slider-slide" data-section="reportes" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Reportes</h2>
                                <p>Generación de informes y estadísticas.</p>
                        <?php if($auth->checkRole(['admin'])): ?>
                        <button class="btn-link" data-link="reportes.php">Ver Reportes</button>
                        <?php endif; ?>
                            </div>
                        </div>

                        <div class="slider-slide" data-section="usuarios" style="position: absolute; top: 0; left: 0; width: 100%; opacity: 0; pointer-events: none; transform: translateX(100%);">
                            <div class="slider-content">
                                <h2>Usuarios</h2>
                                <p>Control para añadir o modificar usuarios.</p>
                        <?php if($auth->checkRole(['admin'])): ?>
                        <button class="btn-link" data-link="usuarios.php">Gestionar Usuarios</button>
                        <?php endif; ?>
                            </div>
                        </div>


                        <div class="slider-nav" style="position: absolute; top: 50%; width: 148%; display: flex; justify-content: space-between; transform: translateY(-50%) translateX(-3.5%); pointer-events: all; z-index: 4; padding: 0 1rem;">
                            <button class="slider-prev" aria-label="Previous">&#8592;</button>
                            <button class="slider-next" aria-label="Next">&#8594;</button>
                        </div>
                    </div>

                    <div class="slider-character" style="width: 300px; height: 450px; position: relative; z-index: 3; pointer-events: none; top: -50px; right: 80px;">
                        <img src="../assets/imagenes/3.png" alt="Personaje" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>
                    <div class="slider-dots" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 5;">
                        <!-- Dots will be dynamically created here -->
                    </div>
                </div>
            </div>

            <?php if(!$auth->checkRole(['base'])): ?>
            <div class="menu-selection" style="margin-top: 2rem;">
                <h3>🍽️ Menú del Día</h3>
                <?php if($consumoMenu): ?>
                    <?php
                        $menuProductsStmt = $menuController->getMenuProducts($consumoMenu['id_menu']);
                        $maxDishes = null;
                        if ($menuProductsStmt && $menuProductsStmt->rowCount() > 0) {
                            $maxDishes = PHP_INT_MAX;
                            while ($product = $menuProductsStmt->fetch(PDO::FETCH_ASSOC)) {
                                if ($product['cantidad_por_plato'] > 0) {
                                    $possibleDishes = floor($product['stock'] / $product['cantidad_por_plato']);
                                    if ($possibleDishes < $maxDishes) {
                                        $maxDishes = $possibleDishes;
                                    }
                                }
                            }
                        }
                    ?>
                    <p>Menú actual: <strong><?php echo htmlspecialchars($consumoMenu['nombre']); ?></strong>
                    <?php if ($maxDishes !== null): ?>
                        <span style="font-weight: normal; font-size: 0.9em; color: #555; margin-left: 0.5em;">
                            (Máximo platos: <?php echo $maxDishes; ?>)
                        </span>
                    <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p>No hay menú seleccionado para hoy</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if(!$auth->checkRole(['base'])): ?>
                <?php if (isset($_SESSION['menu_set_success'])): ?>
                    <div class="alert alert-success" style="margin-bottom: 1rem; padding: 0.75rem 1rem; border: 1px solid #4CAF50; background-color: #d4edda; color: #155724; border-radius: 4px;">
                        <?php 
                            echo $_SESSION['menu_set_success']; 
                            unset($_SESSION['menu_set_success']);
                        ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_menu">Seleccionar menú para hoy:</label>
                        <select name="id_menu" id="id_menu" class="form-control" required>
                            <option value="">Seleccione un menú...</option>
                            <?php foreach($menusArray as $menu): ?>
                                <?php
                                    $menuProductsStmt = $menuController->getMenuProducts($menu['id_menu']);
                                    $maxDishesOption = null;
                                    if ($menuProductsStmt && $menuProductsStmt->rowCount() > 0) {
                                        $maxDishesOption = PHP_INT_MAX;
                                        while ($product = $menuProductsStmt->fetch(PDO::FETCH_ASSOC)) {
                                            if ($product['cantidad_por_plato'] > 0) {
                                                $possibleDishes = floor($product['stock'] / $product['cantidad_por_plato']);
                                                if ($possibleDishes < $maxDishesOption) {
                                                    $maxDishesOption = $possibleDishes;
                                                }
                                            }
                                        }
                                    }
                                ?>
                                <option value="<?php echo $menu['id_menu']; ?>"
                                    <?php echo ($consumoMenu && $consumoMenu['id_menu'] == $menu['id_menu']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($menu['nombre']); ?>
                                    <?php if ($maxDishesOption !== null): ?>
                                        (Máximo platos: <?php echo $maxDishesOption; ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Establecer Menú del Día</button>
            <?php endif; ?>
                </form>
            </div>
        </div>
    </main>

    <script>
        const productosBajoStock = <?php echo json_encode($productosBajoStockArray); ?>;
        document.addEventListener('DOMContentLoaded', () => {
            productosBajoStock.forEach(producto => {
                const message = `⚠️ Producto con Stock Bajo: ${producto.nombre}. Stock actual: ${producto.stock} ${producto.simbolo} (Mínimo requerido: ${producto.stock_minimo} ${producto.simbolo})`;
                showNotification(message);
            });
        });
    </script>
    <script src="../assets/js/notifications_custom.js"></script>
    <script src="../assets/js/dashboard-images-custom.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>

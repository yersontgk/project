<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/MenuController.php';
require_once __DIR__ . '/../controllers/MenuProductoController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';

$auth = new AuthController();
$menuController = new MenuController();
$menuProductoController = new MenuProductoController();
$asistenciaController = new AsistenciaController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

require_once __DIR__ . '/../controllers/ConsumoController.php';
$consumoController = new ConsumoController();

$user_id = $_SESSION['user_id'];

// Handle consumo menu selection form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                // Restrict modifications for 'base' role
                if ($auth->checkRole(['base'])) {
                    $mensaje = '<div class="alert alert-danger">No tiene permisos para modificar el menú.</div>';
                } else {
                    switch ($_POST['action']) {
                        case 'create':
                            $nombre = $_POST['nombre'];
                            $observacion = $_POST['observacion'];
                            $fecha = $_POST['fecha'];
                            
                            if ($menuController->createMenu($nombre, $observacion, $fecha, $user_id)) {
                                $id_menu = $menuController->getLastInsertedId();
                                if (isset($_POST['productos']) && is_array($_POST['productos'])) {
                                    $productos = [];
                                    foreach ($_POST['productos'] as $id_producto => $cantidad) {
                                        if ($cantidad > 0) {
                                            // Convertir cantidad de gramos a kilogramos si el producto está en kg
                                            $productoInfo = $menuProductoController->getProductosDisponibles()->fetchAll(PDO::FETCH_ASSOC);
                                            $unidadProducto = 'u';
                                            foreach ($productoInfo as $prod) {
                                                if ($prod['id_producto'] == $id_producto) {
                                                    $unidadProducto = $prod['simbolo'];
                                                    break;
                                                }
                                            }
                                            $cantidadConvertida = $unidadProducto === 'kg' ? $cantidad / 1000 : $cantidad;
                                            $productos[] = [
                                                'id_producto' => $id_producto,
                                                'cantidad_por_plato' => $cantidadConvertida
                                            ];
                                        }
                                    }
                                    $menuProductoController->updateMenuProductos($id_menu, $productos);
                                }
                                $mensaje = '<div class="alert alert-success">Menú creado correctamente</div>';
                            } else {
                                $mensaje = '<div class="alert alert-danger">Error al crear el menú</div>';
                            }
                            break;

                        case 'update':
                            $id_menu = $_POST['id_menu'];
                            $nombre = $_POST['nombre'];
                            $observacion = $_POST['observacion'];
                            $fecha = $_POST['fecha'];

                            if ($menuController->updateMenu($id_menu, $nombre, $observacion, $fecha)) {
                                if (isset($_POST['productos']) && is_array($_POST['productos'])) {
                                    $productos = [];
                                    foreach ($_POST['productos'] as $id_producto => $cantidad) {
                                        if ($cantidad > 0) {
                                            // Convertir cantidad de gramos a kilogramos si el producto está en kg
                                            $productoInfo = $menuProductoController->getProductosDisponibles()->fetchAll(PDO::FETCH_ASSOC);
                                            $unidadProducto = 'u';
                                            foreach ($productoInfo as $prod) {
                                                if ($prod['id_producto'] == $id_producto) {
                                                    $unidadProducto = $prod['simbolo'];
                                                    break;
                                                }
                                            }
                                            $cantidadConvertida = $unidadProducto === 'kg' ? $cantidad / 1000 : $cantidad;
                                            $productos[] = [
                                                'id_producto' => $id_producto,
                                                'cantidad_por_plato' => $cantidadConvertida
                                            ];
                                        }
                                    }
                                    if (!$menuProductoController->updateMenuProductos($id_menu, $productos)) {
                                        $mensaje = '<div class="alert alert-danger">Error al actualizar los productos del menú</div>';
                                    }
                                }
                                $mensaje = '<div class="alert alert-success">Menú actualizado correctamente</div>';
                            } else {
                                $mensaje = '<div class="alert alert-danger">Error al actualizar el menú</div>';
                            }
                            break;

                        case 'select_menu_del_dia':
                            $selected_menu_id = $_POST['selected_menu_id'];
                            $fecha_seleccion = $_POST['fecha_seleccion'];
                            $observacion = isset($_POST['observacion_seleccion']) ? $_POST['observacion_seleccion'] : '';

                            if ($consumoController->createOrUpdateConsumo($fecha_seleccion, $observacion, $selected_menu_id, true, $user_id)) {
                                $mensaje = '<div class="alert alert-success">Menú del día seleccionado correctamente</div>';
                            } else {
                                $mensaje = '<div class="alert alert-danger">Error al seleccionar el menú del día</div>';
                            }
                            break;

                        case 'delete':
                            $id_menu = $_POST['id_menu'];
                            if ($menuController->deleteMenu($id_menu)) {
                                $mensaje = '<div class="alert alert-success">Menú eliminado correctamente</div>';
                            } else {
                                $mensaje = '<div class="alert alert-danger">Error al eliminar el menú</div>';
                            }
                            break;
                        case 'enable':
                            $id_menu = $_POST['id_menu'];
                            if ($menuController->enableMenu($id_menu)) {
                                $mensaje = '<div class="alert alert-success">Menú habilitado correctamente</div>';
                            } else {
                                $mensaje = '<div class="alert alert-danger">Error al habilitar el menú</div>';
                            }
                            break;
                    }
                }
            }
        }

// Load menus and other data
$menus = $menuController->getAllMenus();
$disabledMenus = $menuController->getDisabledMenus();
$productosDisponibles = $menuProductoController->getProductosDisponibles();
$asistenciaHoy = $asistenciaController->getAsistenciasPorFecha(date('Y-m-d'));

// Calcular total de asistentes
$totalAsistentes = 0;
while ($asistencia = $asistenciaHoy->fetch(PDO::FETCH_ASSOC)) {
    $totalAsistentes += $asistencia['total_masculino'] + $asistencia['total_femenino'];
}

// Load current consumo for user and date to show selected menu
$currentConsumoStmt = $consumoController->getConsumoWithMenuByDateAndUser($fecha, $user_id);
$currentConsumo = $currentConsumoStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Menú - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/menu.css">
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Gestión de Menú</h2>
            <?php echo $mensaje; ?>

            <?php if(!$auth->checkRole(['base'])): ?>
            <button type="button" class="btn btn-primary mb-4" onclick="showCreateModal()">
                Crear Nuevo Menú
            </button>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Observación</th>
                        <th>Creado por</th>
                        <th>Productos</th>
                        <th>Gramaje Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($menu = $menus->fetch(PDO::FETCH_ASSOC)): 
                        $productosMenu = $menuProductoController->getProductosByMenu($menu['id_menu']);
                        $productosArray = [];
                        $productosList = [];
                        $gramajeTotal = 0;
                        while ($producto = $productosMenu->fetch(PDO::FETCH_ASSOC)) {
                            // Convertir cantidad_por_plato a gramos si la unidad es kg
                            $cantidadEnGramos = $producto['unidad'] === 'kg' ? $producto['cantidad_por_plato'] * 1000 : $producto['cantidad_por_plato'];
                            $gramajeTotal += $cantidadEnGramos;
                            $productosArray[$producto['id_producto']] = $cantidadEnGramos;
                            $productosList[] = $producto;
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($menu['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($menu['observacion']); ?></td>
                            <td><?php echo htmlspecialchars($menu['creado_por']); ?></td>
                            <td>
                                <?php 
                                foreach ($productosList as $producto) {
                                    // Mostrar cantidad en gramos si la unidad es kg
                                    $cantidadMostrar = $producto['unidad'] === 'kg' ? $producto['cantidad_por_plato'] * 1000 : $producto['cantidad_por_plato'];
                                    $unidadMostrar = $producto['unidad'] === 'kg' ? 'g' : $producto['unidad'];
                                    echo htmlspecialchars($producto['producto_nombre']) . 
                                         ': ' . $cantidadMostrar . 
                                         ' ' . $unidadMostrar . '<br>';
                                }
                                ?>
                            </td>
                            <td><?php echo $gramajeTotal * $totalAsistentes; ?> g</td>
                            <td>
                                <?php if(!$auth->checkRole(['base'])): ?>
                                <button type="button" class="btn btn-primary" 
                                        onclick='showEditModal(
                                            "<?php echo $menu['id_menu']; ?>",
                                            "<?php echo htmlspecialchars($menu['nombre']); ?>",
                                            "<?php echo htmlspecialchars($menu['observacion']); ?>",
                                            "<?php echo $menu['fecha']; ?>",
                                            <?php echo json_encode($productosArray); ?>
                                        )'>
                                    Editar
                                </button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Está seguro de deshabilitar este menú?')">
                                        deshabilitar 
                                    </button>
                                </form>
                                <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled>
                                    Editar
                                </button>
                                <button type="button" class="btn btn-danger" disabled>
                                    deshabilitar
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div>
            <button id="toggleDisabledMenusBtn" class="btn btn-secondary mb-3">Mostrar Menús Deshabilitados</button>
        </div>

        <div id="disabledMenusContainer" class="card mt-5" style="opacity: 0.6; color: red; display: none;">
            <h2>Menús Deshabilitados</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Observación</th>
                        <th>Creado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($menu = $disabledMenus->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($menu['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($menu['observacion']); ?></td>
                            <td><?php echo htmlspecialchars($menu['creado_por']); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="enable">
                                    <input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>">
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('¿Está seguro de habilitar este menú?')"
                                            <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                                        habilitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para crear menú -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevo Menú</h3>
                <form id="createForm" method="POST" action="">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="observacion">Observación:</label>
                    <textarea id="observacion" name="observacion" class="form-control" rows="3" <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>></textarea>
                </div>

                <h4>Productos del Menú</h4>
                <button type="button" id="toggleProductListCreate" class="btn btn-secondary mb-3">➕ Mostrar productos</button>
                <div id="productChecklistCreate" class="product-checklist" style="display:none;">
                    <?php 
                    $productosDisponibles->execute();
                    while ($producto = $productosDisponibles->fetch(PDO::FETCH_ASSOC)): 
                    ?>
                    <div>
                        <input type="checkbox" class="product-checkbox-create" id="check-create-<?php echo $producto['id_producto']; ?>" value="<?php echo $producto['id_producto']; ?>" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                        <label for="check-create-<?php echo $producto['id_producto']; ?>">
                            <?php echo htmlspecialchars($producto['nombre']); ?> (Stock: <?php echo htmlspecialchars($producto['stock']); ?> <?php echo $producto['simbolo']; ?>)
                        </label>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="productos-grid" id="selectedProductsCreate">
                    <!-- Selected products will be shown here -->
                </div>

                <!-- Removed gramaje-total div -->

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Crear Menú</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
                </form>
        </div>
    </div>

    <!-- Modal para editar menú -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Menú</h3>
                <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_menu" id="edit-id">
 
                <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                    <input type="text" id="edit-nombre" name="nombre" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="edit-fecha">Fecha:</label>
                    <input type="date" id="edit-fecha" name="fecha" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="edit-observacion">Observación:</label>
                    <textarea id="edit-observacion" name="observacion" class="form-control" rows="3" <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>></textarea>
                </div>

                <h4>Productos del Menú</h4>
                <button type="button" id="toggleProductListEdit" class="btn btn-secondary mb-3">➕ Mostrar productos</button>
                <div id="productChecklistEdit" class="product-checklist" style="display:none;">
                    <?php 
                    $productosDisponibles->execute();
                    while ($producto = $productosDisponibles->fetch(PDO::FETCH_ASSOC)): 
                    ?>
                    <div>
                        <input type="checkbox" class="product-checkbox-edit" id="check-edit-<?php echo $producto['id_producto']; ?>" value="<?php echo $producto['id_producto']; ?>" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                        <label for="check-edit-<?php echo $producto['id_producto']; ?>">
                            <?php echo htmlspecialchars($producto['nombre']); ?> (Stock: <?php echo htmlspecialchars($producto['stock']); ?> <?php echo $producto['simbolo']; ?>)
                        </label>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="productos-grid" id="selectedProductsEdit">
                    <!-- Selected products will be shown here -->
                </div>

                <!-- Removed gramaje-total edit div -->

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
                </form>
        </div>
    </div>

    <script src="../assets/js/menu.js"></script>
    <?php if (!empty($mensaje)): ?>>
    <?php endif; ?>

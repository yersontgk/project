<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/MenuController.php';
require_once __DIR__ . '/../../controllers/MenuProductoController.php';

$auth = new AuthController();
$menuController = new MenuController();
$menuProductoController = new MenuProductoController();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$mensaje = '';
$id_menu = isset($_GET['id_menu']) ? $_GET['id_menu'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $productos = [];
        foreach ($_POST['productos'] as $id_producto => $cantidad) {
            if ($cantidad > 0) {
                $productos[] = [
                    'id_producto' => $id_producto,
                    'cantidad_por_plato' => $cantidad
                ];
            }
        }

        if ($menuProductoController->updateMenuProductos($id_menu, $productos)) {
            $mensaje = '<div class="alert alert-success">Productos actualizados correctamente</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al actualizar los productos</div>';
        }
    }
}

$menu = null;
$productosMenu = [];
if ($id_menu) {
    $menu = $menuController->getMenuByDate(date('Y-m-d'))->fetch(PDO::FETCH_ASSOC);
    $productosMenu = $menuProductoController->getProductosByMenu($id_menu);
}

$productosDisponibles = $menuProductoController->getProductosDisponibles();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos del Menú - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Gestión de Productos del Menú</h2>
            <?php echo $mensaje; ?>

            <?php if ($menu): ?>
                <h3>Menú: <?php echo htmlspecialchars($menu['nombre']); ?></h3>
                <p>Fecha: <?php echo $menu['fecha']; ?></p>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="update">
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Unidad</th>
                                <th>Gramaje por Plato</th>
                                <th>Cantidad por Plato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $productosDisponibles->fetch(PDO::FETCH_ASSOC)): 
                                $productoMenu = null;
                                if ($productosMenu) {
                                    while ($pm = $productosMenu->fetch(PDO::FETCH_ASSOC)) {
                                        if ($pm['id_producto'] == $producto['id_producto']) {
                                            $productoMenu = $pm;
                                            break;
                                        }
                                    }
                                }
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['simbolo']); ?></td>
                                    <td><?php echo $productoMenu ? $productoMenu['gramaje_por_plato'] : '0'; ?></td>
                                    <td>
                                        <input type="number" 
                                               name="productos[<?php echo $producto['id_producto']; ?>]" 
                                               value="<?php echo $productoMenu ? $productoMenu['cantidad_por_plato'] : '0'; ?>"
                                               class="form-control" 
                                               min="0" 
                                               step="0.01">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">
                    Seleccione un menú para gestionar sus productos.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
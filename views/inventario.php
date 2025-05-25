<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/InventarioController.php';

$auth = new AuthController();
$inventarioController = new InventarioController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Restrict modifications for 'base' role
        if ($auth->checkRole(['base'])) {
            $mensaje = '<div class="alert alert-danger">No tiene permisos para modificar el inventario.</div>';
        } else {
            switch ($_POST['action']) {
                case 'create':
                    $nombre = $_POST['nombre'];
                    $stock = $_POST['stock'];
                    $stock_minimo = $_POST['stock_minimo'];
                    $id_unidad = $_POST['id_unidad'];
                    // Removed gramaje handling
                    if ($inventarioController->createProduct($nombre, $stock, $stock_minimo, $id_unidad)) {
                        $id_producto = $inventarioController->getLastInsertedId();
                        $mensaje = '<div class="alert alert-success">Producto creado correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al crear el producto</div>';
                    }
                    break;

                case 'update':
                    $id_producto = $_POST['id_producto'];
                    $nombre = $_POST['nombre'];
                    $stock = $_POST['stock'];
                    $stock_minimo = $_POST['stock_minimo'];
                    $id_unidad = $_POST['id_unidad'];
                    // Removed gramaje handling
                    if ($inventarioController->updateProduct($id_producto, $nombre, $stock, $stock_minimo, $id_unidad)) {
                        $mensaje = '<div class="alert alert-success">Producto actualizado correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-danger">Error al actualizar el producto</div>';
                    }
                    break;

                case 'updateStock':
                    $id_producto = $_POST['id_producto'];
                    $cantidad = $_POST['cantidad'];
                    $tipo = $_POST['tipo'];

                    if ($inventarioController->updateStock($id_producto, $cantidad, $tipo === 'ingreso')) {
                        $mensaje = '<div class="alert alert-success">Stock actualizado correctamente</div>';
                    } else {
                        $mensaje = '<div class="alert alert-warning">No es posible reducir el stock por debajo de cero</div>';
                    }
                    break;
            }
        }
    }
}

$productos = $inventarioController->getAllProducts();
$productosBajoStock = $inventarioController->getProductosBajoStock();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Sistema de Comedor Escolar</title>
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
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        .stock-warning {
            color: #dc3545;
            font-weight: bold;
        }
        .inventory-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>
    <?php include 'partials/sidebar.php'; ?>
    <?php include 'partials/navigation_buttons.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Gestión de Inventario</h2>
            <?php echo $mensaje; ?>

            <div class="inventory-stats">
                <div class="stat-card">
                    <h4>Total de Productos</h4>
                    <div class="value"><?php echo $productos->rowCount(); ?></div>
                </div>
                <div class="stat-card">
                    <h4>Productos en Bajo Stock</h4>
                    <div class="value"><?php echo $productosBajoStock->rowCount(); ?></div>
                </div>
            </div>

            <?php if($productosBajoStock->rowCount() > 0): ?>
                <div class="alert alert-warning">
                    <h4>Productos con bajo stock:</h4>
                    <ul>
                        <?php while($producto = $productosBajoStock->fetch(PDO::FETCH_ASSOC)): ?>
                            <li>
                                <?php echo htmlspecialchars($producto['nombre']); ?> - 
                                Stock actual: <?php echo $producto['stock'] . ' ' . $producto['simbolo']; ?> 
                                (Mínimo: <?php echo $producto['stock_minimo'] . ' ' . $producto['simbolo']; ?>)
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if(!$auth->checkRole(['base'])): ?>
            <button type="button" class="btn btn-primary mb-4" onclick="showCreateModal()">
                Crear Nuevo Producto
            </button>
            <?php endif; ?>

            <input type="text" id="searchInput" placeholder="Buscar producto..." class="form-control mb-3" />

            <table class="table" id="productosTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Stock Mínimo</th>
                        <th>Unidad</th>
                            <!-- Removed Gramaje por Plato column -->
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($producto = $productos->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo $producto['stock'] . ' ' . $producto['simbolo']; ?></td>
                            <td><?php echo $producto['stock_minimo'] . ' ' . $producto['simbolo']; ?></td>
                            <td><?php echo htmlspecialchars($producto['unidad']); ?></td>
                            <td>
                                <?php if($producto['stock'] <= $producto['stock_minimo']): ?>
                                    <span class="stock-warning">Bajo Stock</span>
                                <?php else: ?>
                                    <span class="text-success">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!$auth->checkRole(['base'])): ?>
                                <button type="button" class="btn btn-primary" 
                                        onclick="showEditModal(
                                            '<?php echo $producto['id_producto']; ?>',
                                            '<?php echo htmlspecialchars($producto['nombre']); ?>',
                                            '<?php echo $producto['stock']; ?>',
                                            '<?php echo $producto['stock_minimo']; ?>',
                                            '<?php echo $producto['id_unidad']; ?>'
                                        )">
                                    Editar
                                </button>
                                <button type="button" class="btn btn-secondary"
                                        onclick="showStockModal('<?php echo $producto['id_producto']; ?>')">
                                    Actualizar Stock
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled>
                                    Editar
                                </button>
                                <button type="button" class="btn btn-secondary" disabled>
                                    Actualizar Stock
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para crear producto -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevo Producto</h3>
            <form id="createForm" method="POST" action="">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Inicial:</label>
                <input type="number" id="stock" name="stock" class="form-control" step="0.01" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="stock_minimo">Stock Mínimo:</label>
                <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" step="0.01" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="id_unidad">Unidad:</label>
                <select id="id_unidad" name="id_unidad" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                        <option value="1">Kilogramos (kg)</option>
                        <option value="2">Litros (L)</option>
                        <option value="3">Unidades (u)</option>
                    </select>
                </div>

                <!-- Removed gramaje input field -->

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Crear Producto</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar producto -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Producto</h3>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_producto" id="edit-id">

                <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                <input type="text" id="edit-nombre" name="nombre" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="edit-stock">Stock:</label>
                <input type="number" id="edit-stock" name="stock" class="form-control" step="0.01" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="edit-stock_minimo">Stock Mínimo:</label>
                <input type="number" id="edit-stock_minimo" name="stock_minimo" class="form-control" step="0.01" required <?php if($auth->checkRole(['base'])) echo 'readonly'; ?>>
                </div>

                <div class="form-group">
                    <label for="edit-id_unidad">Unidad:</label>
                <select id="edit-id_unidad" name="id_unidad" class="form-control" required <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>
                        <option value="1">Kilogramos (kg)</option>
                        <option value="2">Litros (L)</option>
                        <option value="3">Unidades (u)</option>
                    </select>
                </div>

                <!-- Removed edit gramaje input field -->

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para actualizar stock -->
    <div id="stockModal" class="modal">
        <div class="modal-content">
            <h3>Actualizar Stock</h3>
            <form id="stockForm" method="POST" action="">
                <input type="hidden" name="action" value="updateStock">
                <input type="hidden" name="id_producto" id="stock-id">

                <div class="form-group">
                    <label for="tipo">Tipo de Movimiento:</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="ingreso">Ingreso</option>
                        <option value="salida">Salida</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" step="0.01" min="0.01" required>
                </div>

                <button type="submit" class="btn btn-primary" <?php if($auth->checkRole(['base'])) echo 'disabled'; ?>>Actualizar Stock</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('stockModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function showEditModal(id, nombre, stock, stock_minimo, id_unidad) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('edit-stock_minimo').value = stock_minimo;
            document.getElementById('edit-id_unidad').value = id_unidad;
            document.getElementById('editModal').style.display = 'block';
        }

        function showStockModal(id) {
            document.getElementById('stock-id').value = id;
            document.getElementById('stockModal').style.display = 'block';
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

        // Real-time search filter
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productosTable tbody tr');
            rows.forEach(row => {
                const productName = row.querySelector('td').textContent.toLowerCase();
                if (productName.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

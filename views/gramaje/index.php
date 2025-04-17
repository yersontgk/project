<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/GramajeController.php';

$auth = new AuthController();
$gramajeController = new GramajeController();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id_producto = $_POST['id_producto'];
        $gramaje_por_plato = $_POST['gramaje_por_plato'];

        if ($gramajeController->updateGramaje($id_producto, $gramaje_por_plato)) {
            $mensaje = '<div class="alert alert-success">Gramaje actualizado correctamente</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al actualizar el gramaje</div>';
        }
    }
}

$gramajes = $gramajeController->getAllGramajes();
$productosSinGramaje = $gramajeController->getProductosSinGramaje();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gramaje - Sistema de Comedor Escolar</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="card">
            <h2>Gestión de Gramaje</h2>
            <?php echo $mensaje; ?>

            <div class="mb-4">
                <h3>Productos con Gramaje Asignado</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Gramaje por Plato</th>
                            <th>Unidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($gramaje = $gramajes->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($gramaje['producto_nombre']); ?></td>
                                <td><?php echo $gramaje['gramaje_por_plato']; ?></td>
                                <td><?php echo htmlspecialchars($gramaje['unidad']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                            onclick="editarGramaje(
                                                '<?php echo $gramaje['id_producto']; ?>',
                                                '<?php echo $gramaje['gramaje_por_plato']; ?>'
                                            )">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if($productosSinGramaje->rowCount() > 0): ?>
                <div class="mb-4">
                    <h3>Productos sin Gramaje</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Unidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $productosSinGramaje->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['simbolo']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary"
                                                onclick="agregarGramaje('<?php echo $producto['id_producto']; ?>')">
                                            Agregar Gramaje
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal para editar gramaje -->
    <div id="gramajeModal" class="modal">
        <div class="modal-content">
            <h3>Editar Gramaje</h3>
            <form id="gramajeForm" method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_producto" id="edit-id-producto">

                <div class="form-group">
                    <label for="gramaje_por_plato">Gramaje por Plato:</label>
                    <input type="number" id="gramaje_por_plato" name="gramaje_por_plato" 
                           class="form-control" step="0.01" min="0" required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function editarGramaje(id_producto, gramaje) {
            document.getElementById('edit-id-producto').value = id_producto;
            document.getElementById('gramaje_por_plato').value = gramaje;
            document.getElementById('gramajeModal').style.display = 'block';
        }

        function agregarGramaje(id_producto) {
            document.getElementById('edit-id-producto').value = id_producto;
            document.getElementById('gramaje_por_plato').value = '';
            document.getElementById('gramajeModal').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('gramajeModal').style.display = 'none';
        }

        // Cerrar modal cuando se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
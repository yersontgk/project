<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/MenuController.php';

$auth = new AuthController();
$menuController = new MenuController();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$mensaje = '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nombre = $_POST['nombre'];
                $observacion = $_POST['observacion'];
                $fecha = $_POST['fecha'];
                
                if ($menuController->createMenu($nombre, $observacion, $fecha, $_SESSION['user_id'])) {
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
                    $mensaje = '<div class="alert alert-success">Menú actualizado correctamente</div>';
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al actualizar el menú</div>';
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
        }
    }
}

$menus = $menuController->getAllMenus();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Menú - Sistema de Comedor Escolar</title>
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
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
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
            <h2>Gestión de Menú</h2>
            <?php echo $mensaje; ?>

            <button type="button" class="btn btn-primary mb-4" onclick="showCreateModal()">
                Crear Nuevo Menú
            </button>

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
                    <?php while ($menu = $menus->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($menu['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($menu['observacion']); ?></td>
                            <td><?php echo htmlspecialchars($menu['creado_por']); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" 
                                        onclick="showEditModal(
                                            '<?php echo $menu['id_menu']; ?>',
                                            '<?php echo htmlspecialchars($menu['nombre']); ?>',
                                            '<?php echo htmlspecialchars($menu['observacion']); ?>',
                                            '<?php echo $menu['fecha']; ?>'
                                        )">
                                    Editar
                                </button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Está seguro de eliminar este menú?')">
                                        Eliminar
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
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="observacion">Observación:</label>
                    <textarea id="observacion" name="observacion" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Crear Menú</button>
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
                    <input type="text" id="edit-nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit-fecha">Fecha:</label>
                    <input type="date" id="edit-fecha" name="fecha" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit-observacion">Observación:</label>
                    <textarea id="edit-observacion" name="observacion" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showCreateModal() {
            document.getElementById('createModal').style.display = 'block';
            document.getElementById('fecha').value = new Date().toISOString().split('T')[0];
        }

        function showEditModal(id, nombre, observacion, fecha) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-observacion').value = observacion;
            document.getElementById('edit-fecha').value = fecha;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';

$auth = new AuthController();

if (!$auth->isLoggedIn() || !$auth->checkRole(['admin'])) {
    header('Location: login.php');
    exit();
}

$userController = new UserController();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $username = $_POST['username'];
                $password = $_POST['password'];
                $nombre_completo = $_POST['nombre_completo'];
                $rol = $_POST['rol'];

                if ($userController->createUser($username, $password, $nombre_completo, $rol)) {
                    $mensaje = '<div class="alert alert-success">Usuario creado correctamente</div>';
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al crear el usuario</div>';
                }
                break;

            case 'update':
                $id = $_POST['id'];
                $nombre_completo = $_POST['nombre_completo'];
                $rol = $_POST['rol'];
                $estado = isset($_POST['estado']) ? 1 : 0;

                if ($userController->updateUser($id, $nombre_completo, $rol, $estado)) {
                    $mensaje = '<div class="alert alert-success">Usuario actualizado correctamente</div>';
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al actualizar el usuario</div>';
                }
                break;
        }
    }
}

$usuarios = $userController->getAllUsers();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Comedor Escolar</title>
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
            <h2>Gestión de Usuarios</h2>
            <?php echo $mensaje; ?>

            <button type="button" class="btn btn-primary mb-4" onclick="showCreateModal()">
                Crear Nuevo Usuario
            </button>

            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                            <td><?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?></td>
                            <td><?php echo htmlspecialchars($usuario['created_at']); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" 
                                        onclick="showEditModal(
                                            '<?php echo $usuario['id']; ?>', 
                                            '<?php echo htmlspecialchars($usuario['nombre_completo']); ?>', 
                                            '<?php echo $usuario['rol']; ?>', 
                                            <?php echo $usuario['estado']; ?>
                                        )">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para crear usuario -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevo Usuario</h3>
            <form id="createForm" method="POST" action="">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="admin">Administrador</option>
                        <option value="base">Usuario Base</option>
                        <option value="cocinero">Cocinero</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Usuario</h3>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">

                <div class="form-group">
                    <label for="edit-nombre_completo">Nombre Completo:</label>
                    <input type="text" id="edit-nombre_completo" name="nombre_completo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit-rol">Rol:</label>
                    <select id="edit-rol" name="rol" class="form-control" required>
                        <option value="admin">Administrador</option>
                        <option value="base">Usuario Base</option>
                        <option value="cocinero">Cocinero</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="estado" id="edit-estado">
                        Usuario Activo
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function showEditModal(id, nombre_completo, rol, estado) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre_completo').value = nombre_completo;
            document.getElementById('edit-rol').value = rol;
            document.getElementById('edit-estado').checked = estado;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . '/GramajeController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    // Check user role to restrict 'base' users
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'base') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción.']);
        ob_end_flush();
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['action']) && $input['action'] === 'restarInsumos') {
            $fecha = $input['fecha'] ?? null;
            // Validate date is not in the future
            if ($fecha > date('Y-m-d')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'La fecha no puede ser futura.']);
                ob_end_flush();
                exit;
            }
            $controller = new GramajeController();
            try {
                $success = $controller->restarInsumos($fecha);
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al restar insumos']);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            ob_end_flush();
            exit;
        }
}
?>

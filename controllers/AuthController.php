<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function login($username, $password) {
        $this->usuario->username = $username;
        $result = $this->usuario->login();
        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Si la contraseña está en texto plano (primera vez)
            if ($row['password'] === 'yerson123') {
                // Actualizar a hash
                $hashedPassword = password_hash('123456789y', PASSWORD_DEFAULT);
                $this->usuario->id = $row['id'];
                $this->usuario->password = $hashedPassword;
                $this->usuario->updatePassword();
                
                // Verificar con la nueva contraseña
                if ($password === '123456789y') {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['rol'] = $row['rol'];
                    return true;
                }
            } else {
                // Verificar con password_verify
                if (password_verify($password, $row['password']) && $row['estado']) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['rol'] = $row['rol'];
                    return true;
                }
            }
        }
        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function checkRole($allowed_roles) {
        if(!$this->isLoggedIn()) return false;
        return in_array($_SESSION['rol'], $allowed_roles);
    }
}
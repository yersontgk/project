<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UserController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function getAllUsers() {
        return $this->usuario->read();
    }

    public function createUser($username, $password, $nombre_completo, $rol) {
        $this->usuario->username = $username;
        $this->usuario->password = $password;
        $this->usuario->nombre_completo = $nombre_completo;
        $this->usuario->rol = $rol;
        
        return $this->usuario->create();
    }

    public function updateUser($id, $nombre_completo, $rol, $estado) {
        $this->usuario->id = $id;
        $this->usuario->nombre_completo = $nombre_completo;
        $this->usuario->rol = $rol;
        $this->usuario->estado = $estado;
        
        return $this->usuario->update();
    }
}
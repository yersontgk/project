<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $username;
    public $password;
    public $nombre_completo;
    public $rol;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        $query = "SELECT id, password, rol, estado FROM " . $this->table_name . " 
                 WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        return $stmt;
    }

    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . "
                SET password = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->password);
        $stmt->bindParam(2, $this->id);

        return $stmt->execute();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (username, password, nombre_completo, rol)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->password);
        $stmt->bindParam(3, $this->nombre_completo);
        $stmt->bindParam(4, $this->rol);

        return $stmt->execute();
    }

    public function read() {
        $query = "SELECT id, username, nombre_completo, rol, estado, created_at 
                 FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET nombre_completo = ?, rol = ?, estado = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nombre_completo);
        $stmt->bindParam(2, $this->rol);
        $stmt->bindParam(3, $this->estado);
        $stmt->bindParam(4, $this->id);

        return $stmt->execute();
    }
}
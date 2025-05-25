<?php
class Menu {
    private $conn;
    private $table_name = "menu";

    public $id_menu;
    public $nombre;
    public $observacion;
    public $fecha;
    public $estado;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (nombre, observacion, fecha, created_by)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->observacion);
        $stmt->bindParam(3, $this->fecha);
        $stmt->bindParam(4, $this->created_by);

        $result = $stmt->execute();
        if ($result) {
            $this->id_menu = $this->conn->lastInsertId();
        }
        return $result;
    }

    public function read() {
        $query = "SELECT m.*, u.nombre_completo as creado_por 
                 FROM " . $this->table_name . " m
                 LEFT JOIN usuarios u ON m.created_by = u.id
                 WHERE m.estado = 1 
                 ORDER BY m.fecha DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readDisabled() {
        $query = "SELECT m.*, u.nombre_completo as creado_por 
                 FROM " . $this->table_name . " m
                 LEFT JOIN usuarios u ON m.created_by = u.id
                 WHERE m.estado = 0 
                 ORDER BY m.fecha DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readByDate($fecha) {
        $query = "SELECT m.*, u.nombre_completo as creado_por 
                 FROM " . $this->table_name . " m
                 LEFT JOIN usuarios u ON m.created_by = u.id
                 WHERE m.fecha = ? AND m.estado = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();

        return $stmt;
    }

    public function readById($id_menu) {
        $query = "SELECT m.*, u.nombre_completo as creado_por 
                 FROM " . $this->table_name . " m
                 LEFT JOIN usuarios u ON m.created_by = u.id
                 WHERE m.id_menu = ? AND m.estado = 1
                 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_menu);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET nombre = ?, observacion = ?, fecha = ?
                WHERE id_menu = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->observacion);
        $stmt->bindParam(3, $this->fecha);
        $stmt->bindParam(4, $this->id_menu);

        return $stmt->execute();
    }

    public function delete() {
        $query = "UPDATE " . $this->table_name . "
                SET estado = 0
                WHERE id_menu = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_menu);

        return $stmt->execute();
    }

    public function enable() {
        $query = "UPDATE " . $this->table_name . "
                SET estado = 1
                WHERE id_menu = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_menu);

        return $stmt->execute();
    }
}

<?php
class Consumo {
    private $conn;
    private $table_name = "consumo";

    public $id_consumo;
    public $fecha;
    public $observacion;
    public $id_menu;
    public $estado;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (fecha, observacion, id_menu, estado, created_by)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->fecha);
        $stmt->bindParam(2, $this->observacion);
        $stmt->bindParam(3, $this->id_menu);
        $stmt->bindParam(4, $this->estado);
        $stmt->bindParam(5, $this->created_by);

        return $stmt->execute();
    }

    public function readByDate($fecha) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE fecha = ? AND estado = true LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        return $stmt;
    }

    public function readByDateAndUser($fecha, $user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE fecha = ? AND created_by = ? AND estado = true LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->bindParam(2, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET observacion = ?, id_menu = ?, estado = ?, created_by = ?
                WHERE id_consumo = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->observacion);
        $stmt->bindParam(2, $this->id_menu);
        $stmt->bindParam(3, $this->estado);
        $stmt->bindParam(4, $this->created_by);
        $stmt->bindParam(5, $this->id_consumo);

        return $stmt->execute();
    }
}

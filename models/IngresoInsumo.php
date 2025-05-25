<?php
class IngresoInsumo {
    private $conn;
    private $table_name = "ingreso_insumo";

    public $id_ingreso_insumo;
    public $fecha;
    public $observacion;
    public $estado;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (fecha, observacion, estado, created_by)
                VALUES (?, ?, true, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->fecha);
        $stmt->bindParam(2, $this->observacion);
        $stmt->bindParam(3, $this->created_by);

        if($stmt->execute()) {
            $this->id_ingreso_insumo = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT i.*, u.nombre_completo as creado_por 
                 FROM " . $this->table_name . " i
                 LEFT JOIN usuarios u ON i.created_by = u.id
                 WHERE i.estado = true 
                 ORDER BY i.fecha DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
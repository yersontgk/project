<?php
class Asistencia {
    private $conn;
    private $table_name = "asistencia";

    public $id_asistencia;
    public $fecha;
    public $total_masculino;
    public $total_femenino;
    public $id_matricula;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (fecha, total_masculino, total_femenino, id_matricula)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->fecha);
        $stmt->bindParam(2, $this->total_masculino);
        $stmt->bindParam(3, $this->total_femenino);
        $stmt->bindParam(4, $this->id_matricula);

        return $stmt->execute();
    }

    public function readByDate($fecha) {
        $query = "SELECT a.*, m.grado, m.seccion, m.tipo 
                 FROM " . $this->table_name . " a
                 INNER JOIN matricula m ON a.id_matricula = m.id_matricula
                 WHERE a.fecha = ? AND a.estado = true
                 ORDER BY m.grado, m.seccion";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET total_masculino = ?, total_femenino = ?
                WHERE id_asistencia = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->total_masculino);
        $stmt->bindParam(2, $this->total_femenino);
        $stmt->bindParam(3, $this->id_asistencia);

        return $stmt->execute();
    }
}
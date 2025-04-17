<?php
class Matricula {
    private $conn;
    private $table_name = "matricula";

    public $id_matricula;
    public $tipo;
    public $grado;
    public $seccion;
    public $lapso_academico;
    public $total_masculino;
    public $total_femenino;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE estado = true 
                 ORDER BY grado, seccion";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readByTipo($tipo) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE tipo = ? AND estado = true 
                 ORDER BY grado, seccion";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tipo);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . "
                 WHERE id_matricula = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_matricula);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET grado = ?, seccion = ?
                WHERE id_matricula = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->grado);
        $stmt->bindParam(2, $this->seccion);
        $stmt->bindParam(3, $this->id_matricula);

        return $stmt->execute();
    }
}
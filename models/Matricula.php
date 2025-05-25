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
    public $error_message; // To store validation error messages

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

    public function readByTipoAndGrado($tipo, $grado = null) {
        if ($tipo !== 'estudiante' || $grado === null || $grado === '') {
            return $this->readByTipo($tipo);
        }

        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE tipo = ? AND grado = ? AND estado = true 
                 ORDER BY seccion";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tipo);
        $stmt->bindParam(2, $grado);
        $stmt->execute();

        return $stmt;
    }

    public function getGrados() {
        $query = "SELECT DISTINCT grado FROM " . $this->table_name . " 
                 WHERE tipo = 'estudiante' AND estado = true 
                 ORDER BY grado";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function checkGradoExists($grado) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE grado = ? AND tipo = 'estudiante' AND estado = true";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $grado);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
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

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
            (tipo, grado, seccion, lapso_academico, total_masculino, total_femenino, estado) 
            VALUES (:tipo, :grado, :seccion, :lapso_academico, :total_masculino, :total_femenino, :estado)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':grado', $this->grado);
        $stmt->bindParam(':seccion', $this->seccion);
        $stmt->bindParam(':lapso_academico', $this->lapso_academico);
        $stmt->bindParam(':total_masculino', $this->total_masculino);
        $stmt->bindParam(':total_femenino', $this->total_femenino);
        $stmt->bindParam(':estado', $this->estado);

        return $stmt->execute();
    }
}

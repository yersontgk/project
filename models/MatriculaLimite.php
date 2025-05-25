<?php
class MatriculaLimite {
    private $conn;
    private $table_name = "matricula_limite";

    public $id_limite;
    public $id_matricula;
    public $limite_masculino;
    public $limite_femenino;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (id_matricula, limite_masculino, limite_femenino)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    limite_masculino = VALUES(limite_masculino),
                    limite_femenino = VALUES(limite_femenino)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_matricula);
        $stmt->bindParam(2, $this->limite_masculino);
        $stmt->bindParam(3, $this->limite_femenino);

        return $stmt->execute();
    }

    public function readByMatricula($id_matricula) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE id_matricula = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_matricula);
        $stmt->execute();

        return $stmt;
    }
}
<?php
class ConsumoAsistencia {
    private $conn;
    private $table_name = "consumo_asistencia";

    public $id_consumo_asistencia;
    public $id_consumo;
    public $id_asistencia;
    public $id_matricula;
    public $platos_servidos;
    public $platos_devueltos;
    public $fecha;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (id_consumo, id_asistencia, id_matricula, platos_servidos, platos_devueltos, fecha)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_consumo);
        $stmt->bindParam(2, $this->id_asistencia);
        $stmt->bindParam(3, $this->id_matricula);
        $stmt->bindParam(4, $this->platos_servidos);
        $stmt->bindParam(5, $this->platos_devueltos);
        $stmt->bindParam(6, $this->fecha);

        return $stmt->execute();
    }

    public function readByKeys($id_consumo, $id_asistencia, $id_matricula, $fecha) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE id_consumo = ? AND id_asistencia = ? AND id_matricula = ? AND fecha = ?
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_consumo);
        $stmt->bindParam(2, $id_asistencia);
        $stmt->bindParam(3, $id_matricula);
        $stmt->bindParam(4, $fecha);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET platos_servidos = ?, platos_devueltos = ?
                WHERE id_consumo_asistencia = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->platos_servidos);
        $stmt->bindParam(2, $this->platos_devueltos);
        $stmt->bindParam(3, $this->id_consumo_asistencia);

        return $stmt->execute();
    }

    // New method to read all platos servidos y devueltos by fecha
    public function readByFecha($fecha) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE fecha = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        return $stmt;
    }
}

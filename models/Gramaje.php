<?php
class Gramaje {
    private $conn;
    private $table_name = "gramaje";

    public $id_gramaje;
    public $id_producto;
    public $gramaje_por_plato;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (id_producto, gramaje_por_plato)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE 
                    gramaje_por_plato = VALUES(gramaje_por_plato),
                    updated_at = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_producto);
        $stmt->bindParam(2, $this->gramaje_por_plato);

        if($stmt->execute()) {
            // Optionally, fetch last inserted id_gramaje if needed
            // $this->id_gramaje = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT g.*, p.nombre as producto_nombre, u.simbolo as unidad 
                 FROM " . $this->table_name . " g
                 INNER JOIN producto p ON g.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT g.*, p.nombre as producto_nombre, u.simbolo as unidad 
                 FROM " . $this->table_name . " g
                 INNER JOIN producto p ON g.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE g.id_producto = ?
                 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_producto);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET gramaje_por_plato = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id_producto = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->gramaje_por_plato);
        $stmt->bindParam(2, $this->id_producto);

        return $stmt->execute();
    }
}
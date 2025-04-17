<?php
class Producto {
    private $conn;
    private $table_name = "producto";

    public $id_producto;
    public $nombre;
    public $stock;
    public $stock_minimo;
    public $id_unidad;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (nombre, stock, stock_minimo, id_unidad)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->stock);
        $stmt->bindParam(3, $this->stock_minimo);
        $stmt->bindParam(4, $this->id_unidad);

        return $stmt->execute();
    }

    public function read() {
        $query = "SELECT p.*, u.nombre as unidad, u.simbolo 
                 FROM " . $this->table_name . " p
                 LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE p.estado = true 
                 ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET nombre = ?, stock = ?, stock_minimo = ?, id_unidad = ?
                WHERE id_producto = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->stock);
        $stmt->bindParam(3, $this->stock_minimo);
        $stmt->bindParam(4, $this->id_unidad);
        $stmt->bindParam(5, $this->id_producto);

        return $stmt->execute();
    }

    public function updateStock($cantidad, $esIngreso = true) {
        $query = "UPDATE " . $this->table_name . "
                SET stock = stock " . ($esIngreso ? "+" : "-") . " ?
                WHERE id_producto = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $cantidad);
        $stmt->bindParam(2, $this->id_producto);

        return $stmt->execute();
    }

    public function getBajoStock() {
        $query = "SELECT p.*, u.nombre as unidad, u.simbolo 
                 FROM " . $this->table_name . " p
                 LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE p.estado = true AND p.stock <= p.stock_minimo
                 ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
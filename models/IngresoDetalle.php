<?php
class IngresoDetalle {
    private $conn;
    private $table_name = "ingreso_detalle";

    public $id_ingreso_detalle;
    public $cantidad;
    public $id_producto;
    public $id_ingreso_insumo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (cantidad, id_producto, id_ingreso_insumo)
                VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->cantidad);
        $stmt->bindParam(2, $this->id_producto);
        $stmt->bindParam(3, $this->id_ingreso_insumo);

        return $stmt->execute();
    }

    public function getByIngresoId($id_ingreso) {
        $query = "SELECT d.*, p.nombre as producto_nombre, p.stock, u.simbolo as unidad 
                 FROM " . $this->table_name . " d
                 INNER JOIN producto p ON d.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE d.id_ingreso_insumo = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_ingreso);
        $stmt->execute();

        return $stmt;
    }
}
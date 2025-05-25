<?php
class ConsumoDetalle {
    private $conn;
    private $table_name = "consumo_detalle";

    public $id_consumo_detalle;
    public $cantidad;
    public $id_producto;
    public $id_consumo;
    public $fecha;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (cantidad, id_producto, id_consumo, fecha, created_by)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->cantidad);
        $stmt->bindParam(2, $this->id_producto);
        $stmt->bindParam(3, $this->id_consumo);
        $stmt->bindParam(4, $this->fecha);
        $stmt->bindParam(5, $this->created_by);

        return $stmt->execute();
    }

    public function readByConsumo($id_consumo) {
        $query = "SELECT cd.*, p.nombre as producto_nombre, u.simbolo as unidad
                 FROM " . $this->table_name . " cd
                 INNER JOIN producto p ON cd.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE cd.id_consumo = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_consumo);
        $stmt->execute();

        return $stmt;
    }
}
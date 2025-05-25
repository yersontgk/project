<?php
class MenuProducto {
    private $conn;
    private $table_name = "menu_producto";

    public $id_menu_producto;
    public $id_menu;
    public $id_producto;
    public $cantidad_por_plato;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (id_menu, id_producto, cantidad_por_plato)
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                    cantidad_por_plato = VALUES(cantidad_por_plato),
                    updated_at = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_menu);
        $stmt->bindParam(2, $this->id_producto);
        $stmt->bindParam(3, $this->cantidad_por_plato);

        return $stmt->execute();
    }

    public function readByMenu($id_menu) {
$query = "SELECT mp.*, p.nombre as producto_nombre, p.stock, u.simbolo as unidad
                 FROM " . $this->table_name . " mp
                 INNER JOIN producto p ON mp.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE mp.id_menu = ?
                 ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_menu);
        $stmt->execute();

        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE id_menu = ? AND id_producto = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_menu);
        $stmt->bindParam(2, $this->id_producto);

        return $stmt->execute();
    }

    public function deleteAllFromMenu($id_menu) {
        $query = "DELETE FROM " . $this->table_name . "
                WHERE id_menu = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_menu);

        return $stmt->execute();
    }
}
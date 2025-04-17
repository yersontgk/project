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
        $query = "INSERT INTO " . $this->table_name . "
                (id_menu, id_producto, cantidad_por_plato)
                VALUES (?, ?, ?)
                ON CONFLICT (id_menu, id_producto) 
                DO UPDATE SET cantidad_por_plato = EXCLUDED.cantidad_por_plato,
                             updated_at = CURRENT_TIMESTAMP
                RETURNING id_menu_producto";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id_menu);
        $stmt->bindParam(2, $this->id_producto);
        $stmt->bindParam(3, $this->cantidad_por_plato);

        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_menu_producto = $row['id_menu_producto'];
            return true;
        }
        return false;
    }

    public function readByMenu($id_menu) {
        $query = "SELECT mp.*, p.nombre as producto_nombre, u.simbolo as unidad,
                        g.gramaje_por_plato
                 FROM " . $this->table_name . " mp
                 INNER JOIN producto p ON mp.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 LEFT JOIN gramaje g ON p.id_producto = g.id_producto
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
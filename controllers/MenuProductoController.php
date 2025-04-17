<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MenuProducto.php';
require_once __DIR__ . '/../models/Producto.php';

class MenuProductoController {
    private $db;
    private $menuProducto;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->menuProducto = new MenuProducto($this->db);
        $this->producto = new Producto($this->db);
    }

    public function getProductosByMenu($id_menu) {
        return $this->menuProducto->readByMenu($id_menu);
    }

    public function updateMenuProductos($id_menu, $productos) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar productos existentes
            $this->menuProducto->deleteAllFromMenu($id_menu);
            
            // Agregar nuevos productos
            foreach ($productos as $producto) {
                $this->menuProducto->id_menu = $id_menu;
                $this->menuProducto->id_producto = $producto['id_producto'];
                $this->menuProducto->cantidad_por_plato = $producto['cantidad_por_plato'];
                
                if (!$this->menuProducto->create()) {
                    throw new Exception("Error al agregar producto al menú");
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getProductosDisponibles() {
        return $this->producto->read();
    }
}
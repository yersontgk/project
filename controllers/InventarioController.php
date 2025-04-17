<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class InventarioController {
    private $db;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
    }

    public function getAllProducts() {
        return $this->producto->read();
    }

    public function createProduct($nombre, $stock, $stock_minimo, $id_unidad) {
        $this->producto->nombre = $nombre;
        $this->producto->stock = $stock;
        $this->producto->stock_minimo = $stock_minimo;
        $this->producto->id_unidad = $id_unidad;
        
        return $this->producto->create();
    }

    public function updateProduct($id_producto, $nombre, $stock, $stock_minimo, $id_unidad) {
        $this->producto->id_producto = $id_producto;
        $this->producto->nombre = $nombre;
        $this->producto->stock = $stock;
        $this->producto->stock_minimo = $stock_minimo;
        $this->producto->id_unidad = $id_unidad;
        
        return $this->producto->update();
    }

    public function updateStock($id_producto, $cantidad, $esIngreso = true) {
        $this->producto->id_producto = $id_producto;
        return $this->producto->updateStock($cantidad, $esIngreso);
    }

    public function getProductosBajoStock() {
        return $this->producto->getBajoStock();
    }
}
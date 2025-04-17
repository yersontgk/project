<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Gramaje.php';
require_once __DIR__ . '/../models/Producto.php';

class GramajeController {
    private $db;
    private $gramaje;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->gramaje = new Gramaje($this->db);
        $this->producto = new Producto($this->db);
    }

    public function getAllGramajes() {
        return $this->gramaje->read();
    }

    public function getGramaje($id_producto) {
        $this->gramaje->id_producto = $id_producto;
        return $this->gramaje->readOne();
    }

    public function updateGramaje($id_producto, $gramaje_por_plato) {
        $this->gramaje->id_producto = $id_producto;
        $this->gramaje->gramaje_por_plato = $gramaje_por_plato;
        
        return $this->gramaje->create();
    }

    public function getProductosSinGramaje() {
        return $this->producto->getProductosSinGramaje();
    }
}
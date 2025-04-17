<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Menu.php';

class MenuController {
    private $db;
    private $menu;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->menu = new Menu($this->db);
    }

    public function getAllMenus() {
        return $this->menu->read();
    }

    public function getMenuByDate($fecha) {
        return $this->menu->readByDate($fecha);
    }

    public function createMenu($nombre, $observacion, $fecha, $created_by) {
        $this->menu->nombre = $nombre;
        $this->menu->observacion = $observacion;
        $this->menu->fecha = $fecha;
        $this->menu->created_by = $created_by;
        
        return $this->menu->create();
    }

    public function updateMenu($id_menu, $nombre, $observacion, $fecha) {
        $this->menu->id_menu = $id_menu;
        $this->menu->nombre = $nombre;
        $this->menu->observacion = $observacion;
        $this->menu->fecha = $fecha;
        
        return $this->menu->update();
    }

    public function deleteMenu($id_menu) {
        $this->menu->id_menu = $id_menu;
        return $this->menu->delete();
    }
}
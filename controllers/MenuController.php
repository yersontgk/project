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

    public function getDisabledMenus() {
        return $this->menu->readDisabled();
    }

    public function getMenuByDate($fecha) {
        return $this->menu->readByDate($fecha);
    }

    public function getMenuById($id_menu) {
        return $this->menu->readById($id_menu);
    }

    public function getMenuProducts($id_menu) {
        require_once __DIR__ . '/../models/MenuProducto.php';
        $menuProducto = new MenuProducto($this->db);
        return $menuProducto->readByMenu($id_menu);
    }

    public function createMenu($nombre, $observacion, $fecha, $created_by) {
        $this->menu->nombre = $nombre;
        $this->menu->observacion = $observacion;
        $this->menu->fecha = $fecha;
        $this->menu->created_by = $created_by;
        
        return $this->menu->create();
    }

    public function getLastInsertedId() {
        return $this->menu->id_menu;
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

    public function enableMenu($id_menu) {
        $this->menu->id_menu = $id_menu;
        return $this->menu->enable();
    }
}

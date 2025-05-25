<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Gramaje.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/InventarioController.php';
require_once __DIR__ . '/AsistenciaController.php';
require_once __DIR__ . '/ConsumoController.php';
require_once __DIR__ . '/MenuProductoController.php';

class GramajeController {
    private $db;
    private $gramaje;
    private $producto;
    private $inventarioController;
    private $asistenciaController;
    private $consumoController;
    private $menuProductoController;
    private $platosServidosController;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->gramaje = new Gramaje($this->db);
        $this->producto = new Producto($this->db);
        $this->inventarioController = new InventarioController();
        $this->asistenciaController = new AsistenciaController();
        $this->consumoController = new ConsumoController();
        $this->menuProductoController = new MenuProductoController();
        require_once __DIR__ . '/PlatosServidosController.php';
        $this->platosServidosController = new PlatosServidosController();
    }

    public function restarInsumos($fecha) {
        // Validate date is not in the future
        if ($fecha > date('Y-m-d')) {
            throw new Exception('La fecha no puede ser futura.');
        }
        try {
            $this->db->beginTransaction();

            // Get menu consumption for the date
            $consumoMenu = $this->consumoController->getConsumoWithMenuByDateAndUser($fecha, $_SESSION['user_id'])->fetch(PDO::FETCH_ASSOC);
            if (!$consumoMenu) {
                throw new Exception('No hay menú seleccionado para esta fecha');
            }

            // Get total platos servidos
            $platosServidosRecords = $this->platosServidosController->getPlatosByFecha($fecha);
            $totalPlatosServidos = 0;
            foreach ($platosServidosRecords as $record) {
                $totalPlatosServidos += $record['platos_servidos'];
            }

            // Get menu products
            $productos = $this->menuProductoController->getProductosByMenu($consumoMenu['id_menu']);
            
            while ($producto = $productos->fetch(PDO::FETCH_ASSOC)) {
                $cantidadNecesaria = $producto['cantidad_por_plato'] * $totalPlatosServidos;
                
                if ($cantidadNecesaria > 0) {
                    // Update stock and register consumption
                    if (!$this->inventarioController->updateStock($producto['id_producto'], $cantidadNecesaria, false)) {
                        throw new Exception("No es posible reducir el stock del producto: " . $producto['producto_nombre']);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
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
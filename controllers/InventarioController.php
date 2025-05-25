<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/IngresoInsumo.php';
require_once __DIR__ . '/../models/IngresoDetalle.php';
require_once __DIR__ . '/../models/Consumo.php';
require_once __DIR__ . '/../models/ConsumoDetalle.php';

class InventarioController {
    private $db;
    private $producto;
    private $ingresoInsumo;
    private $ingresoDetalle;
    private $consumo;
    private $consumoDetalle;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
        $this->ingresoInsumo = new IngresoInsumo($this->db);
        $this->ingresoDetalle = new IngresoDetalle($this->db);
        $this->consumo = new Consumo($this->db);
        $this->consumoDetalle = new ConsumoDetalle($this->db);
    }

    public function getAllProducts() {
        return $this->producto->read();
    }

    public function createProduct($nombre, $stock, $stock_minimo, $id_unidad) {
        $this->producto->nombre = $nombre;
        $this->producto->stock = $stock;
        $this->producto->stock_minimo = $stock_minimo;
        $this->producto->id_unidad = $id_unidad;
        
        if ($this->producto->create()) {
            $id_producto = $this->db->lastInsertId();
            
            // Register initial stock as an ingreso if stock > 0
            if ($stock > 0) {
                // The method registrarIngreso does not exist, so replace with updateStock call for ingreso
                $this->updateStock($id_producto, $stock, true);
            }
            
            return true;
        }
        return false;
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
        try {
            $this->db->beginTransaction();

            // Check current stock to prevent negative stock
            $currentStock = $this->producto->getStockById($id_producto);
            if (!$esIngreso && $currentStock !== null && ($currentStock - $cantidad) < 0) {
                // Cannot reduce stock below zero
                $this->db->rollBack();
                return false;
            }

            if ($esIngreso) {
                // Create ingreso_insumo record
                $this->ingresoInsumo->fecha = date('Y-m-d');
                $this->ingresoInsumo->observacion = "Ingreso de stock";
                $this->ingresoInsumo->created_by = $_SESSION['user_id'];

                if (!$this->ingresoInsumo->create()) {
                    throw new Exception("Error al registrar el ingreso");
                }

                // Create ingreso_detalle record
                $this->ingresoDetalle->cantidad = $cantidad;
                $this->ingresoDetalle->id_producto = $id_producto;
                $this->ingresoDetalle->id_ingreso_insumo = $this->ingresoInsumo->id_ingreso_insumo;

                if (!$this->ingresoDetalle->create()) {
                    throw new Exception("Error al registrar el detalle del ingreso");
                }
            } else {
                // Create or get consumo record for the current date and user
                $fecha_actual = date('Y-m-d');
                $created_by = $_SESSION['user_id'];

                // Check if consumo exists for today and user
                $query = "SELECT id_consumo FROM consumo WHERE fecha = ? AND created_by = ? AND estado = true LIMIT 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $fecha_actual);
                $stmt->bindParam(2, $created_by);
                $stmt->execute();
                $consumo = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($consumo) {
                    $id_consumo = $consumo['id_consumo'];
                } else {
                    // Create new consumo record
                    $this->consumo->fecha = $fecha_actual;
                    $this->consumo->observacion = "Salida de stock";
                    $this->consumo->estado = true;
                    $this->consumo->created_by = $created_by;

                    if (!$this->consumo->create()) {
                        throw new Exception("Error al registrar el consumo");
                    }
                    $id_consumo = $this->db->lastInsertId();
                }

                // Create consumo_detalle record linked to consumo
                $this->consumoDetalle->cantidad = $cantidad;
                $this->consumoDetalle->id_producto = $id_producto;
                $this->consumoDetalle->id_consumo = $id_consumo;
                $this->consumoDetalle->fecha = $fecha_actual;
                $this->consumoDetalle->created_by = $created_by;

                if (!$this->consumoDetalle->create()) {
                    throw new Exception("Error al registrar el detalle del consumo");
                }
            }

            // Update product stock
            $this->producto->id_producto = $id_producto;
            if (!$this->producto->updateStock($cantidad, $esIngreso)) {
                throw new Exception("Error al actualizar el stock");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getProductosBajoStock() {
        return $this->producto->getBajoStock();
    }

    public function getLastInsertedId() {
        return $this->db->lastInsertId();
    }
}
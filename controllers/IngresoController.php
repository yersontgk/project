<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/IngresoInsumo.php';
require_once __DIR__ . '/../models/IngresoDetalle.php';
require_once __DIR__ . '/../models/Producto.php';

class IngresoController {
    private $db;
    private $ingresoInsumo;
    private $ingresoDetalle;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ingresoInsumo = new IngresoInsumo($this->db);
        $this->ingresoDetalle = new IngresoDetalle($this->db);
        $this->producto = new Producto($this->db);
    }

    public function registrarIngreso($fecha, $observacion, $created_by, $productos) {
        try {
            $this->db->beginTransaction();

            // Create ingreso_insumo record
            $this->ingresoInsumo->fecha = $fecha;
            $this->ingresoInsumo->observacion = $observacion;
            $this->ingresoInsumo->created_by = $created_by;

            if (!$this->ingresoInsumo->create()) {
                throw new Exception("Error al crear el ingreso");
            }

            $id_ingreso = $this->ingresoInsumo->id_ingreso_insumo;

            // Create ingreso_detalle records and update stock
            foreach ($productos as $producto) {
                $this->ingresoDetalle->cantidad = $producto['cantidad'];
                $this->ingresoDetalle->id_producto = $producto['id_producto'];
                $this->ingresoDetalle->id_ingreso_insumo = $id_ingreso;

                if (!$this->ingresoDetalle->create()) {
                    throw new Exception("Error al registrar detalle de ingreso");
                }

                // Update product stock
                $this->producto->id_producto = $producto['id_producto'];
                if (!$this->producto->updateStock($producto['cantidad'], true)) {
                    throw new Exception("Error al actualizar stock");
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getIngresos() {
        return $this->ingresoInsumo->read();
    }

    public function getDetalleIngreso($id_ingreso) {
        return $this->ingresoDetalle->getByIngresoId($id_ingreso);
    }
}
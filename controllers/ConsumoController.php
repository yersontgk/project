<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Consumo.php';

class ConsumoController {
    private $db;
    private $consumo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->consumo = new Consumo($this->db);
    }

    public function createConsumo($fecha, $observacion, $id_menu, $estado, $created_by) {
        $this->consumo->fecha = $fecha;
        $this->consumo->observacion = $observacion;
        $this->consumo->id_menu = $id_menu;
        $this->consumo->estado = $estado;
        $this->consumo->created_by = $created_by;

        return $this->consumo->create();
    }

    public function getConsumoByDate($fecha) {
        $query = "SELECT c.*, m.nombre, m.observacion FROM consumo c
                  LEFT JOIN menu m ON c.id_menu = m.id_menu
                  WHERE c.fecha = ? AND c.estado = true
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        return $stmt;
    }

    public function getConsumoWithMenuByDate($fecha) {
        $query = "SELECT c.*, m.nombre, m.observacion FROM consumo c
                  LEFT JOIN menu m ON c.id_menu = m.id_menu
                  WHERE c.fecha = ? AND c.estado = true
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->execute();
        return $stmt;
    }

    public function updateConsumo($id_consumo, $observacion, $id_menu, $estado, $created_by) {
        $this->consumo->id_consumo = $id_consumo;
        $this->consumo->observacion = $observacion;
        $this->consumo->id_menu = $id_menu;
        $this->consumo->estado = $estado;
        $this->consumo->created_by = $created_by;

        return $this->consumo->update();
    }

    public function createOrUpdateConsumo($fecha, $observacion, $id_menu, $estado, $created_by) {
        $existingConsumoStmt = $this->getConsumoByDate($fecha);
        $existingConsumo = $existingConsumoStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingConsumo) {
            return $this->updateConsumo(
                $existingConsumo['id_consumo'],
                $observacion,
                $id_menu,
                $estado,
                $created_by
            );
        } else {
            return $this->createConsumo($fecha, $observacion, $id_menu, $estado, $created_by);
        }
    }

    public function getConsumoWithMenuByDateAndUser($fecha, $user_id) {
        $query = "SELECT c.*, m.nombre, m.observacion FROM consumo c
                  LEFT JOIN menu m ON c.id_menu = m.id_menu
                  WHERE c.fecha = ? AND c.estado = true AND c.created_by = ?
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha);
        $stmt->bindParam(2, $user_id);
        $stmt->execute();
        return $stmt;
    }
}

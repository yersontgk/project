<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/AsistenciaController.php';
require_once __DIR__ . '/MenuController.php';
require_once __DIR__ . '/InventarioController.php';
require_once __DIR__ . '/ConsumoController.php';
require_once __DIR__ . '/PlatosServidosController.php';

class ReporteController {
    private $db;
    private $asistenciaController;
    private $menuController;
    private $inventarioController;
    private $consumoController;
    private $platosServidosController;

    private function clampDateToToday($date) {
        $today = date('Y-m-d');
        if ($date > $today) {
            return $today;
        }
        return $date;
    }

    private function validateDateRange(&$fecha_inicio, &$fecha_fin) {
        $fecha_inicio = $this->clampDateToToday($fecha_inicio);
        $fecha_fin = $this->clampDateToToday($fecha_fin);
    }

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->asistenciaController = new AsistenciaController();
        $this->menuController = new MenuController();
        $this->inventarioController = new InventarioController();
        $this->consumoController = new ConsumoController();
        $this->platosServidosController = new PlatosServidosController();
    }

    // New method to get usernames who did gramaje rest filtered by date range
    public function getUsuariosQueRealizaronGramaje($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT DISTINCT u.username, u.nombre_completo
                  FROM consumo c
                  INNER JOIN usuarios u ON c.created_by = u.id
                  WHERE c.fecha BETWEEN ? AND ? AND c.estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // New method to get count of users with role 'cocinera' or 'cocinero'
    public function getCantidadCocineras() {
        $query = "SELECT COUNT(*) as cantidad FROM usuarios WHERE (rol = 'cocinera' OR rol = 'cocinero') AND estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['cantidad'] : 0;
    }

    // New method to get matricula total male + female
    public function getMatriculaTotal() {
        $query = "SELECT SUM(total_masculino + total_femenino) as total_matricula FROM matricula WHERE estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_matricula'] : 0;
    }

    // New method to get total attendance filtered by date range
    public function getTotalAsistencia($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT SUM(total_masculino + total_femenino) as total_asistencia
                  FROM asistencia
                  WHERE fecha BETWEEN ? AND ? AND estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_asistencia'] : 0;
    }

    // New method to get total platos servidos filtered by date range
    public function getTotalPlatosServidos($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT SUM(platos_servidos) as total_platos
                  FROM consumo_asistencia
                  WHERE fecha BETWEEN ? AND ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_platos'] : 0;
    }

    // New method to get total platos servidos to docentes filtered by date range
    public function getPlatosServidosDocentes($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT SUM(ca.platos_servidos) as total_docentes
                  FROM consumo_asistencia ca
                  INNER JOIN matricula m ON ca.id_matricula = m.id_matricula
                  WHERE ca.fecha BETWEEN ? AND ? AND m.tipo = 'docente'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_docentes'] : 0;
    }

    // New method to get total platos servidos to otros filtered by date range
    public function getPlatosServidosOtros($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT SUM(ca.platos_servidos) as total_otros
                  FROM consumo_asistencia ca
                  INNER JOIN matricula m ON ca.id_matricula = m.id_matricula
                  WHERE ca.fecha BETWEEN ? AND ? AND m.tipo = 'otros'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_otros'] : 0;
    }

    // New method to get count of 'otros' (casos vulnerables) from base de datos
    public function getCasosVulnerables() {
        $query = "SELECT COUNT(*) as total_otros FROM usuarios WHERE rol = 'otros' AND estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total_otros'] : 0;
    }

    // New method to get menu selected for filtered days (concatenated names)
    public function getMenuDelDia($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT GROUP_CONCAT(DISTINCT m.nombre SEPARATOR ', ') as menus
                  FROM consumo c
                  INNER JOIN menu m ON c.id_menu = m.id_menu
                  WHERE c.fecha BETWEEN ? AND ? AND c.estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['menus'] : '';
    }

    // New method to check if there was any resta de insumos in filtered date range
    public function huboRestaInsumos($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT COUNT(*) as total FROM consumo WHERE fecha BETWEEN ? AND ? AND estado = true";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['total'] > 0;
    }

    public function getAverageAttendancePerMenu($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    m.id_menu,
                    AVG(a.total_masculino + a.total_femenino) as promedio_asistencia
                  FROM menu m
                  INNER JOIN consumo c ON m.id_menu = c.id_menu
                  INNER JOIN asistencia a ON c.fecha = a.fecha
                  WHERE c.fecha BETWEEN ? AND ? AND c.estado = true AND a.estado = true
                  GROUP BY m.id_menu";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['id_menu']] = $row['promedio_asistencia'];
        }
        return $result;
    }

    public function getResumenAsistencia($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    SUM(CASE WHEN m.tipo = 'estudiante' THEN a.total_masculino + a.total_femenino ELSE 0 END) as total_estudiantes,
                    SUM(CASE WHEN m.tipo = 'docente' THEN a.total_masculino + a.total_femenino ELSE 0 END) as total_docentes,
                    COUNT(DISTINCT a.fecha) as total_dias,
                    AVG(a.total_masculino + a.total_femenino) as promedio_diario
                 FROM asistencia a
                 INNER JOIN matricula m ON a.id_matricula = m.id_matricula
                 WHERE a.fecha BETWEEN ? AND ? AND a.estado = true";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAsistenciaPorDia($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    a.fecha,
                    SUM(a.total_masculino + a.total_femenino) as total_asistencia,
                    m.tipo
                 FROM asistencia a
                 INNER JOIN matricula m ON a.id_matricula = m.id_matricula
                 WHERE a.fecha BETWEEN ? AND ? AND a.estado = true
                 GROUP BY a.fecha, m.tipo
                 ORDER BY a.fecha";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt;
    }

    public function getConsumoInsumos($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    p.nombre as producto,
                    u.simbolo as unidad,
                    SUM(cd.cantidad) as total_consumido
                 FROM consumo_detalle cd
                 INNER JOIN consumo c ON cd.id_consumo = c.id_consumo
                 INNER JOIN producto p ON cd.id_producto = p.id_producto
                 INNER JOIN unidad u ON p.id_unidad = u.id_unidad
                 WHERE c.fecha BETWEEN ? AND ? AND c.estado = true
                 GROUP BY p.id_producto, p.nombre, u.simbolo
                 ORDER BY total_consumido DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt;
    }

    public function getMenusPopulares($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    m.id_menu,
                    m.nombre as menu,
                    COUNT(DISTINCT c.fecha) as veces_utilizado,
                    MAX(c.fecha) as ultima_fecha
                 FROM menu m
                 INNER JOIN consumo c ON m.id_menu = c.id_menu
                 WHERE c.fecha BETWEEN ? AND ? AND c.estado = true
                 GROUP BY m.id_menu, m.nombre
                 ORDER BY veces_utilizado DESC
                 LIMIT 10";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt;
    }

    public function getEstadisticasGenerales($fecha_inicio, $fecha_fin) {
        $this->validateDateRange($fecha_inicio, $fecha_fin);
        $query = "SELECT 
                    COUNT(DISTINCT p.id_producto) as total_productos,
                    SUM(CASE WHEN p.stock <= p.stock_minimo THEN 1 ELSE 0 END) as productos_bajo_stock,
                    SUM(cd.cantidad) as consumo_total
                 FROM producto p
                 LEFT JOIN consumo_detalle cd ON p.id_producto = cd.id_producto
                 LEFT JOIN consumo c ON cd.id_consumo = c.id_consumo AND c.fecha BETWEEN ? AND ?
                 WHERE p.estado = true";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $fecha_inicio);
        $stmt->bindParam(2, $fecha_fin);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
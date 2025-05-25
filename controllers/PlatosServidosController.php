<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Consumo.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/ConsumoAsistencia.php';

class PlatosServidosController {
    private $db;
    private $consumo;
    private $asistencia;
    private $consumoAsistencia;
    private $matricula;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->consumo = new Consumo($this->db);
        $this->asistencia = new Asistencia($this->db);
        $this->consumoAsistencia = new ConsumoAsistencia($this->db);
        require_once __DIR__ . '/../models/Matricula.php';
        $this->matricula = new Matricula($this->db);
    }

    public function registrarPlatos($fecha, $platos_servidos, $platos_devueltos, $tipo = 'estudiante', $grado = null) {
        // Get or create consumo for the date and user
        $user_id = $_SESSION['user_id'];
        $consumoStmt = $this->consumo->readByDateAndUser($fecha, $user_id);
        $consumo = $consumoStmt->fetch(PDO::FETCH_ASSOC);

        if (!$consumo) {
            $this->consumo->fecha = $fecha;
            // Removed observation assignment
            $this->consumo->id_menu = null; // or set appropriately if needed
            $this->consumo->estado = true;
            $this->consumo->created_by = $user_id;
            $this->consumo->create();

            // Get the newly created consumo id
            $consumoStmt = $this->consumo->readByDateAndUser($fecha, $user_id);
            $consumo = $consumoStmt->fetch(PDO::FETCH_ASSOC);
        }

        $id_consumo = $consumo['id_consumo'];

        // Get matriculas filtered by tipo and grado
        $matriculaStmt = $this->matricula->readByTipoAndGrado($tipo, $grado);
        $matriculas = $matriculaStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($matriculas as $matricula) {
            $id_matricula = $matricula['id_matricula'];

            // Check if asistencia exists for this matricula and date
            $asistenciaStmt = $this->asistencia->readByDateAndMatricula($fecha, $id_matricula);
            $asistencia = $asistenciaStmt->fetch(PDO::FETCH_ASSOC);

            if (!$asistencia) {
                // Create asistencia record if not exists
                $this->asistencia->fecha = $fecha;
                $this->asistencia->total_masculino = 0;
                $this->asistencia->total_femenino = 0;
                $this->asistencia->id_matricula = $id_matricula;
                $this->asistencia->estado = true;
                $this->asistencia->create();

                // Fetch the newly created asistencia
                $asistenciaStmt = $this->asistencia->readByDateAndMatricula($fecha, $id_matricula);
                $asistencia = $asistenciaStmt->fetch(PDO::FETCH_ASSOC);
            }

            $id_asistencia = $asistencia['id_asistencia'];

            // Get platos servidos and devueltos for this matricula from input arrays
            $platos_servidos_val = isset($platos_servidos[$id_matricula]) ? $platos_servidos[$id_matricula] : 0;
            $platos_devueltos_val = isset($platos_devueltos[$id_matricula]) ? $platos_devueltos[$id_matricula] : 0;

            // Skip saving if both values are zero
            if ($platos_servidos_val == 0 && $platos_devueltos_val == 0) {
                continue;
            }

            $this->consumoAsistencia->id_consumo = $id_consumo;
            $this->consumoAsistencia->id_asistencia = $id_asistencia;
            $this->consumoAsistencia->id_matricula = $id_matricula;
            $this->consumoAsistencia->platos_servidos = $platos_servidos_val;
            $this->consumoAsistencia->platos_devueltos = $platos_devueltos_val;
            $this->consumoAsistencia->fecha = $fecha;

            // Check if record exists
            $existingStmt = $this->consumoAsistencia->readByKeys($id_consumo, $id_asistencia, $id_matricula, $fecha);
            $existingRecord = $existingStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRecord) {
                // Update existing record
                $this->consumoAsistencia->id_consumo_asistencia = $existingRecord['id_consumo_asistencia'];
                $this->consumoAsistencia->update();
            } else {
                // Create new record
                $this->consumoAsistencia->create();
            }
        }

        return true;
    }

    public function getPlatosByFecha($fecha) {
        $stmt = $this->consumoAsistencia->readByFecha($fecha);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}

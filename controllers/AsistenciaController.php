<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Matricula.php';

class AsistenciaController {
    private $db;
    private $asistencia;
    private $matricula;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->asistencia = new Asistencia($this->db);
        $this->matricula = new Matricula($this->db);
    }

    public function getMatriculas($tipo = 'estudiante', $grado = null) {
        return $this->matricula->readByTipoAndGrado($tipo, $grado);
    }

    public function getGrados() {
        return $this->matricula->getGrados();
    }

    public function registrarAsistencia($fecha, $asistencias) {
        try {
            foreach ($asistencias as $asistencia) {
                $this->asistencia->fecha = $fecha;
                $this->asistencia->total_masculino = $asistencia['total_masculino'];
                $this->asistencia->total_femenino = $asistencia['total_femenino'];
                $this->asistencia->id_matricula = $asistencia['id_matricula'];

                // Check if attendance record exists for the date and matricula
                $stmt = $this->asistencia->readByDateAndMatricula($fecha, $asistencia['id_matricula']);
                $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingRecord) {
                    // Update existing record
                    $this->asistencia->id_asistencia = $existingRecord['id_asistencia'];
                    if (!$this->asistencia->update()) {
                        throw new Exception("Error al actualizar la asistencia");
                    }
                } else {
                    // Create new record
                    if (!$this->asistencia->create()) {
                        throw new Exception("Error al registrar la asistencia");
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAsistenciasPorFecha($fecha) {
        return $this->asistencia->readByDate($fecha);
    }

    public function actualizarMatricula($id_matricula, $grado, $seccion) {
        $this->matricula->id_matricula = $id_matricula;
        $this->matricula->grado = $grado;
        $this->matricula->seccion = $seccion;
        return $this->matricula->update();
    }

    public function registrarPlatos($fecha, $platos_servidos, $platos_devueltos, $observacion, $tipo = 'estudiante', $grado = null) {
        require_once __DIR__ . '/PlatosServidosController.php';
        $platosServidosController = new PlatosServidosController();
        return $platosServidosController->registrarPlatos($fecha, $platos_servidos, $platos_devueltos, $tipo, $grado);
    }

    public function registrarMatricula($data) {
        $this->matricula->tipo = $data['tipo'];
        $this->matricula->grado = $data['grado'] ?? null;
        $this->matricula->seccion = $data['seccion'] ?? null;
        $this->matricula->lapso_academico = $data['lapso_academico'] ?? null;
        $this->matricula->total_masculino = $data['total_masculino'];
        $this->matricula->total_femenino = $data['total_femenino'];
        $this->matricula->estado = 1; // activo por defecto

        return $this->matricula->create();
    }
}

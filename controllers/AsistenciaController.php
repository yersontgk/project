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

    public function getMatriculas($tipo = 'estudiante') {
        return $this->matricula->readByTipo($tipo);
    }

    public function registrarAsistencia($fecha, $asistencias) {
        try {
            foreach ($asistencias as $asistencia) {
                $this->asistencia->fecha = $fecha;
                $this->asistencia->total_masculino = $asistencia['total_masculino'];
                $this->asistencia->total_femenino = $asistencia['total_femenino'];
                $this->asistencia->id_matricula = $asistencia['id_matricula'];
                
                if (!$this->asistencia->create()) {
                    throw new Exception("Error al registrar la asistencia");
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

    public function getFactorGramaje() {
        // Por ahora retornamos un valor por defecto
        // En una implementación real, esto vendría de la base de datos
        return 100;
    }
}
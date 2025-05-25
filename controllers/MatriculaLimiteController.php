<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MatriculaLimite.php';

class MatriculaLimiteController {
    private $db;
    private $matriculaLimite;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->matriculaLimite = new MatriculaLimite($this->db);
    }

    public function setLimite($id_matricula, $limite_masculino, $limite_femenino) {
        $this->matriculaLimite->id_matricula = $id_matricula;
        $this->matriculaLimite->limite_masculino = $limite_masculino;
        $this->matriculaLimite->limite_femenino = $limite_femenino;
        
        return $this->matriculaLimite->create();
    }

    public function getLimite($id_matricula) {
        return $this->matriculaLimite->readByMatricula($id_matricula);
    }
}
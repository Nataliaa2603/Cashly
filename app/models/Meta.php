<?php

require_once __DIR__ . '/../../config/database.php';

class Meta {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodas($usuario_id) {
        $stmt = $this->db->prepare("SELECT * FROM metas WHERE usuario_id = :usuario_id ORDER BY id DESC");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($usuario_id, $nombre, $monto_objetivo, $monto_actual) {
        // Se omiten columnas incompatibles para prevenir el error de SQL
        $stmt = $this->db->prepare("INSERT INTO metas (usuario_id, nombre, monto_objetivo, monto_actual) VALUES (:usuario_id, :nombre, :monto_objetivo, :monto_actual)");
        return $stmt->execute([
            ':usuario_id'     => $usuario_id,
            ':nombre'         => $nombre,
            ':monto_objetivo' => $monto_objetivo,
            ':monto_actual'   => $monto_actual
        ]);
    }

    public function abonar($id, $usuario_id, $monto) {
        $stmt = $this->db->prepare("UPDATE metas SET monto_actual = monto_actual + :monto WHERE id = :id AND usuario_id = :usuario_id");
        return $stmt->execute([
            ':monto'      => $monto,
            ':id'         => $id,
            ':usuario_id' => $usuario_id
        ]);
    }

    public function eliminar($id, $usuario_id) {
        $stmt = $this->db->prepare("DELETE FROM metas WHERE id = :id AND usuario_id = :usuario_id");
        return $stmt->execute([
            ':id'         => $id,
            ':usuario_id' => $usuario_id
        ]);
    }
}
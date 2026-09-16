<?php

require_once __DIR__ . '/../../config/database.php';

class Cliente {
    private $db;

    public function __construct() {
        // Obtenemos la conexión desde la clase Database
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos($usuario_id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE usuario_id = :usuario_id ORDER BY id DESC");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($usuario_id, $nombre, $email, $telefono) {
        $stmt = $this->db->prepare("INSERT INTO clientes (usuario_id, nombre, email, telefono) VALUES (:usuario_id, :nombre, :email, :telefono)");
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':nombre'     => $nombre,
            ':email'      => $email,
            ':telefono'   => $telefono
        ]);
    }

    public function eliminar($id, $usuario_id) {
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = :id AND usuario_id = :usuario_id");
        return $stmt->execute([
            ':id'         => $id,
            ':usuario_id' => $usuario_id
        ]);
    }
}
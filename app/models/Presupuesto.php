<?php
require_once __DIR__ . '/../../config/database.php';

class Presupuesto {
    private $db;
    private $columnaNombre = 'categoria';

    public function __construct() {
        $this->db = Database::getConnection();
        $this->detectarColumnaNombre();
    }

    private function detectarColumnaNombre() {
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM presupuestos");
            $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach (['categoria', 'nombre', 'titulo', 'descripcion'] as $columna) {
                if (in_array($columna, $columnas, true)) {
                    $this->columnaNombre = $columna;
                    return;
                }
            }
        } catch (Exception $e) {}
    }

    public function obtenerTodos($usuario_id) {
        $stmt = $this->db->prepare("SELECT *, {$this->columnaNombre} AS categoria_nombre FROM presupuestos WHERE usuario_id = :usuario_id ORDER BY id DESC");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id, $usuario_id) {
        $stmt = $this->db->prepare("SELECT * FROM presupuestos WHERE id = :id AND usuario_id = :usuario_id LIMIT 1");
        $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($usuario_id, $nombre_concepto, $monto_limite, $monto_gastado = 0) {
        $sql = "INSERT INTO presupuestos (usuario_id, {$this->columnaNombre}, monto_limite, monto_gastado) VALUES (:usuario_id, :concepto, :monto_limite, :monto_gastado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':concepto' => $nombre_concepto,
            ':monto_limite' => $monto_limite,
            ':monto_gastado' => $monto_gastado
        ]);
    }

    public function actualizar($id, $usuario_id, $nombre_concepto, $monto_limite, $monto_gastado) {
        $sql = "UPDATE presupuestos SET {$this->columnaNombre} = :concepto, monto_limite = :monto_limite, monto_gastado = :monto_gastado WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':concepto' => $nombre_concepto,
            ':monto_limite' => $monto_limite,
            ':monto_gastado' => $monto_gastado,
            ':id' => $id,
            ':usuario_id' => $usuario_id
        ]);
    }

    public function agregarGasto($id, $usuario_id, $monto) {
        $stmt = $this->db->prepare("UPDATE presupuestos SET monto_gastado = monto_gastado + :monto WHERE id = :id AND usuario_id = :usuario_id");
        return $stmt->execute([':monto' => $monto, ':id' => $id, ':usuario_id' => $usuario_id]);
    }

    public function eliminar($id, $usuario_id) {
        $stmt = $this->db->prepare("DELETE FROM presupuestos WHERE id = :id AND usuario_id = :usuario_id");
        return $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
    }
}

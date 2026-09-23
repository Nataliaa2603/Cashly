<?php
require_once __DIR__ . '/../../config/database.php';

class Calendario {

    public static function obtenerGastosPorMes($userId, $mes, $anio) {
        $db = Database::getConnection();
        $sql = "SELECT fecha, SUM(monto) AS total_dia 
                FROM movimientos 
                WHERE usuario_id = :user_id 
                  AND tipo = 'gasto' 
                  AND MONTH(fecha) = :mes 
                  AND YEAR(fecha) = :anio 
                GROUP BY fecha";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':mes'     => $mes,
            ':anio'    => $anio
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerRecordatoriosPorMes($userId, $mes, $anio) {
        $db = Database::getConnection();
        $sql = "SELECT r.*, c.nombre AS categoria_nombre 
                FROM recordatorios r 
                LEFT JOIN categorias c ON r.categoria_id = c.id 
                WHERE r.usuario_id = :user_id 
                  AND MONTH(r.fecha_pago) = :mes 
                  AND YEAR(r.fecha_pago) = :anio 
                ORDER BY r.fecha_pago ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':mes'     => $mes,
            ':anio'    => $anio
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function crearRecordatorio($datos) {
        $db = Database::getConnection();
        $sql = "INSERT INTO recordatorios (usuario_id, titulo, monto, fecha_pago, categoria_id) 
                VALUES (:usuario_id, :titulo, :monto, :fecha_pago, :categoria_id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':usuario_id'   => $datos['usuario_id'],
            ':titulo'       => $datos['titulo'],
            ':monto'        => $datos['monto'],
            ':fecha_pago'   => $datos['fecha_pago'],
            ':categoria_id' => $datos['categoria_id']
        ]);
    }
}
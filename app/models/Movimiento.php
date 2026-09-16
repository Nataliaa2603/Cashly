<?php
require_once __DIR__ . '/../../config/database.php';

class Movimiento {

    public static function obtenerRecientes($userId, $limite = 8) {
        $db = Database::getConnection();
        $sql = "SELECT m.*, c.nombre AS categoria_nombre 
                FROM movimientos m 
                LEFT JOIN categorias c ON m.categoria_id = c.id 
                WHERE m.usuario_id = :user_id 
                ORDER BY m.fecha DESC, m.id DESC 
                LIMIT :limite";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerIngresosTotales($userId) {
        $db = Database::getConnection();
        $sql = "SELECT COALESCE(SUM(monto), 0) AS total 
                FROM movimientos 
                WHERE usuario_id = :user_id AND tipo = 'ingreso'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float)($resultado['total'] ?? 0);
    }

    public static function obtenerGastosTotales($userId) {
        $db = Database::getConnection();
        $sql = "SELECT COALESCE(SUM(monto), 0) AS total 
                FROM movimientos 
                WHERE usuario_id = :user_id AND tipo = 'gasto'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float)($resultado['total'] ?? 0);
    }

    public static function obtenerBalanceTotal($userId) {
        $ingresos = self::obtenerIngresosTotales($userId);
        $gastos = self::obtenerGastosTotales($userId);
        
        return $ingresos - $gastos;
    }

    public static function obtenerCategorias() {
        $db = Database::getConnection();
        $sql = "SELECT * FROM categorias ORDER BY tipo ASC, nombre ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear(array $datos) {
        $db = Database::getConnection();
        $sql = "INSERT INTO movimientos (usuario_id, tipo, monto, categoria_id, metodo_pago, fecha, descripcion) 
                VALUES (:usuario_id, :tipo, :monto, :categoria_id, :metodo_pago, :fecha, :descripcion)";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':usuario_id'   => $datos['usuario_id'],
            ':tipo'         => $datos['tipo'],
            ':monto'        => $datos['monto'],
            ':categoria_id' => $datos['categoria_id'],
            ':metodo_pago'  => $datos['metodo_pago'],
            ':fecha'        => $datos['fecha'],
            ':descripcion'  => $datos['descripcion']
        ]);
    }
}
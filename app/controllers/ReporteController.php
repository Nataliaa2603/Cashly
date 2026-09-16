<?php
require_once __DIR__ . '/../models/Movimiento.php';

class ReporteController {

    private function verificarSesion(): int {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /cashly/public/index.php?url=login");
            exit;
        }
        return (int) $_SESSION['user_id'];
    }

    /**
     * Genera y descarga un archivo CSV con todos los movimientos del usuario.
     */
    public function exportarExcel(): void {
        $userId = $this->verificarSesion();

        // Obtener todos los movimientos del usuario logueado
        $movimientos = Movimiento::obtenerRecientes($userId, 1000); // Límite alto para reporte completo

        $nombreArchivo = "Cashly_Reporte_" . date('Y-m-d_H-i') . ".csv";

        // Configurar cabeceras HTTP para descarga directa
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 para que Excel reconozca tildes y caracteres especiales en español
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Encabezados de la tabla Excel
        fputcsv($output, ['ID', 'Fecha', 'Tipo', 'Categoría', 'Método de Pago', 'Descripción', 'Monto (COP)'], ';');

        // Filas de datos
        foreach ($movimientos as $mov) {
            fputcsv($output, [
                $mov['id'],
                date('d/m/Y', strtotime($mov['fecha'])),
                strtoupper($mov['tipo']),
                $mov['categoria_nombre'],
                strtoupper($mov['metodo_pago']),
                $mov['descripcion'] ?: 'Sin descripción',
                $mov['monto']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
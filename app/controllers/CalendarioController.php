<?php
require_once __DIR__ . '/../models/Calendario.php';

class CalendarioController {

    private function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=login");
            exit;
        }
        return (int)$_SESSION['user_id'];
    }

    public function index() {
        $userId = $this->verificarSesion();

        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
        $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');

        $gastosDiarios = Calendario::obtenerGastosPorMes($userId, $mes, $anio);
        $recordatorios = Calendario::obtenerRecordatoriosPorMes($userId, $mes, $anio);
        
        // Obtener categorías directamente con fallback de seguridad
        $db = Database::getConnection();
        $stmtCat = $db->query("SELECT * FROM categorias ORDER BY nombre ASC");
        $categorias = $stmtCat ? $stmtCat->fetchAll(PDO::FETCH_ASSOC) : [];

        $mapaGastos = [];
        foreach ($gastosDiarios as $g) {
            $mapaGastos[$g['fecha']] = $g['total_dia'];
        }

        $mapaRecordatorios = [];
        foreach ($recordatorios as $r) {
            $mapaRecordatorios[$r['fecha_pago']][] = $r;
        }

        require_once __DIR__ . '/../views/calendario/index.php';
    }

    public function guardarRecordatorio() {
        $userId = $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'usuario_id'   => $userId,
                'titulo'       => htmlspecialchars(trim($_POST['titulo'] ?? ''), ENT_QUOTES, 'UTF-8'),
                'monto'        => (float)($_POST['monto'] ?? 0),
                'fecha_pago'   => $_POST['fecha_pago'] ?? date('Y-m-d'),
                'categoria_id' => !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null
            ];

            if (!empty($datos['titulo']) && $datos['monto'] > 0) {
                Calendario::crearRecordatorio($datos);
            }
        }
        header("Location: index.php?url=calendario");
        exit;
    }
}
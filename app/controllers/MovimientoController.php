<?php
require_once __DIR__ . '/../models/Movimiento.php';

class MovimientoController {

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
     * Procesa la creación de un nuevo movimiento (ingreso o gasto).
     */
    public function guardar(): void {
        $userId = $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaId = filter_var($_POST['categoria_id'] ?? null, FILTER_VALIDATE_INT);
            $monto       = filter_var($_POST['monto'] ?? null, FILTER_VALIDATE_FLOAT);
            $tipo        = in_array($_POST['tipo'] ?? '', ['ingreso', 'gasto']) ? $_POST['tipo'] : 'gasto';
            $metodoPago  = in_array($_POST['metodo_pago'] ?? '', ['nequi', 'daviplata', 'pse', 'efectivo', 'tarjeta', 'transferencia']) 
                           ? $_POST['metodo_pago'] 
                           : 'efectivo';
            $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
            $fecha       = $_POST['fecha'] ?? date('Y-m-d');

            if ($categoriaId && $monto && $monto > 0) {
                Movimiento::crear([
                    'usuario_id'   => $userId,
                    'categoria_id' => $categoriaId,
                    'monto'        => $monto,
                    'tipo'         => $tipo,
                    'metodo_pago'  => $metodoPago,
                    'descripcion'  => $descripcion,
                    'fecha'        => $fecha
                ]);
            }
        }

        header("Location: /cashly/public/index.php?url=dashboard");
        exit;
    }

    /**
     * Procesa la edición de un movimiento existente.
     */
    public function actualizar(): void {
        $userId = $this->verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $categoriaId = filter_var($_POST['categoria_id'] ?? null, FILTER_VALIDATE_INT);
            $monto       = filter_var($_POST['monto'] ?? null, FILTER_VALIDATE_FLOAT);
            $tipo        = in_array($_POST['tipo'] ?? '', ['ingreso', 'gasto']) ? $_POST['tipo'] : 'gasto';
            $metodoPago  = in_array($_POST['metodo_pago'] ?? '', ['nequi', 'daviplata', 'pse', 'efectivo', 'tarjeta', 'transferencia']) 
                           ? $_POST['metodo_pago'] 
                           : 'efectivo';
            $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
            $fecha       = $_POST['fecha'] ?? date('Y-m-d');

            if ($id && $categoriaId && $monto && $monto > 0) {
                Movimiento::actualizar([
                    'id'           => $id,
                    'usuario_id'   => $userId,
                    'categoria_id' => $categoriaId,
                    'monto'        => $monto,
                    'tipo'         => $tipo,
                    'metodo_pago'  => $metodoPago,
                    'descripcion'  => $descripcion,
                    'fecha'        => $fecha
                ]);
            }
        }

        header("Location: /cashly/public/index.php?url=dashboard");
        exit;
    }

    /**
     * Elimina un movimiento por su ID de forma segura.
     */
    public function eliminar(): void {
        $userId = $this->verificarSesion();
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id) {
            Movimiento::eliminar($id, $userId);
        }

        header("Location: /cashly/public/index.php?url=dashboard");
        exit;
    }
}
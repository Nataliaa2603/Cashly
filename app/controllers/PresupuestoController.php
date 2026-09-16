<?php
require_once __DIR__ . '/../models/Presupuesto.php';

class PresupuestoController {
    public function index() {
        $presupuestoModel = new Presupuesto();
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $db = Database::getConnection();
        $presupuestoEditar = null;

        // Compatibilidad con la estructura que ya tiene Cashly.
        try { $db->exec("ALTER TABLE presupuestos ADD COLUMN monto_gastado DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER monto_limite"); } catch (Exception $e) {}
        try { $db->exec("ALTER TABLE presupuestos MODIFY COLUMN categoria_id INT(11) NULL"); } catch (Exception $e) {}

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'eliminar') {
            $presupuestoModel->eliminar((int)$_GET['id'], $usuario_id);
            header('Location: index.php?url=presupuestos'); exit;
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar') {
            $presupuestoEditar = $presupuestoModel->obtenerPorId((int)$_GET['id'], $usuario_id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'crear';
            if ($action === 'gasto') {
                $id = (int)($_POST['presupuesto_id'] ?? 0);
                $monto = (float)($_POST['monto_gasto'] ?? 0);
                if ($id > 0 && $monto > 0) $presupuestoModel->agregarGasto($id, $usuario_id, $monto);
            } elseif ($action === 'actualizar') {
                $id = (int)($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $limite = (float)($_POST['monto_limite'] ?? 0);
                $gastado = (float)($_POST['monto_gastado'] ?? 0);
                if ($id > 0 && $nombre !== '' && $limite > 0 && $gastado >= 0) $presupuestoModel->actualizar($id, $usuario_id, $nombre, $limite, $gastado);
            } else {
                $nombre = trim($_POST['nombre'] ?? '');
                $limite = (float)($_POST['monto_limite'] ?? 0);
                $gastado = (float)($_POST['monto_gastado'] ?? 0);
                if ($nombre !== '' && $limite > 0 && $gastado >= 0) $presupuestoModel->crear($usuario_id, $nombre, $limite, $gastado);
            }
            header('Location: index.php?url=presupuestos'); exit;
        }

        $presupuestos = $presupuestoModel->obtenerTodos($usuario_id);
        require_once __DIR__ . '/../views/presupuestos/index.php';
    }
}

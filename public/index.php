<?php
// Activar reporte de errores para ver el fallo exacto
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ... resto del código ...
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ClienteController.php';
require_once __DIR__ . '/../app/controllers/PresupuestoController.php';
require_once __DIR__ . '/../app/controllers/MetaController.php';
require_once __DIR__ . '/../app/controllers/CalendarioController.php';

$url = $_GET['url'] ?? 'dashboard';

switch ($url) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->login();
        } else {
            require_once __DIR__ . '/../app/views/auth/login.php';
        }
        break;

    case 'registro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->registrar();
        } else {
            require_once __DIR__ . '/../app/views/auth/registro.php';
        }
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'guardar-movimiento':
        (new DashboardController())->guardarMovimiento();
        break;

    case 'clientes':
        (new ClienteController())->index();
        break;

    case 'guardar-cliente':
        (new ClienteController())->guardar();
        break;

    case 'eliminar-cliente':
        (new ClienteController())->eliminar();
        break;

    case 'presupuestos':
        (new PresupuestoController())->index();
        break;

    case 'guardar-presupuesto':
        (new PresupuestoController())->guardar();
        break;

    case 'metas':
        (new MetaController())->index();
        break;

    case 'guardar-meta':
        (new MetaController())->guardar();
        break;

    // --- RUTAS DE CALENDARIO Y RECORDATORIOS ---
    case 'calendario':
        (new CalendarioController())->index();
        break;

    case 'guardar-recordatorio':
        (new CalendarioController())->guardarRecordatorio();
        break;

    default:
        header("Location: index.php?url=dashboard");
        exit;
}
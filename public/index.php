<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recuperar una sesión si el usuario activó “Recordarme”.
if (!isset($_SESSION['usuario_id']) && !empty($_COOKIE['cashly_remember'])) {
    require_once __DIR__ . '/../app/controllers/AuthController.php';
    (new AuthController())->intentarRecordar();
}

$url = $_GET['url'] ?? 'login';

// Proteger rutas si no ha iniciado sesión
$rutasPublicas = ['login', 'registro'];
if (!isset($_SESSION['usuario_id']) && !in_array($url, $rutasPublicas)) {
    header("Location: index.php?url=login");
    exit;
}

switch ($url) {
    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'registro':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->registro();
        break;

    case 'logout':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/../app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'metas':
        require_once __DIR__ . '/../app/controllers/MetaController.php';
        $controller = new MetaController();
        $controller->index();
        break;

    case 'presupuestos':
        require_once __DIR__ . '/../app/controllers/PresupuestoController.php';
        $controller = new PresupuestoController();
        $controller->index();
        break;

    case 'clientes':
        require_once __DIR__ . '/../app/controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->index();
        break;

    default:
        header("Location: index.php?url=dashboard");
        exit;
}
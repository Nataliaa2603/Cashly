<?php
class ClienteController {
    private function obtenerConexionBD() {
        require_once __DIR__ . '/../../config/database.php';
        return Database::getConnection();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = $this->obtenerConexionBD();
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $clienteEditar = null;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS clientes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL DEFAULT 1,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                telefono VARCHAR(20) NULL,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            try { $db->exec("ALTER TABLE clientes ADD COLUMN empresa VARCHAR(100) NULL AFTER telefono"); } catch (Exception $e) {}
        } catch (Exception $e) {
            die("Error preparando la tabla de clientes: " . htmlspecialchars($e->getMessage()));
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'eliminar') {
            $stmt = $db->prepare("DELETE FROM clientes WHERE id = ? AND usuario_id = ?");
            $stmt->execute([(int)$_GET['id'], $usuario_id]);
            header("Location: index.php?url=clientes"); exit;
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar') {
            $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ? AND usuario_id = ? LIMIT 1");
            $stmt->execute([(int)$_GET['id'], $usuario_id]);
            $clienteEditar = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'crear';
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $empresa = trim($_POST['empresa'] ?? '');

            if ($nombre !== '' && $email !== '') {
                if ($action === 'actualizar') {
                    $stmt = $db->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, empresa = ? WHERE id = ? AND usuario_id = ?");
                    $stmt->execute([$nombre, $email, $telefono, $empresa, (int)($_POST['id'] ?? 0), $usuario_id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO clientes (usuario_id, nombre, email, telefono, empresa) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$usuario_id, $nombre, $email, $telefono, $empresa]);
                }
            }
            header("Location: index.php?url=clientes"); exit;
        }

        $stmt = $db->prepare("SELECT * FROM clientes WHERE usuario_id = ? ORDER BY id DESC");
        $stmt->execute([$usuario_id]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/clientes/index.php';
    }
}

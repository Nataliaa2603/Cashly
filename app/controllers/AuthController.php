<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private function db() {
        return Database::getConnection();
    }

    private function crearTablaRecordarme() {
        $db = $this->db();
        $db->exec("CREATE TABLE IF NOT EXISTS remember_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            selector VARCHAR(32) NOT NULL UNIQUE,
            token_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(usuario_id),
            INDEX(expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function guardarRecordarme($usuarioId) {
        $this->crearTablaRecordarme();
        $db = $this->db();
        $selector = bin2hex(random_bytes(12));
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $stmt = $db->prepare("INSERT INTO remember_tokens (usuario_id, selector, token_hash, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuarioId, $selector, $hash, $expires]);

        setcookie('cashly_remember', $selector . ':' . $token, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private function borrarRecordarme() {
        if (!empty($_COOKIE['cashly_remember'])) {
            $parts = explode(':', $_COOKIE['cashly_remember'], 2);
            if (count($parts) === 2) {
                try {
                    $stmt = $this->db()->prepare("DELETE FROM remember_tokens WHERE selector = ?");
                    $stmt->execute([$parts[0]]);
                } catch (Exception $e) {}
            }
        }
        setcookie('cashly_remember', '', time() - 3600, '/');
    }

    public function intentarRecordar() {
        if (!empty($_SESSION['usuario_id']) || empty($_COOKIE['cashly_remember'])) return;
        $parts = explode(':', $_COOKIE['cashly_remember'], 2);
        if (count($parts) !== 2) return;
        try {
            $stmt = $this->db()->prepare("SELECT r.usuario_id, u.nombre FROM remember_tokens r INNER JOIN usuarios u ON u.id = r.usuario_id WHERE r.selector = ? AND r.token_hash = ? AND r.expires_at > NOW() LIMIT 1");
            $stmt->execute([$parts[0], hash('sha256', $parts[1])]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['usuario_id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
            } else {
                $this->borrarRecordarme();
            }
        } catch (Exception $e) {}
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $recordarme = isset($_POST['recordarme']);

            if (!empty($email) && !empty($password)) {
                $usuarioModel = new Usuario();
                $usuario = $usuarioModel->obtenerPorEmail($email);

                if ($usuario && password_verify($password, $usuario['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    if ($recordarme) {
                        $this->guardarRecordarme($usuario['id']);
                    } else {
                        $this->borrarRecordarme();
                    }
                    header("Location: index.php?url=dashboard");
                    exit;
                } else {
                    $error = "Correo o contraseña incorrectos.";
                }
            } else {
                $error = "Por favor completa todos los campos.";
            }
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($nombre) && !empty($email) && !empty($password)) {
                $usuarioModel = new Usuario();
                if ($usuarioModel->obtenerPorEmail($email)) {
                    $error = "El correo ya está registrado.";
                } else {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    if ($usuarioModel->crear($nombre, $email, $passwordHash)) {
                        header("Location: index.php?url=login&registro=exito");
                        exit;
                    } else {
                        $error = "Error al crear la cuenta.";
                    }
                }
            } else {
                $error = "Por favor completa todos los campos.";
            }
        }
        require_once __DIR__ . '/../views/auth/registro.php';
    }

    public function logout() {
        $this->borrarRecordarme();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header("Location: index.php?url=login");
        exit;
    }
}

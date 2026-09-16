<?php
class MetaController {
    private function obtenerConexion() {
        require_once __DIR__ . '/../../config/database.php';
        return Database::getConnection();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = $this->obtenerConexion();
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $movimientoEditar = null;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS metas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL DEFAULT 1,
                nombre VARCHAR(150) NOT NULL,
                monto_objetivo DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                monto_actual DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                fecha_limite DATE NULL,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $db->exec("CREATE TABLE IF NOT EXISTS abonos_metas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                meta_id INT NOT NULL,
                monto DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(meta_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (PDOException $e) {
            die("Error al preparar metas: " . htmlspecialchars($e->getMessage()));
        }

        // CRUD de movimientos/abonos de metas.
        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'eliminar_movimiento') {
            $id = (int)$_GET['id'];
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("SELECT a.monto FROM abonos_metas a INNER JOIN metas m ON m.id = a.meta_id WHERE a.id = ? AND m.usuario_id = ? FOR UPDATE");
                $stmt->execute([$id, $usuario_id]);
                $mov = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($mov) {
                    $stmt = $db->prepare("SELECT meta_id FROM abonos_metas WHERE id = ?");
                    $stmt->execute([$id]);
                    $metaId = (int)$stmt->fetchColumn();
                    $stmt = $db->prepare("UPDATE metas SET monto_actual = GREATEST(0, monto_actual - ?) WHERE id = ? AND usuario_id = ?");
                    $stmt->execute([(float)$mov['monto'], $metaId, $usuario_id]);
                    $stmt = $db->prepare("DELETE FROM abonos_metas WHERE id = ?");
                    $stmt->execute([$id]);
                }
                $db->commit();
            } catch (Exception $e) { if ($db->inTransaction()) $db->rollBack(); }
            header("Location: index.php?url=metas"); exit;
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'editar_movimiento') {
            $stmt = $db->prepare("SELECT a.*, m.nombre AS meta_nombre FROM abonos_metas a INNER JOIN metas m ON m.id = a.meta_id WHERE a.id = ? AND m.usuario_id = ? LIMIT 1");
            $stmt->execute([(int)$_GET['id'], $usuario_id]);
            $movimientoEditar = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'crear_meta' || (isset($_POST['nombre']) && !isset($_POST['meta_id']))) {
                $nombre = trim($_POST['nombre'] ?? '');
                $objetivo = (float)($_POST['monto_objetivo'] ?? 0);
                $inicial = (float)($_POST['monto_inicial'] ?? 0);
                $fecha = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
                if ($nombre !== '' && $objetivo > 0) {
                    $stmt = $db->prepare("INSERT INTO metas (usuario_id, nombre, monto_objetivo, monto_actual, fecha_limite) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$usuario_id, $nombre, $objetivo, max(0, $inicial), $fecha]);
                    $metaId = (int)$db->lastInsertId();
                    if ($inicial > 0) {
                        $stmt = $db->prepare("INSERT INTO abonos_metas (meta_id, monto) VALUES (?, ?)");
                        $stmt->execute([$metaId, $inicial]);
                    }
                }
                header("Location: index.php?url=metas"); exit;
            }

            if ($action === 'abono') {
                $metaId = (int)($_POST['meta_id'] ?? 0);
                $monto = (float)($_POST['monto'] ?? 0);
                if ($metaId > 0 && $monto > 0) {
                    $stmt = $db->prepare("UPDATE metas SET monto_actual = monto_actual + ? WHERE id = ? AND usuario_id = ?");
                    $stmt->execute([$monto, $metaId, $usuario_id]);
                    if ($stmt->rowCount()) {
                        $stmt = $db->prepare("INSERT INTO abonos_metas (meta_id, monto) VALUES (?, ?)");
                        $stmt->execute([$metaId, $monto]);
                    }
                }
                header("Location: index.php?url=metas"); exit;
            }

            if ($action === 'actualizar_movimiento') {
                $movId = (int)($_POST['movimiento_id'] ?? 0);
                $metaId = (int)($_POST['meta_id'] ?? 0);
                $nuevoMonto = (float)($_POST['monto'] ?? 0);
                if ($movId > 0 && $metaId > 0 && $nuevoMonto > 0) {
                    try {
                        $db->beginTransaction();
                        $stmt = $db->prepare("SELECT a.monto, a.meta_id FROM abonos_metas a INNER JOIN metas m ON m.id = a.meta_id WHERE a.id = ? AND m.usuario_id = ? FOR UPDATE");
                        $stmt->execute([$movId, $usuario_id]);
                        $old = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stmt = $db->prepare("SELECT id FROM metas WHERE id = ? AND usuario_id = ? FOR UPDATE");
                        $stmt->execute([$metaId, $usuario_id]);
                        $metaExists = $stmt->fetchColumn();
                        if ($old && $metaExists) {
                            $oldMeta = (int)$old['meta_id'];
                            $oldMonto = (float)$old['monto'];
                            if ($oldMeta === $metaId) {
                                $stmt = $db->prepare("UPDATE metas SET monto_actual = GREATEST(0, monto_actual + ? - ?) WHERE id = ? AND usuario_id = ?");
                                $stmt->execute([$nuevoMonto, $oldMonto, $metaId, $usuario_id]);
                            } else {
                                $stmt = $db->prepare("UPDATE metas SET monto_actual = GREATEST(0, monto_actual - ?) WHERE id = ? AND usuario_id = ?");
                                $stmt->execute([$oldMonto, $oldMeta, $usuario_id]);
                                $stmt = $db->prepare("UPDATE metas SET monto_actual = monto_actual + ? WHERE id = ? AND usuario_id = ?");
                                $stmt->execute([$nuevoMonto, $metaId, $usuario_id]);
                            }
                            $stmt = $db->prepare("UPDATE abonos_metas SET meta_id = ?, monto = ? WHERE id = ?");
                            $stmt->execute([$metaId, $nuevoMonto, $movId]);
                        }
                        $db->commit();
                    } catch (Exception $e) { if ($db->inTransaction()) $db->rollBack(); }
                }
                header("Location: index.php?url=metas"); exit;
            }
        }

        $stmt = $db->prepare("SELECT * FROM metas WHERE usuario_id = ? ORDER BY id DESC");
        $stmt->execute([$usuario_id]);
        $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalAhorrado = 0; $objetivoGlobal = 0;
        foreach ($metas as $m) { $totalAhorrado += (float)($m['monto_actual'] ?? 0); $objetivoGlobal += (float)($m['monto_objetivo'] ?? 0); }
        $progresoGeneral = $objetivoGlobal > 0 ? min(100, ($totalAhorrado / $objetivoGlobal) * 100) : 0;

        $stmt = $db->prepare("SELECT a.*, m.nombre AS meta_nombre FROM abonos_metas a INNER JOIN metas m ON a.meta_id = m.id WHERE m.usuario_id = ? ORDER BY a.fecha DESC, a.id DESC LIMIT 20");
        $stmt->execute([$usuario_id]);
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/metas/index.php';
    }
}

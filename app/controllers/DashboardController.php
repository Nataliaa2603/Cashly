<?php
class DashboardController {
    private function obtenerConexion() {
        require_once __DIR__ . '/../../config/database.php';
        return Database::getConnection();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $db = $this->obtenerConexion();
        $usuario_id = $_SESSION['usuario_id'] ?? 1;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS transacciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL DEFAULT 1,
                tipo VARCHAR(50) NOT NULL,
                monto DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                categoria VARCHAR(150) NOT NULL,
                metodo_pago VARCHAR(100) NOT NULL,
                fecha DATE NOT NULL,
                descripcion TEXT NULL,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(usuario_id), INDEX(fecha)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (PDOException $e) {
            die("Error preparando movimientos: " . htmlspecialchars($e->getMessage()));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'guardar';
            $tipo = trim($_POST['tipo'] ?? 'Gasto');
            $monto = (float)($_POST['monto'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'Otros');
            $metodo_pago = trim($_POST['metodo_pago'] ?? 'Efectivo');
            $fecha = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($monto > 0) {
                if ($action === 'editar' && !empty($_POST['id'])) {
                    $stmt = $db->prepare("UPDATE transacciones SET tipo=?, monto=?, categoria=?, metodo_pago=?, fecha=?, descripcion=? WHERE id=? AND usuario_id=?");
                    $stmt->execute([$tipo, $monto, $categoria, $metodo_pago, $fecha, $descripcion, (int)$_POST['id'], $usuario_id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO transacciones (usuario_id,tipo,monto,categoria,metodo_pago,fecha,descripcion) VALUES (?,?,?,?,?,?,?)");
                    $stmt->execute([$usuario_id, $tipo, $monto, $categoria, $metodo_pago, $fecha, $descripcion]);
                }
            }
            header("Location: index.php?url=dashboard"); exit;
        }

        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'eliminar') {
            $stmt = $db->prepare("DELETE FROM transacciones WHERE id=? AND usuario_id=?");
            $stmt->execute([(int)$_GET['id'], $usuario_id]);
            header("Location: index.php?url=dashboard"); exit;
        }

        $balanceTotal=0;$ingresosTotales=0;$gastosTotales=0;$transacciones=[];$catGastosEtiquetas=[];$cantGastos=[];$catIngresosEtiquetas=[];$cantIngresos=[];$fechasComparativa=[];$datosIngresosLinea=[];$datosGastosLinea=[];
        $stmt = $db->prepare("SELECT COALESCE(SUM(CASE WHEN tipo='Ingreso' THEN monto ELSE 0 END),0) ingresos, COALESCE(SUM(CASE WHEN tipo='Gasto' THEN monto ELSE 0 END),0) gastos FROM transacciones WHERE usuario_id=?");
        $stmt->execute([$usuario_id]); $tot=$stmt->fetch();
        $ingresosTotales=(float)$tot['ingresos']; $gastosTotales=(float)$tot['gastos']; $balanceTotal=$ingresosTotales-$gastosTotales;

        $stmt=$db->prepare("SELECT * FROM transacciones WHERE usuario_id=? ORDER BY fecha DESC,id DESC LIMIT 20");$stmt->execute([$usuario_id]);$transacciones=$stmt->fetchAll();
        $stmt=$db->prepare("SELECT categoria,SUM(monto) total FROM transacciones WHERE usuario_id=? AND tipo='Gasto' GROUP BY categoria HAVING total>0 ORDER BY total DESC");$stmt->execute([$usuario_id]);foreach($stmt->fetchAll() as $r){$catGastosEtiquetas[]=$r['categoria'];$cantGastos[]=(float)$r['total'];}
        $stmt=$db->prepare("SELECT categoria,SUM(monto) total FROM transacciones WHERE usuario_id=? AND tipo='Ingreso' GROUP BY categoria HAVING total>0 ORDER BY total DESC");$stmt->execute([$usuario_id]);foreach($stmt->fetchAll() as $r){$catIngresosEtiquetas[]=$r['categoria'];$cantIngresos[]=(float)$r['total'];}
        $stmt=$db->prepare("SELECT fecha,SUM(CASE WHEN tipo='Ingreso' THEN monto ELSE 0 END) ingreso,SUM(CASE WHEN tipo='Gasto' THEN monto ELSE 0 END) gasto FROM transacciones WHERE usuario_id=? GROUP BY fecha ORDER BY fecha ASC LIMIT 15");$stmt->execute([$usuario_id]);foreach($stmt->fetchAll() as $r){$fechasComparativa[]=date('d/m',strtotime($r['fecha']));$datosIngresosLinea[]=(float)$r['ingreso'];$datosGastosLinea[]=(float)$r['gasto'];}

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}

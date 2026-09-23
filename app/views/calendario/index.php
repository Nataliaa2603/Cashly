<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashly - Calendario Financiero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0EA5E9;
            --primary-hover: #0284c7;
            --bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        .sidebar {
            width: 250px;
            background: var(--card-bg);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand { font-size: 1.5rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; margin-bottom: 30px; padding-left: 10px; }
        .nav-list { list-style: none; }
        .nav-item { margin-bottom: 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-muted); text-decoration: none; font-weight: 600; border-radius: 8px; transition: all 0.2s; }
        .nav-link:hover, .nav-link.active { background: #F1F5F9; color: var(--primary); }

        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header { margin-bottom: 25px; }

        .card { background: var(--card-bg); padding: 24px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .calendar-day-head { text-align: center; font-weight: 700; color: var(--text-muted); padding: 10px; font-size: 0.85rem; background: #F1F5F9; border-radius: 6px; }
        .calendar-day { background: #FFF; border: 1px solid var(--border); border-radius: 8px; min-height: 90px; padding: 8px; font-size: 0.85rem; position: relative; }
        .calendar-day.empty { background: #F8FAFC; border: none; }
        .day-num { font-weight: 700; margin-bottom: 5px; color: var(--text-main); }

        .badge-gasto { background: #FEE2E2; color: #DC2626; padding: 2px 5px; border-radius: 4px; font-size: 0.72rem; font-weight: 700; margin-top: 4px; display: block; }
        .badge-recordatorio { background: #FEF3C7; color: #D97706; padding: 2px 5px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; margin-top: 2px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.95rem; outline: none; }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 15px; }
        .btn-submit:hover { background: var(--primary-hover); }

        .btn-logout { color: var(--danger); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; padding: 10px 16px; }
    </style>
</head>
<body>

    <!-- Menú Lateral -->
    <aside class="sidebar">
        <div>
            <div class="brand"><i class="fa-solid fa-wallet"></i> Cashly</div>
            <ul class="nav-list">
                <li class="nav-item"><a href="index.php?url=dashboard" class="nav-link"><i class="fa-solid fa-chart-pie"></i> Panel Principal</a></li>
                <li class="nav-item"><a href="index.php?url=clientes" class="nav-link"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li class="nav-item"><a href="index.php?url=presupuestos" class="nav-link"><i class="fa-solid fa-bullseye"></i> Presupuestos</a></li>
                <li class="nav-item"><a href="index.php?url=metas" class="nav-link"><i class="fa-solid fa-piggy-bank"></i> Metas de Ahorro</a></li>
                <li class="nav-item"><a href="index.php?url=calendario" class="nav-link active"><i class="fa-solid fa-calendar-days"></i> Calendario</a></li>
            </ul>
        </div>
        <div>
            <a href="index.php?url=logout" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="main-content">
        <header class="header">
            <h2>Calendario Financiero 📅</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Visualiza tus gastos diarios y programa pagos obligatorios</p>
        </header>

        <!-- Formulario -->
        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-bell" style="color: var(--warning);"></i> Programar Recordatorio / Pago Recurrente
            </div>
            <form action="index.php?url=guardar-recordatorio" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Compromiso / Servicio *</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Arriendo, Netflix, Servicio Agua" required>
                    </div>
                    <div class="form-group">
                        <label>Monto a Pagar ($ COP) *</label>
                        <input type="number" step="0.01" name="monto" class="form-control" placeholder="Ej: 120000" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Vencimiento *</label>
                        <input type="date" name="fecha_pago" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-control">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-plus-circle"></i> Agregar Recordatorio
                </button>
            </form>
        </div>

        <!-- Renderizado del Calendario -->
        <?php
            $primerDiaMes = mktime(0, 0, 0, $mes, 1, $anio);
            $diasEnMes = date('t', $primerDiaMes);
            $diaSemanaInicio = date('N', $primerDiaMes) - 1; 
            $nombreMeses = [1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        ?>

        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-calendar" style="color: var(--primary);"></i> <?= $nombreMeses[$mes] ?> <?= $anio ?>
            </div>

            <div class="calendar-grid">
                <div class="calendar-day-head">Lun</div>
                <div class="calendar-day-head">Mar</div>
                <div class="calendar-day-head">Mié</div>
                <div class="calendar-day-head">Jue</div>
                <div class="calendar-day-head">Vie</div>
                <div class="calendar-day-head">Sáb</div>
                <div class="calendar-day-head">Dom</div>

                <!-- Celdas vacías iniciales -->
                <?php for ($i = 0; $i < $diaSemanaInicio; $i++): ?>
                    <div class="calendar-day empty"></div>
                <?php endfor; ?>

                <!-- Días del mes -->
                <?php for ($dia = 1; $dia <= $diasEnMes; $dia++): ?>
                    <?php 
                        $fechaActual = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
                        $tieneGasto = isset($mapaGastos[$fechaActual]);
                        $tieneRecordatorio = isset($mapaRecordatorios[$fechaActual]);
                    ?>
                    <div class="calendar-day">
                        <div class="day-num"><?= $dia ?></div>

                        <?php if ($tieneGasto): ?>
                            <span class="badge-gasto" title="Gastado este día">
                                -$<?= number_format($mapaGastos[$fechaActual], 0) ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($tieneRecordatorio): ?>
                            <?php foreach ($mapaRecordatorios[$fechaActual] as $rec): ?>
                                <span class="badge-recordatorio" title="<?= htmlspecialchars($rec['titulo']) ?>">
                                    🔔 <?= htmlspecialchars($rec['titulo']) ?> ($<?= number_format($rec['monto'], 0) ?>)
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </main>

</body>
</html>
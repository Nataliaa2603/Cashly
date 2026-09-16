<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashly - Calendario de Pagos</title>
    <link rel="stylesheet" href="/cashly/public/css/variables.css">
    <link rel="stylesheet" href="/cashly/public/css/dashboard.css">
    <script src="/cashly/public/js/theme.js"></script>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand"><h2>Cashly 💸</h2></div>
            <ul class="sidebar-nav">
                <li><a href="/cashly/public/index.php?url=dashboard">📊 Panel Principal</a></li>
                <li><a href="/cashly/public/index.php?url=presupuestos">🎯 Presupuestos</a></li>
                <li><a href="/cashly/public/index.php?url=metas">🐖 Metas de Ahorro</a></li>
                <li><a href="/cashly/public/index.php?url=calendario" class="active">📅 Calendario de Pagos</a></li>
            </ul>
        </div>
        <div>
            <button onclick="toggleTheme()" class="btn btn-secondary" style="width: 100%;">🌓 Cambiar Tema</button>
        </div>
    </aside>

    <main class="main-content">
        <header class="navbar">
            <h1>📅 Calendario y Vencimiento de Servicios</h1>
            <a href="/cashly/public/index.php?url=exportar-excel" class="btn" style="background: var(--color-success); text-decoration: none;">
                📊 Descargar Reporte CSV/Excel
            </a>
        </header>

        <div class="card" style="margin-bottom: 25px;">
            <h3>Recordatorio de Facturas Próximas (Colombia)</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Consulta las fechas clave para evitar recargos en tus servicios públicos, tarjetas de crédito y suscripciones.
            </p>
        </div>

        <div class="cards-grid">
            <div class="card" style="border-left: 4px solid #EF4444;">
                <span class="badge-pay badge-daviplata">Pendiente</span>
                <h3 style="margin-top: 10px; color: var(--text-primary);">Servicios Públicos (EPM / Enel / Vanti)</h3>
                <p style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 5px 0;">$ 185.000 COP</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">Vence: 18 de este mes</p>
            </div>

            <div class="card" style="border-left: 4px solid #F59E0B;">
                <span class="badge-pay badge-nequi">Próximo</span>
                <h3 style="margin-top: 10px; color: var(--text-primary);">Tarjeta de Crédito (Cuota Mes)</h3>
                <p style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 5px 0;">$ 320.000 COP</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">Vence: 25 de este mes</p>
            </div>

            <div class="card" style="border-left: 4px solid #10B981;">
                <span class="badge-pay badge-pse">Al Día</span>
                <h3 style="margin-top: 10px; color: var(--text-primary);">Arriendo / Administración</h3>
                <p style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 5px 0;">$ 1.200.000 COP</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">Pagado el 05 de este mes</p>
            </div>
        </div>
    </main>
</div>

</body>
</html>
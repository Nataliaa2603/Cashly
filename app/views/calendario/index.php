<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - Cashly</title>
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
    <div class="app-container">
        <!-- Menú Lateral Azul -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Cashly ✨</h2>
            </div>
            <nav class="menu">
                <a href="index.php?url=dashboard" class="nav-link">
                    <i>🏠</i> <span>Inicio</span>
                </a>
                <a href="index.php?url=presupuestos" class="nav-link">
                    <i>📊</i> <span>Presupuestos</span>
                </a>
                <a href="index.php?url=metas" class="nav-link">
                    <i>🎯</i> <span>Metas de Ahorro</span>
                </a>
                <a href="index.php?url=calendario" class="nav-link active">
                    <i>📅</i> <span>Calendario</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="tip-card">
                    <p>Consejo del día 💙</p>
                </div>
            </div>
        </aside>

        <!-- Contenido Principal del Calendario -->
        <main class="content">
            <header class="page-header">
                <h1>Calendario Financiero 📅</h1>
                <p>Revisa tus gastos programados y recordatorios del mes</p>
            </header>

            <div class="calendar-container">
                <div class="calendar-header">
                    <h2><?php echo date('F Y'); ?></h2>
                </div>
                
                <div class="calendar-grid">
                    <!-- Vista simplificada del calendario -->
                    <p>Aquí se cargan los gastos y recordatorios activos del mes.</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
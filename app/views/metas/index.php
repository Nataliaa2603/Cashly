<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas de Ahorro - Cashly</title>
    <!-- Vinculación correcta del CSS desde public/ -->
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Menú Lateral Azul (Sidebar) -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Cashly ✨</h2>
            </div>
            
            <nav class="menu">
                <a href="index.php?url=dashboard" class="nav-link">
                    <i class="fas fa-home"></i> <span>Inicio</span>
                </a>
                <a href="index.php?url=presupuestos" class="nav-link">
                    <i class="fas fa-chart-bar"></i> <span>Presupuestos</span>
                </a>
                <a href="index.php?url=metas" class="nav-link active">
                    <i class="fas fa-bullseye"></i> <span>Metas de Ahorro</span>
                </a>
                <a href="index.php?url=calendario" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> <span>Calendario</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="tip-card">
                    <p>Consejo del día 💙</p>
                </div>
            </div>
        </aside>

        <!-- Área de Contenido Principal -->
        <main class="content">
            <header class="page-header">
                <h1>Metas de Ahorro 📝</h1>
                <p>Planea tus objetivos de inversión a futuro</p>
            </header>

            <!-- Tarjeta de Resumen de Ahorro -->
            <div class="cards-grid">
                <div class="card summary-card">
                    <h3>Ahorrado Total</h3>
                    <p class="total-amount">$1.000 COP</p>
                </div>
            </div>

            <!-- Sección de Objetivos Activos -->
            <section class="active-goals">
                <h2>Tus Objetivos Activos ↗</h2>
                <div class="goal-item card">
                    <div class="goal-header">
                        <h3>✈️ Graduacion</h3>
                    </div>
                    <p class="target">Objetivo: $7.000.000 COP</p>
                    <p class="current-amount">$1.000 COP</p>
                    <p class="remaining">Faltan: $6.999.000 COP</p>
                    <div class="progress-container">
                        <label>Progreso</label>
                        <div class="progress-bar">
                            <div class="progress" style="width: 1%;"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Formulario para Crear Nuevas Metas -->
            <section class="form-section card">
                <h2>+ Crear Nueva Meta</h2>
                <form action="index.php?url=metas/crear" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre de la Meta</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej: Viaje a Japón, Fondo de emergencia" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="monto_objetivo">Monto Objetivo (COP)</label>
                            <input type="number" id="monto_objetivo" name="monto_objetivo" placeholder="Ej: 3000000" required>
                        </div>
                        <div class="form-group">
                            <label for="monto_inicial">Monto Inicial (COP)</label>
                            <input type="number" id="monto_inicial" name="monto_inicial" placeholder="Ej: 500000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha_limite">Fecha Límite</label>
                        <input type="date" id="fecha_limite" name="fecha_limite" required>
                    </div>

                    <button type="submit" class="btn-submit">💾 Crear Meta</button>
                </form>
            </section>

            <!-- Historial de Movimientos -->
            <section class="movements-section card">
                <h2>📜 Últimos Movimientos ↗</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Meta</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se renderizan dinámicamente con PHP -->
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
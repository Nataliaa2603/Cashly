document.addEventListener("DOMContentLoaded", function () {
    const canvasElement = document.getElementById("gastosChart");
    if (!canvasElement) return;

    // Obtener los datos JSON inyectados desde el controlador de PHP
    const rawData = canvasElement.dataset.chartData;
    const dataGastos = JSON.parse(rawData || "[]");

    if (dataGastos.length === 0) {
        canvasElement.parentElement.innerHTML += "<p style='text-align:center; color: var(--text-secondary); margin-top:20px;'>No hay gastos registrados este mes.</p>";
        return;
    }

    const labels = dataGastos.map(item => item.nombre);
    const totals = dataGastos.map(item => parseFloat(item.total));
    const colors = dataGastos.map(item => item.color || '#0EA5E9');

    // Detectar tema actual
    const getTextColor = () => {
        const theme = document.documentElement.getAttribute('data-theme');
        return theme === 'dark' ? '#F8FAFC' : '#1E293B';
    };

    // Crear la gráfica con Chart.js
    const chartInstance = new Chart(canvasElement, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [{
                data: totals,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: getComputedStyle(document.body).getPropertyValue('--bg-surface').trim()
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: getTextColor(),
                        font: { family: 'Segoe UI', size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let value = context.raw || 0;
                            return ' ' + context.label + ': $' + new Intl.NumberFormat('es-CO').format(value) + ' COP';
                        }
                    }
                }
            }
        }
    });

    // Escuchar el evento de cambio de tema para actualizar la leyenda de la gráfica
    window.addEventListener('themeChanged', function () {
        if (chartInstance) {
            chartInstance.options.plugins.legend.labels.color = getTextColor();
            chartInstance.data.datasets[0].borderColor = getComputedStyle(document.body).getPropertyValue('--bg-surface').trim();
            chartInstance.update();
        }
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('chartData');
    if (!el) return;

    const d = el.dataset;

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['À faire', 'En cours', 'Révision', 'Terminé'],
            datasets: [{
                data: [d.todo, d.inProgress, d.review, d.done],
                backgroundColor: ['#9ca3af', '#3b82f6', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '65%',
            plugins: { legend: { display: false } },
            responsive: false,
        }
    });

    new Chart(document.getElementById('priorityChart'), {
        type: 'bar',
        data: {
            labels: ['Urgente', 'Haute', 'Moyenne', 'Faible'],
            datasets: [{
                label: 'Tâches',
                data: [d.urgent, d.high, d.medium, d.low],
                backgroundColor: ['#ef4444', '#f97316', '#3b82f6', '#10b981'],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
});

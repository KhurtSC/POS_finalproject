document.addEventListener('DOMContentLoaded', () => {
    const salesChart = document.getElementById('salesLineChart');
    const productsChart = document.getElementById('topProductsChart');

    const chartData = window.__chartData || { labels: [], revenue: [], counts: [] };

    // Detect light/dark mode and pick appropriate colors
    const isDark = document.documentElement.classList.contains('dark');
    const tickColor   = isDark ? '#94a3b8' : '#64748b';
    const gridColor   = isDark ? '#1e293b' : '#f1f5f9';
    const barColor    = isDark ? '#14b8a6' : '#0f172a';

    if (salesChart) {
        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: chartData.revenue,
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.12)',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { color: tickColor },
                        grid:  { color: gridColor },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: tickColor,
                            callback: (value) => '₱' + Number(value).toLocaleString(),
                        },
                        grid: { color: gridColor },
                    },
                },
            },
        });
    }

    if (productsChart) {
        new Chart(productsChart, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Transactions',
                    data: chartData.counts,
                    backgroundColor: barColor,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { color: tickColor },
                        grid:  { color: gridColor },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: tickColor, stepSize: 1 },
                        grid: { color: gridColor },
                    },
                },
            },
        });
    }
});
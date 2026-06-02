document.addEventListener('DOMContentLoaded', () => {
    const salesChart    = document.getElementById('salesLineChart');
    const productsChart = document.getElementById('topProductsChart');

    // Use real data injected by DashboardController via window.__chartData.
    // Falls back to empty arrays so the canvas renders without errors even if
    // the controller data is missing (e.g., fresh install with no sales yet).
    const chartData = window.__chartData || { labels: [], revenue: [], counts: [] };

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
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => '₱' + Number(value).toLocaleString(),
                        },
                    },
                },
            },
        });
    }

    if (productsChart) {
        // Top products chart is driven by the Reports page, not the dashboard.
        // The dashboard passes 7-day sales data only; show daily transaction counts here.
        new Chart(productsChart, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Transactions',
                    data: chartData.counts,
                    backgroundColor: '#0f172a',
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            },
        });
    }
});
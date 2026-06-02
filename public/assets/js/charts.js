document.addEventListener('DOMContentLoaded', () => {
    const salesChart = document.getElementById('salesLineChart');
    const productsChart = document.getElementById('topProductsChart');

    if (salesChart) {
        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales',
                    data: [42, 58, 49, 64, 72, 91, 84],
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.12)',
                    fill: true,
                    tension: 0.35
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }

    if (productsChart) {
        new Chart(productsChart, {
            type: 'bar',
            data: {
                labels: ['Iced Coffee', 'White Mocha', 'Club Sandwich', 'Truffle Pasta', 'Chocolate Mousse'],
                datasets: [{ label: 'Units', data: [120, 98, 76, 54, 38], backgroundColor: '#0f172a' }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }
});

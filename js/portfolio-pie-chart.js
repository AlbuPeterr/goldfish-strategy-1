async function loadPieChart() {
  const response = await fetch('../goldfish-strategy/api/get-portfolio-breakdown.php');
  const data = await response.json();

  if (data.breakdown) {
    const ctx = document.getElementById('pieChart').getContext('2d');
    const labels = Object.keys(data.breakdown).map(coin => coin.toUpperCase());
    const values = Object.values(data.breakdown);

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: [
            '#F7931A', // BTC
            '#3C3C3D', // ETH
            '#00FFA3', // SOL
            '#FF007A', // SUI
            '#2775CA', // USDC
          ],
          borderColor: '#010E1B',
          borderWidth: 2,
        }]
      },
      options: {
        plugins: {
          legend: {
            labels: {
              color: '#fff',
              font: {
                size: 14
              }
            }
          }
        }
      }
    });
  } else {
    console.error('Hiba a szerver válaszában:', data.error || data);
  }
}

loadPieChart();

async function loadPortfolio() {
  const res = await fetch('../goldfish-strategy/api/get-portfolio.php');
  const text = await res.text();
  console.log("Válasz:", text);

  let data;
  try {
    data = JSON.parse(text);
  } catch (e) {
    console.error("Nem sikerült JSON-né alakítani:", e);
    return alert("Hiba történt az adatok lekérdezésekor.");
  }

  if (!Array.isArray(data)) {
    console.error("A válasz nem tömb:", data);
    return alert("Nem megfelelő formátumú válasz érkezett.");
  }

  const table = document.getElementById('portfolioTable');
  table.innerHTML = `
    <tr>
      <th data-translate="coin">Coin</th>
      <th data-translate="amount">Amount</th>
      <th data-translate="avgBuyPrice">Average Buy Price (USDC)</th>
      <th data-translate="currentPrice">Current Price (USDC)</th>
      <th data-translate="value">Value (USDC)</th>
      <th data-translate="profit">Profit (USDC)</th>
      <th data-translate="profitPercent">Profit (%)</th>
    </tr>
  `;

  let total = 0;

  data.forEach(row => {
    const profitColor = row.profit >= 0 ? 'green' : 'red';
    table.innerHTML += `
      <tr>
        <td>${row.coin.toUpperCase()}</td>
        <td>${parseFloat(row.amount).toFixed(4)}</td>
        <td>$${parseFloat(row.avg_price).toFixed(2)}</td>
        <td>$${parseFloat(row.current_price).toFixed(2)}</td>
        <td>$${parseFloat(row.current_value).toFixed(2)}</td>
        <td style="color: ${profitColor};">$${parseFloat(row.profit).toFixed(2)}</td>
        <td style="color: ${profitColor};">${parseFloat(row.profit_percent).toFixed(2)}%</td>
      </tr>
    `;
    total += row.current_value;
  });

  document.getElementById('totalValue').textContent = `$${total.toFixed(2)}`;
}

async function addUSDC() {
  const amount = parseFloat(document.getElementById('usdcAmount').value);
  if (isNaN(amount) || amount <= 0) {
    return alert("Adj meg érvényes összeget.");
  }

  await fetch('../goldfish-strategy/api/add-usdc.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `amount=${amount}`
  });

  loadPortfolio();
}

async function manualBuy() {
  const coin = document.getElementById('manualCoin').value;
  const amount = parseFloat(document.getElementById('manualAmount').value);
  const price = parseFloat(document.getElementById('manualPrice').value);

  if (!coin || isNaN(amount) || amount <= 0 || isNaN(price) || price <= 0) {
    return alert('Adj meg érvényes adatokat.');
  }

  const response = await fetch('../goldfish-strategy/api/manual-trade.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ coin, amount, price, type: "buy" })
  });

  const result = await response.json();

  if (result.success) {
    alert('Sikeres manuális vásárlás!');
    loadPortfolio();
  } else {
    alert(result.error || 'Ismeretlen hiba.');
  }

}

async function manualSell() {
  const coin = document.getElementById('manualSellCoin').value;
  const amount = parseFloat(document.getElementById('manualSellAmount').value);
  const price = parseFloat(document.getElementById('manualSellPrice').value);

  if (!coin || isNaN(amount) || amount <= 0 || isNaN(price) || price <= 0) {
    return alert('Adj meg érvényes adatokat az eladáshoz.');
  }

  const response = await fetch('../goldfish-strategy/api/manual-trade.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ coin, amount, price, type: "sell" })
  });

  const result = await response.json();

  if (result.success) {
    alert('Sikeres eladás!');
    loadPortfolio();
  } else {
    alert(result.error || 'Ismeretlen hiba.');
  }
}

async function loadSummary() {
  try {
    const res = await fetch('api/get-portfolio-summary.php');
    const data = await res.json();

    if (data.error) {
      console.error("Hiba:", data.error);
      return;
    }

    document.getElementById("summaryCurrentValue").textContent = `$${data.current_value}`;
    document.getElementById("summaryInvested").textContent = `$${data.invested}`;

    const profitElem = document.getElementById("summaryProfit");
    const profit = parseFloat(data.profit);
    const profitPercent = parseFloat(data.profit_percent);

    profitElem.textContent = `$${profit.toFixed(2)} (${profitPercent.toFixed(2)}%)`;

    // Szín beállítása: zöld ha pozitív, piros ha negatív, fehér ha nulla
    if (profit > 0) {
      profitElem.style.color = "#4caf50"; // zöld
    } else if (profit < 0) {
      profitElem.style.color = "#f44336"; // piros
    } else {
      profitElem.style.color = "#ffffff"; // fehér
    }
  } catch (err) {
    console.error("Nem sikerült betölteni az összefoglalót", err);
  }
}


loadSummary();


fetch('../goldfish-strategy/api/get-portfolio-history.php')

// Inicializálás
loadPortfolio();

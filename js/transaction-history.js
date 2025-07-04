let currentLang = localStorage.getItem('lang') || 'en';

const typeLabels = {
  hu: { buy: "Vásárlás", sell: "Eladás", usdc_add: "Hozzáadás" },
  en: { buy: "Buy", sell: "Sell", usdc_add: "Add" },
  de: { buy: "Kauf", sell: "Verkauf", usdc_add: "Hinzufügen" },
  fr: { buy: "Achat", sell: "Vente", usdc_add: "Ajout" },
};

const translations = {
  en: { date: "Date", coin: "Coin", type: "Type", amount: "Amount", priceUsdc: "Price (USDC)", totalValueUsdc: "Total Value (USDC)" },
  hu: { date: "Dátum", coin: "Érme", type: "Típus", amount: "Mennyiség", priceUsdc: "Ár (USDC)", totalValueUsdc: "Teljes érték (USDC)" },
  de: { date: "Datum", coin: "Münze", type: "Typ", amount: "Menge", priceUsdc: "Preis (USDC)", totalValueUsdc: "Gesamtwert (USDC)" },
  fr: { date: "Date", coin: "Pièce", type: "Type", amount: "Montant", priceUsdc: "Prix (USDC)", totalValueUsdc: "Valeur Totale (USDC)" },
};

function translatePage() {
  const currentLang = localStorage.getItem('lang') || 'en';
  document.querySelectorAll("[data-translate]").forEach(el => {
    const key = el.getAttribute("data-translate");
    if (translations[currentLang] && translations[currentLang][key]) {
      el.textContent = translations[currentLang][key];
    }
  });
}


document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("transactionsTable");

  function loadTransactions() {
    const currentLang = localStorage.getItem('lang') || 'en';

    fetch("api/get-transactions.php")
      .then(response => response.json())
      .then(data => {
        if (!Array.isArray(data)) {
          table.innerHTML = "<tr><td colspan='6'>Error loading transactions</td></tr>";
          return;
        }

        const headers = `
          <thead>
            <tr>
              <th data-translate="date">Date</th>
              <th data-translate="coin">Coin</th>
              <th data-translate="type">Type</th>
              <th data-translate="amount">Amount</th>
              <th data-translate="priceUsdc">Price (USDC)</th>
              <th data-translate="totalValueUsdc">Total Value (USDC)</th>
            </tr>
          </thead>
          <tbody>
        `;

        const rows = data.map(tx => {
          const date = new Date(tx.date).toISOString().split("T")[0];
          const total = (tx.amount * tx.price).toFixed(2);

          let typeLabel = typeLabels[currentLang]?.[tx.type] || tx.type;

          let color = "";
          switch (tx.type) {
            case "buy":
              color = "green";
              break;
            case "sell":
              color = "red";
              break;
            case "usdc_add":
              color = "#d4a437";
              break;
            default:
              color = "black";
          }

          return `
            <tr>
              <td>${date}</td>
              <td>${tx.coin.toUpperCase()}</td>
              <td style="color: ${color}; font-weight: 600;">${typeLabel}</td>
              <td>${parseFloat(tx.amount).toFixed(4)}</td>
              <td>${parseFloat(tx.price).toFixed(2)}</td>
              <td>${total}</td>
            </tr>
          `;
        }).join("");

        table.innerHTML = headers + rows + "</tbody>";
        translatePage(); // ha van ilyen funkciód a header-ek miatt
      })
      .catch(err => {
        console.error("Error fetching transactions:", err);
        table.innerHTML = "<tr><td colspan='6'>Failed to load transactions.</td></tr>";
      });
  }

  // Betöltéskor meghívjuk:
  loadTransactions();

  // Ha van nyelvváltó gomb, akkor annak eseményénél hívd meg:
  // pl:
  // document.getElementById('langSelector').addEventListener('change', e => {
  //   localStorage.setItem('lang', e.target.value);
  //   loadTransactions();
  // });
});

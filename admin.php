<?php
session_start();
if ($_SESSION['username'] !== 'admin') {
  header('Location: login.html'); // vagy ahol a login van
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Goldfish Strategy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="img/goldfish-small-logo.png">
  <link rel="icon" type="image/x-icon" href="img/goldfish-small-logo.ico">
  <link rel="apple-touch-icon" href="img/goldfish-small-logo.png">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <header>
    <div class="logo-container">
      <a href="admin.php"><img src="img/goldfish-small-logo-transparent.png" alt="Goldfish Strategy Logo" class="logo" draggable="false"></a>
      <a href="admin.php">
        <div class="logo-text">GOLDFISH <span class="logo-text-span">Strategy</span></div>
      </a>
    </div>
    <nav>
      <ul>
        <li><a href="admin-transaction-history.php" data-translate="transactionHistory">Transaction History</a></li>
        <li><a href="admin.php" data-translate="admin">Admin Dashboard</a></li>
        <li><a href="api/logout.php"  data-translate="logout">Logout</a></li>
        <li>
          <div class="lang-switcher">
            <select id="langSwitcher" class="langSwitcher">
              <option value="en">EN</option>
              <option value="hu">HU</option>
              <option value="de">DE</option>
              <option value="fr">FR</option>
            </select>
          </div>
        </li>
      </ul>
    </nav>
  </header>

  <main style="margin-top: 120px;">
    <div class="admin-layout">
      <div class="top-panels action-sections">

        <!-- Add USDC -->
        <div class="tracker-container">
          <h2 data-translate="addUsdcTitle">Add USDC</h2>
          <div class="panel actions">
            <input type="number" id="usdcAmount" data-i18n-placeholder="addUsdcPlaceholder" placeholder="Amount">
            <button onclick="addUSDC()" data-translate="addUsdcButton">Add</button>
          </div>
        </div>

        <!-- Buy Coin -->
        <div class="tracker-container">
          <h2 data-translate="buyCoinTitle">Buy Coin</h2>
          <div class="panel actions">
            <select id="manualCoin">
              <option value="BTC">Bitcoin</option>
              <option value="ETH">Ethereum</option>
              <option value="SOL">Solana</option>
              <option value="SUI">SUI</option>
            </select>
            <input type="number" id="manualAmount" data-i18n-placeholder="buyCoinPlaceholderAmount" placeholder="Amount">
            <input type="number" id="manualPrice" data-i18n-placeholder="buyCoinPlaceholderPrice" placeholder="Buy Price (USDC)">
            <button onclick="manualBuy()" data-translate="buyCoinButton">Buy</button>
          </div>
        </div>

        <!-- Sell Coin -->
        <div class="tracker-container">
          <h2 data-translate="sellCoinTitle">Sell Coin</h2>
          <div class="panel actions">
            <select id="manualSellCoin">
              <option value="BTC">Bitcoin</option>
              <option value="ETH">Ethereum</option>
              <option value="SOL">Solana</option>
              <option value="SUI">SUI</option>
            </select>
            <input type="number" id="manualSellAmount" data-i18n-placeholder="sellCoinPlaceholderAmount" placeholder="Amount">
            <input type="number" id="manualSellPrice" data-i18n-placeholder="sellCoinPlaceholderPrice" placeholder="Sell Price (USDC)">
            <button onclick="manualSell()" data-translate="sellCoinButton">Sell</button>
          </div>
        </div>

      </div>
    </div>


    <hr class="divider">

    <!-- Alsó szekció: Két hasáb -->
    <div class="bottom-panels portfolio-grid">
      <!-- Bal oldal: statisztikák + pie chart -->
      <div class="left-stats">
        <div class="stat-card">
          <h3 data-translate="profitGrowth">Profit (Growth %)</h3>
          <p id="summaryProfit">$0 (0%)</p>
        </div>
        <div class="stat-card">
          <h3 data-translate="netWorth">Net Worth (USDC)</h3>
          <p id="summaryCurrentValue">$0</p>
        </div>
        <div class="stat-card">
          <h3 data-translate="totalInvested">Total Invested (USDC)</h3>
          <p id="summaryInvested">$0</p>
        </div>
        <div class="stat-card">
          <h2 class="chart-title" data-translate="portfolioTitle">Portfolio (%)</h2>
          <canvas id="pieChart"></canvas>
        </div>
        <div class="stat-card">
          <h2 class="chart-title" data-translate="portfolioValue">Portfolio Value</h2>
          <canvas id="portfolioChart"></canvas>
        </div>
      </div>

      <!-- Jobb oldal: táblázat + chart -->
      <div class="portfolio-section wide-section right-stats">
        <h1 data-translate="portfolio">Portfolio</h1>
        <p id="balanceInfo"><span data-translate="totalValue">Total Value</span>: <span id="totalValue">$0</span></p>
        <table id="portfolioTable" ></table>
      </div>
    </div>
  </div>
</main>


  <footer data-translate="footer">
    &copy; 2025 Péter Albu. Personal project – not financial advice.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="js/portfolio-balance-chart.js"></script>
  <script src="js/portfolio-pie-chart.js"></script>
  <script src="js/admin-portfolio.js"></script>
  <script src="js/lang.js"></script>
  <script src="js/scroll-animation.js"></script>
</body>

</html>

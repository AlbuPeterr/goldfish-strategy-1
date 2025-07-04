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
      <div class="portfolio-section wide-section right-stats">
        <h1 data-translate="transactionHistory">Transaction History</h1>
        <table id="transactionsTable" ></table>
      </div>
    </div>
  </div>
</main>


  <footer data-translate="footer">
    &copy; 2025 Péter Albu. Personal project – not financial advice.
  </footer>

  <script src="js/transaction-history.js"></script>
  <script src="js/lang.js"></script>
  <script src="js/scroll-animation.js"></script>
</body>

</html>

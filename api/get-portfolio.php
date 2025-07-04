<?php
header('Content-Type: application/json');

// DB-kapcsolat
$mysqli = new mysqli("localhost", "root", "", "goldfish-strategy");

// Hibakezelés
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB hiba"]);
    exit;
}

// Coin lista az API-hoz (CoinGecko ID-k)
$coinIds = [
    "btc" => "bitcoin",
    "eth" => "ethereum",
    "sol" => "solana",
    "sui" => "sui",
    "usdc" => "usd-coin"
];

// Lekérjük a DB-ből az összes coin-t és mennyiséget
$result = $mysqli->query("SELECT coin, amount FROM portfolio");
$portfolio = [];
$totalValue = 0;

// Árak lekérése CoinGecko API-ról
$apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=" . implode(',', $coinIds) . "&vs_currencies=usd";
$priceData = json_decode(file_get_contents($apiUrl), true);

// Átlagos árak kiszámítása a legutóbbi nullázás utáni tranzakciókból
$avgPrices = [];

foreach ($coinIds as $short => $cgId) {
    if ($short === "usdc") {
        $avgPrices[$short] = 1;
        continue;
    }

    // Lekérjük az adott coin összes tranzakcióját időrendben
    $txQuery = $mysqli->query("
        SELECT * FROM transactions 
        WHERE coin = '$short' 
        ORDER BY date ASC, id ASC
    ");

    $balance = 0;
    $afterResetBuys = [];

    while ($tx = $txQuery->fetch_assoc()) {
        $type = $tx['type'];
        $amount = floatval($tx['amount']);
        $price = floatval($tx['price']);

        if ($type === 'buy') {
            $balance += $amount;
        } else if ($type === 'sell') {
            $balance -= $amount;
        }

        if ($balance <= 0) {
            $afterResetBuys = []; // nullázódott, újraindítjuk a listát
        }

        if ($type === 'buy' && $balance > 0) {
            $afterResetBuys[] = [
                "amount" => $amount,
                "price" => $price
            ];
        }
    }

    // Átlagár kiszámítása a nullázás utáni buy tranzakciókból
    $totalAmount = 0;
    $totalCost = 0;
    foreach ($afterResetBuys as $buy) {
        $totalAmount += $buy["amount"];
        $totalCost += $buy["amount"] * $buy["price"];
    }

    $avgPrices[$short] = $totalAmount > 0 ? $totalCost / $totalAmount : 0;
}


// Portfólió számítás
while ($row = $result->fetch_assoc()) {
    $coin = strtolower($row['coin']);
    $amount = floatval($row['amount']);
    $current_price = isset($coinIds[$coin]) ? floatval($priceData[$coinIds[$coin]]['usd']) : 1;
    $avg_price = $avgPrices[$coin] ?? 0;

    $current_value = $amount * $current_price;
    $profit = ($current_price - $avg_price) * $amount;

    $denominator = $avg_price * $amount;
    $profit_percent = $denominator > 0 ? ($profit / $denominator) * 100 : 0;

    $portfolio[] = [
        "coin" => $coin,
        "amount" => $amount,
        "avg_price" => $avg_price,
        "current_price" => $current_price,
        "current_value" => $current_value,
        "profit" => $profit,
        "profit_percent" => $profit_percent
    ];

    $totalValue += $current_value;
}

// Százalékos arány kiszámítása
foreach ($portfolio as &$row) {
    $row["percentage"] = $totalValue > 0 ? ($row["current_value"] / $totalValue) * 100 : 0;
}

echo json_encode($portfolio);

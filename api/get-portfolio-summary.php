<?php
$conn = new mysqli("localhost", "root", "", "goldfish");
if ($conn->connect_error) {
    die(json_encode(["error" => "Adatbázis hiba"]));
}

$cacheFile = __DIR__ . '/price_cache.json';
if (!file_exists($cacheFile)) {
    echo json_encode(["error" => "Nem található ár cache"]);
    exit;
}
$prices = json_decode(file_get_contents($cacheFile), true);

// Coin mapping
$coinMap = [
    'btc' => 'bitcoin',
    'eth' => 'ethereum',
    'sol' => 'solana',
    'sui' => 'sui',
    'usdc' => 'usd-coin'
];

// Értékek inicializálása
$currentValue = 0;
$invested = 0;

// Lekérdezzük az aktív coinokat
$res = $conn->query("SELECT coin, amount, avg_price FROM portfolio WHERE amount > 0");
while ($row = $res->fetch_assoc()) {
    $short = strtolower($row['coin']);
    if (!isset($coinMap[$short])) continue;

    $price = $prices[$coinMap[$short]]['usd'] ?? null;
    if (!$price) continue;

    $value = $row['amount'] * $price;
    $cost = $row['amount'] * $row['avg_price'];

    $currentValue += $value;
    $invested += $cost;
}

$profit = $currentValue - $invested;
$profitPercent = $invested > 0 ? round(($profit / $invested) * 100, 2) : 0;

echo json_encode([
    "current_value" => round($currentValue, 2),
    "invested" => round($invested, 2),
    "profit" => round($profit, 2),
    "profit_percent" => $profitPercent
]);

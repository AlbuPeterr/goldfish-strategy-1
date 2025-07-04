<?php
$conn = new mysqli("localhost", "root", "", "goldfish-strategy");
if ($conn->connect_error) {
    die(json_encode(["error" => "Adatbázis hiba"]));
}

// Árak beolvasása a cache fájlból
$cacheFile = __DIR__ . '/price_cache.json';
if (!file_exists($cacheFile)) {
    echo json_encode(["error" => "Nem sikerült betölteni az árakat"]);
    exit;
}

$prices = json_decode(file_get_contents($cacheFile), true);

// Coin rövidítések → API név
$coinMap = [
    'btc' => 'bitcoin',
    'eth' => 'ethereum',
    'sol' => 'solana',
    'sui' => 'sui',
    'usdc' => 'usd-coin'
];

// Coin adatok lekérdezése
$result = $conn->query("SELECT coin, amount FROM portfolio WHERE amount > 0");

$distribution = [];
$totalValue = 0;

while ($row = $result->fetch_assoc()) {
    $short = strtolower($row['coin']);
    $amount = (float) $row['amount'];

    if (!isset($coinMap[$short])) continue;

    $coin = $coinMap[$short];

    $price = $prices[$coin]['usd'] ?? null;
    if (!$price) continue;

    $value = $amount * $price;
    $distribution[$short] = $value; // visszaírjuk az eredeti rövid nevet (btc, eth, stb.)
    $totalValue += $value;
}

// Százalékok számítása
$percentages = [];
foreach ($distribution as $coin => $value) {
    $percentages[$coin] = round(($value / $totalValue) * 100, 2);
}

header('Content-Type: application/json');
echo json_encode([
    "total" => round($totalValue, 2),
    "breakdown" => $percentages
]);


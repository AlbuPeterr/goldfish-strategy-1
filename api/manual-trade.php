<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Csak POST kérés engedélyezett."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$coin = strtolower($data["coin"] ?? '');
$amount = floatval($data["amount"] ?? 0);
$price = floatval($data["price"] ?? 0);
$type = strtolower($data["type"] ?? 'buy');
$date = date("Y-m-d");

if (!$coin || $amount <= 0 || $price <= 0 || !in_array($type, ['buy', 'sell'])) {
    http_response_code(400);
    echo json_encode(["error" => "Hiányzó vagy érvénytelen adat."]);
    exit;
}

$totalValue = $amount * $price;

if ($type === "buy") {
    // USDC ellenőrzés
    $stmt = $conn->prepare("SELECT amount FROM portfolio WHERE coin = 'usdc'");
    $stmt->execute();
    $usdc = $stmt->get_result()->fetch_assoc();

    if (!$usdc || $usdc['amount'] < $totalValue) {
        echo json_encode(["error" => "Nincs elég USDC a vásárláshoz."]);
        exit;
    }

    // USDC levonás
    $newUSDC = $usdc['amount'] - $totalValue;
    $stmt = $conn->prepare("UPDATE portfolio SET amount = ?, last_update = ? WHERE coin = 'usdc'");
    $stmt->bind_param("ds", $newUSDC, $date);
    $stmt->execute();

    // Coin frissítés vagy beszúrás
    $stmt = $conn->prepare("SELECT amount, avg_price FROM portfolio WHERE coin = ?");
    $stmt->bind_param("s", $coin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newAmount = $row['amount'] + $amount;
        $newAvgPrice = (($row['amount'] * $row['avg_price']) + $totalValue) / $newAmount;

        $stmt = $conn->prepare("UPDATE portfolio SET amount = ?, avg_price = ?, last_update = ? WHERE coin = ?");
        $stmt->bind_param("ddss", $newAmount, $newAvgPrice, $date, $coin);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO portfolio (coin, amount, avg_price, last_update) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdds", $coin, $amount, $price, $date);
        $stmt->execute();
    }

    // Vásárlás rögzítése a transactions táblába
    $stmt = $conn->prepare("INSERT INTO transactions (coin, amount, price, date, type) VALUES (?, ?, ?, ?, 'buy')");
    $stmt->bind_param("sdds", $coin, $amount, $price, $date);
    $stmt->execute();


    echo json_encode(["success" => true]);
    exit;

} else if ($type === "sell") {
    // Coin ellenőrzés
    $stmt = $conn->prepare("SELECT amount, avg_price FROM portfolio WHERE coin = ?");
    $stmt->bind_param("s", $coin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Nincs ilyen coin a portfólióban."]);
        exit;
    }

    $row = $result->fetch_assoc();
    if ($row['amount'] < $amount) {
        echo json_encode(["error" => "Nincs elég coin az eladáshoz."]);
        exit;
    }

    $newAmount = $row['amount'] - $amount;
    $newAvgPrice = $newAmount == 0 ? 0 : $row['avg_price']; // csak ha nullára fogy, akkor 0

    $stmt = $conn->prepare("UPDATE portfolio SET amount = ?, avg_price = ?, last_update = ? WHERE coin = ?");
    $stmt->bind_param("ddss", $newAmount, $newAvgPrice, $date, $coin);
    $stmt->execute();

    // USDC növelés vagy beszúrás
    $stmt = $conn->prepare("SELECT amount FROM portfolio WHERE coin = 'usdc'");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newUSDC = $row['amount'] + $totalValue;

        $stmt = $conn->prepare("UPDATE portfolio SET amount = ?, last_update = ? WHERE coin = 'usdc'");
        $stmt->bind_param("ds", $newUSDC, $date);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO portfolio (coin, amount, avg_price, last_update) VALUES ('usdc', ?, 1.0, ?)");
        $stmt->bind_param("ds", $totalValue, $date);
        $stmt->execute();
    }

    // Eladás rögzítése a transactions táblába
    $stmt = $conn->prepare("INSERT INTO transactions (coin, amount, price, date, type) VALUES (?, ?, ?, ?, 'sell')");
    $stmt->bind_param("sdds", $coin, $amount, $price, $date);
    $stmt->execute();


    echo json_encode(["success" => true]);
    exit;
}
?>

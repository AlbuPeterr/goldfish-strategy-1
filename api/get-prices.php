<?php
header('Content-Type: application/json');

$cacheFile = 'price_cache.json';
$cacheTime = 60; // másodperc

// Ha van érvényes gyorsítótár, visszaadjuk
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    echo file_get_contents($cacheFile);
    exit;
}

// CoinGecko API hívás curl-lel
$url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,solana,sui,usd-coin&vs_currencies=usd';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FAILONERROR, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Hiba esetén logolás és hibaüzenet
if ($response === false || $httpCode !== 200) {
    error_log("❌ CoinGecko API hiba: $error | HTTP $httpCode");
    http_response_code(500);
    echo json_encode(['error' => 'Árfolyam lekérési hiba']);
    exit;
}

// Ellenőrizzük, hogy valóban JSON-e a válasz
$json = json_decode($response, true);
if (!is_array($json)) {
    error_log("⚠️ Érvénytelen JSON CoinGecko válasz: $response");
    http_response_code(500);
    echo json_encode(['error' => 'Érvénytelen API válasz']);
    exit;
}

// Mentjük a cache-be és visszaadjuk
file_put_contents($cacheFile, $response);
echo $response;

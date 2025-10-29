<?php

header('Content-Type: application/json');

$api_key = '9d61daa31a45123b789d371a17f77a1a4a1d3d063c898626bb8809ab129d82b9';

if (!isset($_GET['courier']) || !isset($_GET['awb'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Parameter Kurir (courier) dan Resi (awb) dibutuhkan.']);
    exit;
}

$courier = $_GET['courier'];
$awb = $_GET['awb'];

$api_url = "https://api.binderbyte.com/v1/track?api_key={$api_key}&courier={$courier}&awb=CM01691288191";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    http_response_code($http_code);
    echo json_encode(['error' => true, 'message' => 'Gagal menghubungi server tracking. Coba lagi nanti.']);
    exit;
}

echo $response;
?>
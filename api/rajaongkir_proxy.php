<?php
header('Content-Type: application/json');

$apiKey = "wmGlygrL0b17be54278529f9qXzDrGkq"; 

$endpoint = $_GET['endpoint'] ?? ''; 

$curl = curl_init();
$options = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => [
        "key: " . $apiKey
    ],
];

switch ($endpoint) {
    case 'province':
        $options[CURLOPT_CUSTOMREQUEST] = "GET";
        curl_setopt($curl, CURLOPT_URL, "https://rajaongkir.komerce.id/api/v1/destination/province");
        break;

    case 'city':
        $provinceId = $_GET['province_id'] ?? '';
        if (empty($provinceId)) {
            echo json_encode(['error' => 'Province ID is required']);
            exit;
        }
        $options[CURLOPT_CUSTOMREQUEST] = "GET";
        curl_setopt($curl, CURLOPT_URL, "https://rajaongkir.komerce.id/api/v1/destination/city/" . $provinceId);
        break;

    case 'cost':
        $options[CURLOPT_CUSTOMREQUEST] = "POST";
        $origin = $_POST['origin'] ?? '';
        $destination = $_POST['destination'] ?? '';
        $weight = $_POST['weight'] ?? '';
        $courier = $_POST['courier'] ?? '';

        if (empty($origin) || empty($destination) || empty($weight) || empty($courier)) {
            echo json_encode(['error' => 'Missing required parameters for cost calculation']);
            exit;
        }

        curl_setopt($curl, CURLOPT_URL, "https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost");
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ]));
        $options[CURLOPT_HTTPHEADER][] = "content-type: application/x-www-form-urlencoded";
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid endpoint']);
        exit;
}

curl_setopt_array($curl, $options);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    http_response_code(500);
    echo json_encode(['error' => "cURL Error #:" . $err]);
} else {
    echo $response;
}
?>
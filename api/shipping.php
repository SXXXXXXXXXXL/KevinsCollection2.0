<?php
header('Content-Type: application/json');
include('../functions/rajaongkir.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, key");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['destination']) && isset($data['courier'])) {
        $origin = '152'; // Kota Jakarta Pusat
        $destination = $data['destination'];
        $weight = $data['weight'] ?? 1000; // Default weight 1kg
        $courier = $data['courier'];
        
        $result = getShippingCosts($origin, $destination, $weight, $courier);
        
        // Log the API response for debugging
        error_log("RajaOngkir Response: " . print_r($result, true));
        
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Missing parameters']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'cities') {
    $cities = getCities();
    
    // Log the API response for debugging
    error_log("RajaOngkir Cities Response: " . print_r($cities, true));
    
    echo json_encode($cities);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>

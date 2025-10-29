<?php
function getShippingCosts($origin, $destination, $weight, $courier) {
    $api_key = 'zaGraYK066a6e18bcb689e646LAuKts9'; // RajaOngkir API Key
    $url = 'https://api.rajaongkir.com/starter/cost';
    
    $data = array(
        'origin' => $origin,      // City ID of origin
        'destination' => $destination, // City ID of destination
        'weight' => $weight,      // Weight in grams
        'courier' => $courier     // Courier code (jne/pos/tiki)
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => array(
            "key: " . $api_key,
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("RajaOngkir API Error: " . $err);
        return array('error' => $err);
    }

    return json_decode($response, true);
}

function getCities() {
    $api_key = 'zaGraYK066a6e18bcb689e646LAuKts9'; // RajaOngkir API Key
    $url = 'https://api.rajaongkir.com/starter/city';
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: " . $api_key
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("RajaOngkir API Error: " . $err);
        return array('error' => $err);
    }

    return json_decode($response, true);
}
?>

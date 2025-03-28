<?php
function binanceAPI($url, $params = [], $method = "GET")
{
    $api_key = getenv("BINANCE_API_KEY");
    $api_secret = getenv("BINANCE_SECRET_KEY");
    $timestamp = round(microtime(true) * 1000);
    $params += ['timestamp' => $timestamp];
    $query_string = http_build_query($params);
    
    // Buat tanda tangan (signature)
    $signature = hash_hmac('sha256', $query_string, $api_secret);
    
    // Konfigurasi cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . "?" . $query_string . "&signature=" . $signature);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-MBX-APIKEY: $api_key",
        "Content-Type: application/json"
    ]);

    if ($method === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
    }

    elseif ($method === "DELETE") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    // Eksekusi request
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Tampilkan hasil
    $result = json_decode($response);
    log_message('info', 'BINANCE RESPONSE: ' .json_encode($response));
    return $result;
}
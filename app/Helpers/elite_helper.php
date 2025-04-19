<?php
function sendRequest($url, $postData = null)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => $postData !== null,
        CURLOPT_POSTFIELDS => $postData ? json_encode($postData) : null,
        CURLOPT_HTTPHEADER => $postData ? ['Content-Type: application/json'] : [],
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

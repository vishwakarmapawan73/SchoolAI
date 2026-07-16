<?php

require_once "config.php";

$to = "919685460692";   // Apna verified WhatsApp number

$data = [
    "messaging_product" => "whatsapp",
    "to" => $to,
    "type" => "text",
    "text" => [
        "body" => "Hello! School AI Bot is working successfully."
    ]
];

$url = "https://graph.facebook.com/v23.0/".PHONE_NUMBER_ID."/messages";

$headers = [
    "Authorization: Bearer ".ACCESS_TOKEN,
    "Content-Type: application/json"
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    echo "<pre>";
    print_r($response);
}

curl_close($ch);

?>
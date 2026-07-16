<?php

include "config.php";
include "school_data.php";

// =========================
// Create Logs Folder
// =========================
$logDir = __DIR__ . "/logs";

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . "/chat.log";

// =========================
// Read WhatsApp Data
// =========================
$data = json_decode(file_get_contents("php://input"), true);

// Save Log
file_put_contents(
    $logFile,
    date("Y-m-d H:i:s") . PHP_EOL .
    json_encode($data, JSON_PRETTY_PRINT) .
    PHP_EOL . str_repeat("-", 50) . PHP_EOL,
    FILE_APPEND
);

// =========================
// Webhook Verification (GET)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (
        isset($_GET['hub_mode']) ||
        isset($_GET['hub.verify_token']) ||
        isset($_GET['hub_challenge'])
    ) {

        $verify_token = $_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? '';
        $challenge = $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? '';

        if ($verify_token === VERIFY_TOKEN) {
            echo $challenge;
            exit;
        }

        http_response_code(403);
        echo "Verification Failed";
        exit;
    }
}

// =========================
// Receive WhatsApp Message
// =========================
if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {

    $msg = strtolower(trim(
        $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body']
    ));

    $from = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];

    $reply = "Sorry, Please contact school office.";

    if (str_contains($msg, "hi"))
        $reply = "Welcome to " . $school['school_name'];

    elseif (str_contains($msg, "admission"))
        $reply = $school['admission'];

    elseif (str_contains($msg, "timing"))
        $reply = "School Timing : " . $school['timing'];

    elseif (str_contains($msg, "fee"))
        $reply = $school['fee'];

    elseif (str_contains($msg, "principal"))
        $reply = "Principal : " . $school['principal'];

    elseif (str_contains($msg, "transport"))
        $reply = $school['transport'];

    elseif (str_contains($msg, "hostel"))
        $reply = $school['hostel'];

    sendMessage($from, $reply);
}

// =========================
// Send WhatsApp Message
// =========================
function sendMessage($to, $message)
{
    $url = "https://graph.facebook.com/v23.0/" . PHONE_NUMBER_ID . "/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => [
            "body" => $message
        ]
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . ACCESS_TOKEN,
        "Content-Type: application/json"
    ]);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    // API Response Log
    global $logFile;

    file_put_contents(
        $logFile,
        "API RESPONSE:\n" . $response . PHP_EOL .
        str_repeat("=", 50) . PHP_EOL,
        FILE_APPEND
    );

    curl_close($ch);
}

http_response_code(200);
echo "OK";

<?php

include "config.php";
include "school_data.php";

$data = json_decode(file_get_contents("php://input"), true);

$logFile = __DIR__ . "chat.log";

if (!is_dir(__DIR__ . "/logs")) {
    mkdir(__DIR__ . "/logs", 0777, true);
}

file_put_contents(
    $logFile,
    date("Y-m-d H:i:s") . PHP_EOL .
    json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL,
    FILE_APPEND
);

if(isset($data['entry'][0]['changes'][0]['value']['messages'][0]))
{

    $msg = strtolower(
        trim(
            $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body']
        )
    );

    $from = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];

    $reply="Sorry, Please contact school office.";

    if(str_contains($msg,"hi"))
        $reply="Welcome to ".$school['school_name'];

    elseif(str_contains($msg,"admission"))
        $reply=$school['admission'];

    elseif(str_contains($msg,"timing"))
        $reply="School Timing : ".$school['timing'];

    elseif(str_contains($msg,"fee"))
        $reply=$school['fee'];

    elseif(str_contains($msg,"principal"))
        $reply="Principal : ".$school['principal'];

    elseif(str_contains($msg,"transport"))
        $reply=$school['transport'];

    elseif(str_contains($msg,"hostel"))
        $reply=$school['hostel'];

    sendMessage($from,$reply);

}

function sendMessage($to, $message) {
    // Ab ye constant use karein
    $url = "https://graph.facebook.com/v23.0/" . PHONE_NUMBER_ID . "/messages";
    
    $data = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => ["body" => $message]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . ACCESS_TOKEN, // Constant ka use
        "Content-Type: application/json"
    ]);

    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    curl_exec($ch);

    curl_close($ch);

}

http_response_code(200);

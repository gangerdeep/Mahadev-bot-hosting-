<?php

$botToken = "8707141410:AAGHrf0XkMTB7aC-2eYLJb697AfJwpBRVB4";
$website = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

$chat_id = $update["message"]["chat"]["id"] ?? null;
$text = $update["message"]["text"] ?? "";

if($text == "/start"){
    file_get_contents($website."/sendMessage?chat_id=".$chat_id."&text=Bot Online ✅");
}

?>

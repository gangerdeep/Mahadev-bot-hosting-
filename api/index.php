<?php

$botToken = "8525855467:AAEgyjEhpB75XMvwmSfXa-HJY-BeeFL1Rag";
$website = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

$chat_id = $update["message"]["chat"]["id"] ?? null;
$text = $update["message"]["text"] ?? "";

if($text == "/start"){
    file_get_contents($website."/sendMessage?chat_id=".$chat_id."&text=Bot Online 🤖✅");
}

?>

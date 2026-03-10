<?php

$botToken = "8525855467:AAE5TJaKvceJ632XygoP_Zq8Jaz4TXL00xM";
$website = "https://api.telegram.org/bot".$botToken;

$update = json_decode(file_get_contents("php://input"), true);

if(isset($update["message"])){

    $chat_id = $update["message"]["chat"]["id"];
    $text = $update["message"]["text"];

    // START COMMAND
    if($text == "/start"){
        sendMessage($chat_id, "Bot Live ✅");
    }

}

function sendMessage($chat_id, $text){
    global $website;
    file_get_contents($website."/sendMessage?chat_id=".$chat_id."&text=".urlencode($text));
}

?>

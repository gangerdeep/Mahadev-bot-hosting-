<?php

$token=$_GET["token"];
$api="https://api.telegram.org/bot".$token;

$update=json_decode(file_get_contents("php://input"),true);

$chat=$update["message"]["chat"]["id"];
$text=$update["message"]["text"];

$json=file_get_contents("data/bots/".$chat.".json");
$bot=json_decode($json,true);

function send($chat,$msg){
global $api;

file_get_contents($api."/sendMessage?chat_id=".$chat."&text=".urlencode($msg));
}

if(isset($bot[$text])){

send($chat,$bot[$text]);

}

?>
